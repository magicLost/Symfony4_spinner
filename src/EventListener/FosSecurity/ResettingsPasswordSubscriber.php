<?php

namespace App\EventListener\FosSecurity;


use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ResettingsPasswordSubscriber implements EventSubscriberInterface
{
    private $retryTtl;
    /**
     * @var Environment
     */
    private $twig;

    public function __construct($retryTtl, Environment $twig)
    {
        $this->retryTtl = $retryTtl;
        $this->twig = $twig;
    }

    public function onResettingResetRequest($event) : void
    {
        //dump("RESETTING_RESET_REQUEST === ".get_class($event));
        //dump($event);
    }

    public function onResettingSendEmailInitialize(GetResponseNullableUserEvent $event) : void
    {
        //dump("RESETTING_SEND_EMAIL_INITIALIZE === ".get_class($event));
        //dump($event);

        $user = $event->getUser();

        if(null !== $user && $user->isPasswordRequestNonExpired($this->retryTtl))
        {
            $html = $this->twig->render('./bundles/FOSUserBundle/Resetting/already_send_email.html.twig', ['tokenLifetime' => ceil($this->retryTtl / 60)]);
            $response = new Response($html);

            $event->setResponse($response);

            //check ttl and if bad redirect to check_email
            //dump($this->retryTtl);
        }


    }

    public static function getSubscribedEvents()
    {
        return [
            //FOSUserEvents::RESETTING_RESET_REQUEST => 'onResettingResetRequest',
            FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE => 'onResettingSendEmailInitialize'
        ];
    }
}