<?php

namespace App\Controller\Spinner;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{
    /**
     * @Route("/spinner", name="spinner_home")
     */
    public function main()
    {
        return $this->render("spinner/main.html.twig");
    }
}