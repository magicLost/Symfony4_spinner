<?php

namespace App\Controller\Spinner;


use App\Service\Crypt;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class ChechAuthController extends Controller
{

    private $authorizationChecker;
    private $sessionStorage;
    private $authenticationUtils;
    private $crypt;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        SessionStorageInterface $sessionStorage,
        AuthenticationUtils $authenticationUtils,
        Crypt $crypt
    )
    {

        $this->authorizationChecker = $authorizationChecker;
        $this->sessionStorage = $sessionStorage;
        $this->authenticationUtils = $authenticationUtils;
        $this->crypt = $crypt;
    }

    /**
     * @Route("/spinner/check_auth", name="spinner_check_auth")
     */
    public function check(Request $request)
    {

        try
        {

            /*return $this->json(
                [
                    'result' => 'auth',
                    'playerName' => 'John',
                    'hash_one' => '@@123dsafa#$#$',
                    'hash_two' => $this->crypt->checkAuthEncodeData('John', 'auth', '@@123dsafa#$#$')
                ]
            );*/

            //if($request->isXmlHttpRequest())
            {

                if($this->authorizationChecker->isGranted(['ROLE_USER']))
                {
                    //$name = $this->authenticationUtils->getLastUsername();
                    $name = $this->getUser()->getUsername();

                    if($name === '')
                        throw new \RuntimeException("No name");

                    //get hash
                    $hash = '@@123dsafa#$#$';

                    return $this->json(
                        [
                            'result' => 'Auth',
                            'playerName' => $name,
                            'hash_one' => $hash,
                            'hash_two' => $this->crypt->checkAuthEncodeData($name, 'Auth', $hash)
                        ]
                    );

                }
                else
                {
                    throw new \Exception("No Auth");
                }
            }

        }
        catch(\RuntimeException $exception)
        {
            return $this->json(["result" => $exception->getMessage()]);
        }
        catch(\Exception $exception)
        {
            return $this->json(["result" => $exception->getMessage()]);
        }



        //return $this->createNotFoundException("Page not found");
    }

}
