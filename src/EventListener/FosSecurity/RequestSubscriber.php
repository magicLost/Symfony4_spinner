<?php

namespace App\EventListener\FosSecurity;


use App\Service\SecurityTargetPath;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Http\SecurityEvents;

class RequestSubscriber implements EventSubscriberInterface
{

    private $securityTargetPath;
    private $router;

    public function __construct(SecurityTargetPath $securityTargetPath, RouterInterface $router)
    {
        $this->securityTargetPath = $securityTargetPath;
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if($request->isXmlHttpRequest())
            return;

        $uri = $request->getRequestUri();

        //if user go to not fos_security addresses we do nothing
        if(!$this->securityTargetPath->isLoginUri($uri)){

            return;
        }

        //dump("login uri");
        //dump($event->getRequest()->getRequestUri());
        //dump($this->router->generate("fos_user_security_login"));

        $referer = ($_SERVER['HTTP_REFERER']) ?? '';
        //dump($referer);
        //dump($this->securityTargetPath->isSavingReferer($referer));
        $this->securityTargetPath->saveReferer($referer, $event->getRequest());
        //dump($this->securityTargetPath->getRedirectUri($request));
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'OnKernelRequest'
        ];
    }
}