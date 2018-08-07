<?php

namespace App\Tests\Security;

use App\Entity\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;

class RegistrationTest extends WebTestCase
{
    /** @var $entityManager EntityManagerInterface */
    private $entityManager;

    private $testUser = [
        'username' => 'mia_123456',
        'email' => 'mia1234@yahoo.com',
        'password' => '123456'
    ];

    public function setUp()
    {
        /*self::bootKernel();

        $this->entityManager = $this->getEntityManager();

        $user_to_delete = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $this->testUser['username']]);

        if($user_to_delete)
        {
            $this->entityManager->remove($user_to_delete);
            $this->entityManager->flush();
        }*/
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

    public function testRuRegistrationSuccess()
    {
        $client = static::createClient();

        $router = $this->getRouter();

        $crawler = $client->request(
            "GET",
            $router->generate('fos_user_registration_register', ["_locale" => 'ru'])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Зарегистрироваться')->form();

        $form['fos_user_registration_form[username]'] = $this->testUser['username'];
        $form['fos_user_registration_form[email]'] = $this->testUser['email'];
        $form['fos_user_registration_form[plainPassword][first]'] = $this->testUser['password'];
        $form['fos_user_registration_form[plainPassword][second]'] = $this->testUser['password'];

        //мы должны запустить профайлер непосредственно перед запросом, который нам надо профилировать
        $client->enableProfiler();

        $client->submit($form);

        $this->assertContains('Redirecting to /ru/register/check-email', $client->getResponse()->getContent());

        //get link from email and then redirect
        /** @var $mailCollector MessageDataCollector */
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(1, $mailCollector->getMessageCount());

        /** @var $message \Swift_Message */
        $message = $mailCollector->getMessages()[0];

        $message_body = $message->getBody();

        //dump($message_body);

        $confirm_link = $this->getConfirmLinkFromEmail($message_body);

        //dump($confirm_link);

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'Письмо отправлено на адрес '.$this->testUser['email'].'. В нём содержится ссылка, по которой вы можете подтвердить свою регистрацию.',
            $client->getResponse()->getContent()
        );

        $client->request('GET', $confirm_link);

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'Поздравляем '.$this->testUser['username'].', ваш аккаунт подтвержден.',
            $client->getResponse()->getContent()
        );

    }

    public function testEnRegistrationSuccess()
    {
        $client = static::createClient();

        $router = $this->getRouter();

        $crawler = $client->request(
            "GET",
            $router->generate('fos_user_registration_register', ["_locale" => 'en'])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Register')->form();

        $form['fos_user_registration_form[username]'] = $this->testUser['username'];
        $form['fos_user_registration_form[email]'] = $this->testUser['email'];
        $form['fos_user_registration_form[plainPassword][first]'] = $this->testUser['password'];
        $form['fos_user_registration_form[plainPassword][second]'] = $this->testUser['password'];

        //мы должны запустить профайлер непосредственно перед запросом, который нам надо профилировать
        $client->enableProfiler();

        $client->submit($form);

        $this->assertContains('Redirecting to /en/register/check-email', $client->getResponse()->getContent());

        //get link from email and then redirect
        /** @var $mailCollector MessageDataCollector */
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(1, $mailCollector->getMessageCount());

        /** @var $message \Swift_Message */
        $message = $mailCollector->getMessages()[0];

        $message_body = $message->getBody();

        //dump($message_body);

        $confirm_link = $this->getConfirmLinkFromEmail($message_body, 'en');

        //dump($confirm_link);

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'An email has been sent to '.$this->testUser['email'].'. It contains an activation link you must click to activate your account.',
            $client->getResponse()->getContent()
        );

        $client->request('GET', $confirm_link);

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains('Congrats '.$this->testUser['username'].', your account is now activated.', $client->getResponse()->getContent());

    }

    private function getEntityManager() : EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }

    private function getRouter() : RouterInterface
    {
        return self::$container->get('router');
    }

    private function getConfirmLinkFromEmail(string $email_body, string $locale = 'ru') : string
    {
        if($locale === 'ru')
        {
            $start_index = strpos($email_body, 'localhost') + 9;

            $end_index = strpos($email_body, 'Эта') - 2;

            $link = substr($email_body, $start_index,  $end_index - $start_index);

        }elseif ($locale === 'en')
        {
            $start_index = strpos($email_body, 'localhost') + 9;

            $end_index = strpos($email_body, 'This link') - 2;

            $link = substr($email_body, $start_index,  $end_index - $start_index);
        }
        else {
            throw new \Exception('Bad locale ='.$locale);
        }

        return $link;
    }
}