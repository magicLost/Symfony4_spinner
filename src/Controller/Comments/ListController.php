<?php

namespace App\Controller\Comments;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ListController extends Controller
{
    /**
     * @Route("/{_locale}/comments", name="comments_list", requirements={"_locale"="ru|en"})
     */
    public function show_all()
    {
        return $this->render("comments/list.html.twig");
    }

}