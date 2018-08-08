<?php

namespace App\EventListener\FosSecurity;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onLogoutSuccess(Request $request)
    {
        $locale = (strpos($request->getPathInfo(), 'en') !== false) ? 'en' : 'ru';

        $path = ($locale === 'ru') ? $this->router->generate('home_ru') : $this->router->generate('home_en', ['_locale' => 'en']);

        $httpUtils = new HttpUtils();

        return $httpUtils->createRedirectResponse($request, $path);
    }
}