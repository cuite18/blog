<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * méthode permettant dafficher l'acceil du backOfice
     * 
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    /**
     * methode permettant d'afficher tout les liste des articles sous forme de tableaux HTML backoffice'
     * 
     * 
     * @Route("/admin/article/", name="admin_articles")
     * @route("/admin/{id}/remove", name="admin_remove_article")
     */
    public function adminArticles(EntityManagerInterface $manager, ArticleRepository $repoArticle, Article $article = null): Response
    {

        // Le manager permet de manipuler le BDD, on execute la méthode getClassMetadata() afin de selectionner les méta données des colonnes (primary key, not nul, type, taille etc...)
        // getFieldNames() permet de seelctionner le noms des champs/colonne de la table Article de la bdd
        // $colonne = $data->getColumnMeta() -> $colonne['name']
        $colonnes = $manager->getClassMetadata(Article::class)->getFieldNames();

        dump($colonnes);

        // selection des article en BDD
        $articles = $repoArticle->findAll();

        dump($article);

        if($article)
        {
            $id = $article->getId();

            $manager->remove($article); // on prepare la requette de suppresion (DELETE) on la garde en mémoire

            $manager->flush(); //on execute la requette de suppresion

            $this->addFlash('success', "l'article n°$id a bien été suprimée !!");

            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('admin/admin_articles.html.twig', [
            'colonnes' => $colonnes, // on  transmet a la methode rener le nom des champs colonnes selectionnée en bdd afin de pouvoir les receptionner  sur le template et de pouvoirles afficher
            'articleBdd' => $articles // // on transmet à la méthode render les articles selectionnés en BDD au template afin de pouvoir les afficher
        ]);
    }
    /**
     * Méthode permettant de modifier un article existant dans le BackOffice
     * 
     * @Route("/admin/{id}/edit-article", name="admin_edit_article")
     */
    public function adminEditArticle(Article $article, Request $request, EntityManagerInterface $manager)
    {
        dump($article);

        // On crée un formulaire via la classe ArticleFormType qui a pour but de remplir l'entité $article
        $formArticle = $this->createForm(ArticleFormType::class, $article);

        dump($request);

        $formArticle->handleRequest($request);

        if($formArticle->isSubmitted() && $formArticle->isValid())
        {
            $manager->persist($article); // on prepare la requette de modification 
            $manager->flush(); // on execute la resuette de modification

            $this->addFlash('success', "l'article n°" . $article->getId() . " a bien été modifier");

            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('admin/admin_edit_article.html.twig', [
            'idArticle' => $article->getId(),
            'formArticle' => $formArticle->createView()
        ]);
    }

    /**
     * méthode permettant dafficher sous forme de tableau HTML les catégories stockées en BDD
     * 
     *@Route("/admin/categories", name="admin_category")
     */
    public function admin_category(): Response
    {
        return $this->render('admin/admin_category.html.twig');
    }

}
