<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BrainController extends AbstractController
{
    /**
     * @Route("/brain", name="brain")
     */
    public function liste(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();

        return $this->render('brain/liste.html.twig', [
            'controller_name' => 'BrainController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(){
        return $this->render('brain/home.html.twig');
    }

    /**
    * @Route("/brain/new", name="brain_create")
    * @Route("/brain/{id}/edit", name="brain_edit")
    */
    public function form(Article $article = null, Request $request, EntityManagerInterface $entityManager){
        if(!$article){
            $article = new Article(0);
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime())
                ;
            }

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('brain_show', ['id'=> $article->getId()]);
        }

        return $this->render('brain/create.html.twig', [
            'formArticle'=> $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/brain/{id}", name="brain_show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $entityManager){
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article)
            ;

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('brain_show', ['id' =>
            $article->getId()]);
        }

        return $this->render('brain/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }
}
