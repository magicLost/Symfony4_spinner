<?php

//class work with references uri after login, register
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use FOS\UserBundle\FOSUserEvents;

class SecurityTargetPath
{
    use TargetPathTrait;

    private const OUR_TARGET_PATH_KEY = "Security_Target_Path";

    private $router;

    private $security_referer = [

        'fos_user_security_login',
        'fos_user_security_check',
        'fos_user_registration_register',
        'fos_user_resetting_request'

    ];

    private $security_login_uri = 'fos_user_security_login';

    private $security_uri = [

        'fos_user_security_login',
        'fos_user_registration_register'

    ];

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    //if we go to one of securities urls, we save referer to session
    public function saveReferer(string $referer, Request $request) : void
    {
        //$referer = ($_SERVER['HTTP_REFERER']) ?? '';
        //if($referer != '' && $referer != $this->router->generate('security_login', [], UrlGeneratorInterface::ABSOLUTE_URL))
        if($this->isSavingReferer($referer))
        {
            $request->getSession()->set(
                self::OUR_TARGET_PATH_KEY, $referer
            );

        }
    }

    public function isSavingReferer(string $referer) : bool
    {

        foreach($this->security_referer as $value)
        {
            if($referer === $this->router->generate($value, [], UrlGeneratorInterface::ABSOLUTE_URL))
            {
                return false;
            }
        }

        return true;
    }

    public function isSecurityUri(string $uri) : bool
    {

        foreach($this->security_uri as $value)
        {
            if($uri === $this->router->generate($value))
            {
                return true;
            }
        }

        return false;
    }

    public function isLoginUri(string $uri) : bool
    {

        return $uri === $this->router->generate($this->security_login_uri);

    }



    public function getRedirectUri(Request $request) : string
    {
        //dump("Referer == ".$this->referer);

        //dump("Login path == ".$this->router->generate('security_login', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $url = $this->getTargetPath($request->getSession(), 'main');

        if($url)
            return $url;


        $referer = $request->getSession()->get(self::OUR_TARGET_PATH_KEY);

        if(!$referer){

           // $referer = $this->router->generate('home');

            $locale = $request->getLocale();
            $referer = ($locale === 'ru') ? $this->router->generate('home_ru') : $this->router->generate('home_en');

        }

        return $referer;
    }

    public function removeTargetPath(Request $request)
    {
        $session = $request->getSession();

        if($session->has(self::OUR_TARGET_PATH_KEY))
        {
            $session->remove(self::OUR_TARGET_PATH_KEY);
        }
    }

}