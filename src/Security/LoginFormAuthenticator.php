<?php

namespace App\Security;


use App\Service\SecurityTargetPath;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    private $formFactory;
    private $entityManager;
    private $router;
    private $passwordEncoder;
    private $csrfTokenManager;
    /**
     * @var SecurityTargetPath
     */
    private $securityTargetPath;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        UserPasswordEncoderInterface $passwordEncoder,
        CsrfTokenManagerInterface $csrfTokenManager,
        SecurityTargetPath $securityTargetPath,

        LoggerInterface $logger
    )
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->securityTargetPath = $securityTargetPath;
        $this->logger = $logger;
    }

    public function supports(Request $request)
    {
        $this->logger->warning("LoginFormAuthenticator | supports " );


        //dump("login guard");

        /*$pathInfo = $request->getPathInfo();
        $routeGenerate = $this->router->generate("fos_user_security_check");

        if($request->getPathInfo() === $this->router->generate("fos_user_security_check") && $request->isMethod('post'))
        {
            $this->logger->warning("Welcome | pathInfo == ".$pathInfo." | routeGenerae == ".$routeGenerate );
        }
        else{
            $this->logger->warning("Welcome | pathInfo == ".$pathInfo." | routeGenerae == ".$routeGenerate );

        }*/

        return $request->getPathInfo() === $this->router->generate("fos_user_security_check") && $request->isMethod('post');
    }

    public function getCredentials(Request $request)
    {

        $username = $request->request->get('_username');
        $password = $request->request->get("_password");
        $token = $request->request->get("_csrf_token");

        $this->logger->warning("LoginFormAuthenticator | getCredentials | name == ".$username." | password == ".$password." | token == ".$token );

        if(false == $this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $token)))
        {
            throw new InvalidCsrfTokenException('Invalid Csrf token.');
        }

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $username
        );

        return [
            '_password' => $password,
            '_username' => $username
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $this->logger->warning("LoginFormAuthenticator | getUser" );

        $userName = $credentials['_username'];

        if(null === $userName)
        {
            return null;
        }

        return $userProvider->loadUserByUsername($userName);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $this->logger->warning("LoginFormAuthenticator | checkCredentials" );

        $password = $credentials['_password'];

        if($this->passwordEncoder->isPasswordValid($user, $password))
        {
            return true;
        }

        return false;
    }

    protected function getLoginUrl()
    {
        //dump($this->router->generate("fos_user_security_login"));die;
        /*$referer = $_SERVER['HTTP_REFERER'];

        $locale = (strpos($referer, 'en') === false) ? 'ru' : 'en';

        return $this->router->generate("fos_user_security_login", ['_locale' => $locale]);*/
        return $this->router->generate("fos_user_security_login");
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        /*$locale = $request->getLocale();
        $url = ($locale === 'ru') ? $this->router->generate('home_ru') : $this->router->generate('home_en');
        return new RedirectResponse($url);*/

        $url = $this->securityTargetPath->getRedirectUri($request);

        $this->securityTargetPath->removeTargetPath($request);

        return new RedirectResponse($url);
    }

}