<?php

namespace App\Tests\Security;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class LoginCheckTest extends WebTestCase
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



    }

    public function testAccessToCheckLoginPage()
    {
        $client = static::createClient();

        $router = $this->getRouter();

        $crawler = $client->request(
            "POST",
            $router->generate('fos_user_security_check', ["_locale" => 'ru'])
        );

        dump($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    private function getEntityManager() : EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }

    private function getRouter() : RouterInterface
    {
        return self::$container->get('router');
    }


}