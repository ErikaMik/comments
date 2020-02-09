<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * @Route("/home", name="home")
     */
    public function index(CommentRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $comments = $this->getDoctrine()
            ->getRepository('App\Entity\Comment')->findAll();

        // Add pagination
//        $queryBuilder = $repository->getWithSearchQueryBuilder($q);
//
//        $pagination = $paginator->paginate(
//            $queryBuilder, /* query NOT result */
//            $request->query->getInt('page', 1)/*page number*/,
//            5/*limit per page*/
//        ); 'pagination' => $pagination,

        if(!empty($comments)){
            return $this->render('home/index.html.twig', [
                'comments' => $comments,
            ]);
        }else{
            return $this->render('home/index.html.twig', [
                'comments' => '',
            ]);
        }

    }

    /**
     * @Route("/write-comment", name="write-comment")
     */
    public function createComment(EntityManagerInterface $em, Request $request){
        $form = $this->createForm(CommentFormType::class);

        // If it is GET request, isSubmitted() returns false, and the form is passed to Twig
        $form->handleRequest($request); // Process POST data
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $comment = new Comment();
            $comment->setName($data['name']);
            $comment->setComment($data['comment']);

            $dateTimeNow = new DateTime('now');
            $comment->setDate($dateTimeNow);

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('home');

        }

        return $this->render('home/create.html.twig', [
            'commentForm' => $form->createView(),
        ]);
    }

}