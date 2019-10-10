<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\ArticleType;


class BlogController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */

    public function home(){ 

        $prenom = "Copyright: Pépé 2019";
        return $this->render('blog/home.html.twig', [
            'controller_name' => 'BlogController',
            'prenom' => $prenom,
            
        ]);
    }

    /**
     * @Route("/articles", name="articles")
     */

    public function articles()
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();

        return $this->render('blog/articles.html.twig', [
        'articles' => $articles,
        ]);
    }

    /**
     * @Route("/blog/{id}", name="show")
     */

    public function show($id)
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);

        return $this->render('blog/show.html.twig', [
        'article' => $article,
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @Route("/blog/{id}/edit", name="edit")
     */

    public function form(Article $article = null, Request $request, ObjectManager $manager) {
        
        if(!$article) {
        $article = new Article();
        }

        //Methode Manuelle:

        // $form = $this->createFormBuilder($article)

        //             ->add('title')
        //             ->add('content')
        //             ->add('image')
        //             ->getForm();

        //Methode automatique: (php bin/console make:form)

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            if(!$article->getId()) {
            $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('show', ['id' => $article->getId()
            ]);
        }


        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    
    }

    /**
     * @Route("/contact", name="contact")
     */

    public function contact(){
        return $this->render('blog/contact.html.twig');
    }

}
