<?php

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Context\Context;
use App\Entity\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

require_once __DIR__.'/../../vendor/bin/.phpunit/phpunit-6.5/vendor/autoload.php';

class AuthContext extends RawMinkContext implements Context
{
    /** @var $container ContainerInterface */
    private static $container;

    /**
     * @Given create test user with name :name, password :pass and email :email
     */
    public function createTestUserWithNamePasswordAndEmail($name, $pass, $email)
    {
        //create user and enabled his account
        $entityManager = $this->getEntityManager();

        if($entityManager->getRepository(User::class)->findOneBy(['username' => $name]))
            return ;

        $user = new User();
        $user->setUsername($name);
        $user->setEmail($email);
        $user->setPlainPassword($pass);
        $user->setRoles(['ROLE_USER']);

        $entityManager->persist($user);
        $entityManager->flush();

        $connection = $entityManager->getConnection();
        $sql = '
            UPDATE fos_user SET enabled=:enabled WHERE username=:name
        ';
        $stmt = $connection->prepare($sql);
        $stmt->execute(['enabled' => 1, 'name' => $name]);
    }

    /**
     * @Given delete test user with name :name
     */
    public function deleteTestUserWithName($name)
    {
        $entityManager = $this->getEntityManager();
        $user_to_delete = $entityManager->getRepository(User::class)->findOneBy(['username' => $name]);

        $entityManager->remove($user_to_delete);
        $entityManager->flush();

        $this->getSession();
    }

    /**
     * @BeforeSuite
     */
    public static function bootstrapSymfony()
    {
        require_once __DIR__.'/../../vendor/autoload.php';

        $kernel = new \App\Kernel('test', true);
        $kernel->boot();

        self::$container = $kernel->getContainer();
    }

    private function getEntityManager() : EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }
}