<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home_ru")
     */
    public function index_ru()
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/{_locale}", name="home_en", requirements={"_locale"="en"} )
     */
    public function index_en(Request $request)
    {
        return $this->render('home/index.html.twig');
    }
}
