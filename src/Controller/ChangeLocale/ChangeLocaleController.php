<?php
/**
 * Created by PhpStorm.
 * User: Nikki
 * Date: 07.08.2018
 * Time: 21:50
 */

namespace App\Controller\ChangeLocale;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChangeLocaleController extends Controller
{
    private $paths_bad = [

    ];

    /**
     * @Route("/{_locale}/change_locale", name="change_locale", requirements={"_locale"="en|ru"})
     */
    public function change_locale(Request $request, string $_locale)
    {

        $path = $request->getQueryString();
        $path = str_replace('%2F', '/', $path);
        $path = str_replace('%3F', '?', $path);
        $path = str_replace('%3D', '=', $path);
        $path = str_replace('path=', '', $path);

        if($path === '/')
        {
            return $this->redirect('/en');
        }

        if($path === '/en')
        {
            return $this->redirect('/');
        }

        //если путь не один из левых путей, то просто меняем локале
        $path = ($_locale === 'ru') ? str_replace('ru', 'en', $path) : str_replace('en', 'ru', $path);


        return $this->redirect($path);
        //dump($path);exit;

    }
}