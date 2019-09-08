<?php

namespace App\Controller\Comments;


use App\Entity\Comments\Comments;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PostController extends Controller
{
    private $logger;
    private $entityManager;
    private $authorizationChecker;

    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @Route("/comments/post_comment", name="post_comment")
     */
    public function post(Request $request)
    {
        if($request->isXmlHttpRequest() && $request->isMethod('post'))
        {
            if(!$this->authorizationChecker->isGranted('ROLE_USER'))
            {
                throw $this->createNotFoundException('Post comments do not access');
            }

            try
            {
                $comment = new Comments();

                //get name from auth
                $name = $this->getUser()->getUsername();

                $comment->setName($name);

                //parent_id, content, created, hasChild, replyTo, upvote_count
                $parent_id = (int)$request->request->get('parent_id');
                $parent_id = ($parent_id < 1) ? null : $parent_id;
                $comment->setParentId($parent_id);
                $comment->setContent(trim(strip_tags($request->request->get('content'))));
                $comment->setHasChild(false);
                $comment->setReplyTo(trim(strip_tags(substr($request->request->get('replyTo'), 0, 45))));
                $comment->setCreated(new \DateTime());

                /** @var  $repository CommentsRepository */
                $repository = $this->entityManager->getRepository(Comments::class);

                //if first child update parent hasChild
                if($request->request->get('firstChild') && $parent_id !== null)
                {
                    $parent_comment = $repository->findOneBy(['id' => $parent_id]);
                    $parent_comment->setHasChild(true);

                    $this->entityManager->persist($parent_comment);
                }

                $this->entityManager->persist($comment);

                $this->entityManager->flush();

                return $this->json($comment);

            }
            catch(\Exception $exception)
            {
                $this->logger->debug("ERROR XmlHttprequest post!!!! ".$exception->getMessage());

                return $this->json([]);
            }

        }
        else
        {

            throw $this->createNotFoundException('Post comments not found');

        }
    }
}