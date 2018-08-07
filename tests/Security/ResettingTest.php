<?php

namespace App\Tests\Security;


use App\Entity\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Symfony\Component\Routing\RouterInterface;

class ResettingTest extends WebTestCase
{
    /** @var $entityManager EntityManagerInterface */
    private $entityManager;

    private $testUser = [
        'username' => 'mia_123456',
        'email' => 'mia1234@yahoo.com',
        'password' => '123456',
        'new_password' => '654321'
    ];

    public function setUp()
    {
        self::bootKernel();

        $this->entityManager = $this->getEntityManager();

        $user_to_save = new User();
        $user_to_save->setUsername($this->testUser['username']);
        $user_to_save->setEmail($this->testUser['email']);
        $user_to_save->setPlainPassword($this->testUser['password']);
        $user_to_save->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user_to_save);
        $this->entityManager->flush();

        $connection = $this->entityManager->getConnection();
        $sql = 'UPDATE fos_user SET enabled=:enabled WHERE username=:name';
        $stmt = $connection->prepare($sql);
        $stmt->execute(['enabled' => 1, 'name' => $this->testUser['username']]);

    }

    public function tearDown()
    {
        //self::bootKernel();

        $this->entityManager = $this->getEntityManager();

        $user_to_delete = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $this->testUser['username']]);

        if($user_to_delete)
        {
            $this->entityManager->remove($user_to_delete);
            $this->entityManager->flush();
        }
    }

    public function testRuResettingsPasswordSuccess()
    {
        $client = static::createClient();

        $router = $this->getRouter();

        $reset_link = $this->sendRuRequestToResetAndGetTokenLink($client, $router);

        //dump($reset_link);

        $this->checkAlreadySendEvent_Ru($client, $router);

        sleep(12);

        $new_reset_link = $this->sendRuRequestToResetAndGetTokenLink($client, $router);

        //dump($new_reset_link);

        $this->assertEquals($reset_link, $new_reset_link);

        $this->setNewPassword($client, $reset_link);

        $this->logout($client, $router);

        $this->checkLogin_Ru($client, $router);
    }

    private function sendRuRequestToResetAndGetTokenLink(Client $client, RouterInterface $router, int $messages_count = 1) : string
    {
        //send request to change password
        $crawler = $client->request(
            "GET",
            $router->generate('fos_user_resetting_request', ["_locale" => 'ru'])
        );

        //send form with username, who need to change password
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Сбросить пароль')->form();

        $form['username'] = $this->testUser['username'];

        $client->enableProfiler();

        $client->submit($form);

        //we on the send email page, before redirect we must get link from email
        $this->assertContains('Redirecting to /ru/resetting/check-email?username='.$this->testUser['username'], $client->getResponse()->getContent());

        //get link from email and then redirect
        /** @var $mailCollector MessageDataCollector */
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertSame($messages_count, $mailCollector->getMessageCount());

        if($messages_count === 0)
        {
            return '';
        }
        /** @var $message \Swift_Message */
        $message = $mailCollector->getMessages()[0];

        $message_body = $message->getBody();

        //dump($message_body);

        $confirm_link = $this->getResetPasswordLinkFromEmail($message_body);

        //dump($confirm_link);

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'Письмо отправлено. Оно содержит ссылку,',
            $client->getResponse()->getContent()
        );

        return $confirm_link;

    }

    private function setNewPassword(Client $client, string $confirm_link) : void
    {
        //we follow link from email
        $crawler = $client->request('GET', $confirm_link);

        //we see form change password
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'Новый пароль',
            $client->getResponse()->getContent()
        );

        $form = $crawler->selectButton('Изменить пароль')->form();
        $form['fos_user_resetting_form[plainPassword][first]'] = $this->testUser['new_password'];
        $form['fos_user_resetting_form[plainPassword][second]'] = $this->testUser['new_password'];

        $client->submit($form);

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'Пароль успешно сброшен.',
            $client->getResponse()->getContent()
        );

        $this->assertContains(
            'Имя пользователя: '.$this->testUser['username'],
            $client->getResponse()->getContent()
        );
    }

    private function checkLogin_Ru(Client $client, RouterInterface $router)
    {
        $crawler = $client->request(
            "GET",
            $router->generate('fos_user_security_login', ["_locale" => 'ru'])
        );

        //send form with username, who need to change password
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Войти')->form();

        $form['_username'] = $this->testUser['username'];
        $form['_password'] = $this->testUser['new_password'];

        $client->submit($form);

        $client->followRedirect();

        $this->assertContains(
            $this->testUser['username'],
            $client->getResponse()->getContent()
        );
    }

    private function logout(Client $client, RouterInterface $router)
    {
        $crawler = $client->request(
            "GET",
            $router->generate('home_ru')
        );

        //send form with username, who need to change password
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Выйти')->link();
        $client->click($link);

        $client->followRedirect();

        $this->assertContains(
            'Учетная запись',
            $client->getResponse()->getContent()
        );
    }

    private function checkAlreadySendEvent_Ru(Client $client, RouterInterface $router, int $messages_count = 0) : void
    {
        //send request to change password
        $crawler = $client->request(
            "GET",
            $router->generate('fos_user_resetting_request', ["_locale" => 'ru'])
        );

        //send form with username, who need to change password
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Сбросить пароль')->form();

        $form['username'] = $this->testUser['username'];

        $client->enableProfiler();

        $client->submit($form);

        //we on the send email page, before redirect we must get link from email
        $this->assertContains('Вы можете запросить новое письмо только через', $client->getResponse()->getContent());

        //get link from email and then redirect
        /** @var $mailCollector MessageDataCollector */
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertSame($messages_count, $mailCollector->getMessageCount());

        /*if($messages_count === 0)
        {
            return '';
        }
        $message = $mailCollector->getMessages()[0];

        $message_body = $message->getBody();

        //dump($message_body);

        $confirm_link = $this->getResetPasswordLinkFromEmail($message_body);

        //dump($confirm_link);

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'Письмо отправлено. Оно содержит ссылку,',
            $client->getResponse()->getContent()
        );

        return $confirm_link;*/
    }

    private function getEntityManager() : EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }

    private function getRouter() : RouterInterface
    {
        return self::$container->get('router');
    }

    private function getResetPasswordLinkFromEmail(string $email_body, string $locale = 'ru') : string
    {
        if($locale === 'ru')
        {
            $start_index = strpos($email_body, 'localhost') + 9;

            $end_index = strpos($email_body, 'С наилучшими') - 2;

            $link = substr($email_body, $start_index,  $end_index - $start_index);

        }elseif ($locale === 'en')
        {
            $start_index = strpos($email_body, 'localhost') + 9;

            $end_index = strpos($email_body, 'Regards') - 2;

            $link = substr($email_body, $start_index,  $end_index - $start_index);
        }
        else {
            throw new \Exception('Bad locale ='.$locale);
        }

        return $link;
    }
}