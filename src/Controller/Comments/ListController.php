<?php

namespace App\Controller\Comments;


use App\Entity\Comments\Comments;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends Controller
{
    private $logger;
    private $entityManager;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("{_locale}/comments", name="comments_list", requirements={"_locale"="ru|en"})
     */
    public function show_parent(Request $request)
    {


        if($request->isXmlHttpRequest())
        {
            try
            {
                /** @var  $repository CommentsRepository */
                $repository = $this->entityManager->getRepository(Comments::class);

                $comments = $repository->getParentComments(100);

                //$this->logger->debug("XmlHttp request list!!!!");

                return $this->json($comments);

            }
            catch(\Exception $exception)
            {
                $this->logger->debug("ERROR XmlHttprequest list!!!! ".$exception->getMessage());

                return $this->json([]);
            }
        }



        return $this->render("comments/list.html.twig");
    }

    /**
     * @Route("/comments/child_comments", name="child_comments")
     */
    public function show_child(Request $request)
    {
        if($request->isXmlHttpRequest() && $request->isMethod('post'))
        {
            try
            {
                $parent_id = (int)$request->request->get('parent_id');

                if($parent_id < 1)
                    throw new \Exception('Bad parent id');

                /** @var  $repository CommentsRepository */
                $repository = $this->entityManager->getRepository(Comments::class);
                //$comments = $repository->getCommentsInArrays(1300);
                $comments = $repository->getChildComments(3, $parent_id);

                //$this->logger->debug("XmlHttp request child_comments!!!!");

                return $this->json($comments);
            }
            catch(\Exception $exception)
            {
                $this->logger->debug("ERROR XmlHttprequest child comments!!!! ".$exception->getMessage());

                return $this->json([]);
            }
        }
    }

    /**
     * @Route("/comments/more_child_comments", name="more_child_comments")
     */
    public function show_more_child(Request $request)
    {
        if($request->isXmlHttpRequest() && $request->isMethod('post'))
        {
            try
            {
                $parent_id = (int)$request->request->get('parent_id');

                if($parent_id < 1)
                    throw new \Exception('Bad parent id');

                $last_child_id = (int)$request->request->get('last_child_id');

                if($last_child_id < 1)
                    throw new \Exception('Bad last child id');

                /** @var  $repository CommentsRepository */
                $repository = $this->entityManager->getRepository(Comments::class);

                $comments = $repository->getMoreChildComments(3, $parent_id, $last_child_id);

                //$this->logger->debug("XmlHttp request show_more_child!!!!");

                return $this->json($comments);
            }
            catch(\Exception $exception)
            {
                $this->logger->debug("ERROR XmlHttprequest child comments!!!! ".$exception->getMessage());

                return $this->json([]);
            }
        }
    }




}