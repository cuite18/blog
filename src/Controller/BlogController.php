<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleFormType;
use Doctrine\ORM\EntityManager;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function index(ArticleRepository $repo): Response
    {

        /*
            Pour selectionner des données dans une table SQL, nous devons absolument avoir accès à la classe Repository de l'entité correspondante 
            Un Repository est une classe permettant uniquement d'executer des requetes de selection en BDD (SELECT)
            Nous devons donc accéder au repository de l'netité Article au sein de notre controller  

            On appel l'ORM doctrine (getDoctrine()), puis on importe le repositoritory de la classe Article grace à la méthode getRepository()
            $repo est un objet issu de la classe ArticleRepository
            cet objet contient des méthodes permettant d'executer des requetes de selections
            findAll() : méthode issue de la classe ArticleRepository permettant de selectionner l'ensemble de la table SQL 'Article'
        */


        //$repo = $this->getDoctrine()->getRepository(Article::class);

        // outil de débuggage de symfony (equivalent d'un var_dump en php)
        dump($repo);

        $articles = $repo->findAll();// équivalent a un SELECT * FROM article + fetchAll

        // outil de débuggage de symfony (equivalent d'un var_dump en php)
        dump($articles);

        return $this->render('blog/index.html.twig', [
            'title' => 'liste des Moulaga',
            'articles' => $articles // on envoie sur le template, les articles selectionnés en BDD afin de pouvoir les afficher dynamiquement sur le template à l'aide du langage Twig

        ]);

    }

     /**
     * méthode permettant d'inseret et de Modifier un arcticle
     * 
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function create(Article $articleCreate = null, Request $request, EntityManagerInterface $manager): Response
    {
        // la classe Request de Symfony permet de véhiculer les données des superglobales PHP ($_POST, $_FILES, $_COOKIE, $_SESSION)
        // $request est un objet issu de la classe Request injecté en dependance de la méthode create()

        dump($request);

        // $request permet de stocker les donnée des superglobales la proprietés $request->request permet de stocker les donnée véhiculer par un formulaire ($_POST), ici on compte si il y a donnée qui ont été saisie dans le formulaire
        // if($request->request->count() > 0)
        // {
        //     // Pour inserer dans la table Article nous devons instancier un objet issu de la classe entité Article qui est lié a la table SQL Article
        //     $articleCreate = new Article;

        //     // On rensigne tout les setteurs de l'objet avec en arguments les données du formulaire ($_POST)
        //     $articleCreate->setTitle($request->request->get('title'))
        //                   ->setContent($request->request->get('content'))
        //                   ->setImage($request->request->get('image'))
        //                   ->setCreatedAt(new \DateTime);

        //     dump($articleCreate);// on observe que l'objet entité Article $articleCreate, les propriétés contiennent bien les données du formaulaire

        //     // On fait appel au manager afin de pouvoir executer une insertion en BDD
        //     $manager->persist($articleCreate); // on prépare et garde en mémoire l'insertion
        //     $manager->flush(); // on execute l'insertion

        //     // Après l'insertion, on redirige l'internaute vers le détail de l'article qui vient d'être inséré en BDD
        //     // Cela correspond à la route 'blog_show', mais c'est une route paramétrée qui attend un ID dans l'URL
        //     // En 2ème argument de redirectToRoute, nous transmettons l'ID de l'article qui vient d'être inséré en BDD
        //     return $this->redirectToRoute('blog_show', [
        //         'id' =>$articleCreate->getId()
        //     ]);
        // }


        

        //createFormBuilder() méthode de symfony qui permet de generee un formulaire de remplir une entité $articleCreate
        // $form = $this->createFormBuilder($articleCreate)
        //              ->add('title') // add() méthode qui permet de généré des champ de formulaire

        //              ->add('content')

        //              ->add('image')

        //              ->getForm(); //permet de valider le formulaire //permet d'afficher le rendu final


        // Si la variable $articleCreate N'EST PAS, si elle ne contient aucun article de la BDD, cela veut dire nous avons envoyé la route '/blog/new', c'est une insertion, on entre dans le IF et on crée une nouvelle instance de l'entité Article, création d'un nouvel article
        // Si la variable $articleCreate contient un article de la BDD, cela veut dire que nous avons envoyé la route '/blog/id/edit', c'est une modifiction d'article, on entre pas dans le IF
        if(!$articleCreate)
        {
            $articleCreate = new Article; // setTitle($_POST['title'])
        }

        

        // Ici nous renseignons le setter de l'objet et Symfony est capable automatiquement d'envoyer les valeurs de l'entité directement dans les attributs 'value' du formulaire, étant donné que l'entité $articleCreate est relié au formulaire
        // $articleCreate->setTitle("titre a la con")
        //               ->setContent("contenu a la con");


        // Nous avons créer une classe qui permet de générer le formulaire d'ajout d'article, il faut dans le controller importer cette classe ArticleFormType et relier le formulaire à notre entité Article $articleCreate
        $form = $this->createForm(ArticleFormType::class, $articleCreate);

        
        // On pioche dans l'objet du formulaire la methode handleRequest() qui permet de recuperer chaque données saisie dans le formulaire ($request) et de les bindé, de les transmettre directement dans les bons setteur de mon entité $articleCreate
        // $_POST['title'] --> setTitle($_POST['title']);
        $form->handleRequest($request);

        //dump($articleCreate);

        if($form->isSubmitted() && $form->isValid())
        {
            

            if(!$articleCreate->getId())
            {
                // On appel le setter de la date ,puisque nous avons pas de champs date dans le formulaire
                $articleCreate->setCreatedAt(new \DateTime);
            }

            $manager->persist($articleCreate); // On appel le manager pour préparer la requet d'insertion et la garder en mémoire
            
            $manager->flush();// On execute la resuette d'insertion en BDD

            return $this->redirectToRoute('blog_show', [
                'id' => $articleCreate->getId()
            ]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(), // on transet sur le template le formulaire createView() retourne un petit objet qui represent l'affichage du formulaire on le recupère sur le template create.html.twig

            'editMode' => $articleCreate->getId()
        ]);
    }

    /**
     * methode permettent d'afficher le detail d'un article
     * 
     * on definie une route parametré une route definie avec un ID d'un article dans l'URL
     * /blog/9-->{id} --> $id = 9
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article): Response
    {
        // $repoArticle est un objet issue de la classe articleRepository
        //$repoArticle = $this->getDoctrine()->getRepository(Article::class);

        //dump($repoArticle);

        //dump($id); // id 9

        // On transmet à la méthode find() de la classe ArticleRepository l'id recupéré dans l'URL et transmit en argument de la fonction show($id) | $id = 3
        // La méthode find() permet de selectionner en BDD un article par son ID
        //$article = $repoArticle->find($id);

        return $this->render('blog/show.html.twig', [
            'articleTwig' => $article // on envoi sur le template les données selectionnées en BDD, c'est à dire les informations d'1 article en fonction l'id transmit dans l'URL
        ]);
    }

    /*
        En fonction de la route paramétrée {id} et de l'injection de dépendance $article, Symfony voit que l'on besoin d'un article de la BDD par rapport à l'ID transmit dans l'URL, il est donc capable de recupérer l'ID et de selectionner en BDD l'article correspondant et de l'envoyer directement en argument de la méthode show(Article $article)
        Tout ça grace à des ParamConverter qui appel des convertisseurs pour convertir les paramètres de l'objet. Ces objets sont stockés en tant qu'attribut de requete et peuvent donc être injectés an tant qu'argument de méthode de controller
    */

   
}
