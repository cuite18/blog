<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('blog/home.html.twig', [
            'title' => "bienvenue sur le blog Symfony ma Moula",
            'age' => 32,
        ]);
    }
    /**
     * methode permettant d'afficher 
     * 
     * @Route("/blog", name="blog")
     */
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'title' => 'liste des article',
        ]);
    }
    /**
     * methode permettent d'afficher le detail d'un article
     * 
     * @Route("/blog/12", name="blog_show")
     */
    public function show(): Response
    {
        return $this->render('blog/show.html.twig');
    }



}
