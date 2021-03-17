<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\ArticleFormType;
use App\Form\CategoryFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
     @Route("/admin/category/{id}/remove", name="admin_remove_category")
     */
    public function admin_category(EntityManagerInterface $manager, CategoryRepository $repoCategory, Category $category = null): Response
    {
        $colonnes = $manager->getClassMetadata(Category::class)->getFieldNames();

        dump($colonnes);

        // si la variable retourne true cela veut dire qu'elle contient une categorie de la BDD alors on entre dans le if  et on tente d'executer la suppresion
        if($category)
        {
            // Nous avons une relation entre la table Article et Category et une contrainte d'intégrité en RESTRICT
            // Donc ne pourrons pas supprimer la catégorie si 1 article lui est toujours associé
            // getArticles() de l'entité Category retourne tout les articles associés à la catégorie (relation bi-drirectionnelle)
            // Si getArticles() retourne un résultat vide, cela veut dire qu'il n'y a plus aucun article associé à la catégorie, nous pouvons dcon la supprimer
            if($category->getArticles()->isEmpty())
            {
                $manager->remove($category);
                $manager->flush();

                $this->addFlash('success', "la catégorie est supprimée !!!!");
            }
            else // sino tout les autre cas les article sont toujours afficher a la categorie on affiche un messages d'erreur a lutilisateur
            {
                $this->addFlash('danger', "la catégorie ne peut pas  etre supprimer des arcticles y sont toujours associée !!");
            }
            
            return $this->redirectToRoute('admin_category');
        }

        $categoryBdd = $repoCategory->findAll();

        dump($categoryBdd);

        return $this->render('admin/admin_category.html.twig', [
            'colonnes' => $colonnes,
            'categoryBdd' => $categoryBdd
        ]);
    }
    
    /**
     * 
     * 
     * @Route("/admin/category/new", name="admin_new_category")
     * @Route("/admin/category/{id}/edit", name="admin_edit_category")
     */
    public function adminFormCategory(Request $request , EntityManagerInterface $manager, Category $category = null): Response
    {
        // Si l'objet entité $category ne possède pas d'id, cela veut dire que nous sommes sur la route '/admin/category/new', que nous souhaitons créer une nouvelle catégorie, alors on entre dans la condition IF
        // Si l'objet entité $category possède un id, cela veut dire que nous sommes sur la route "/admin/category/{id}/edit", l'id envoyé dans l'URL a été selctionné en BDD, nous souhaitons modifier la catégorie existante
        if(!$category)
        {
            $category = new Category;
        }

        $formCategory = $this->createForm(CategoryFormType::class, $category, [
            'validation_groups' => ['category']
        ]);

        dump($request);

        $formCategory->handleRequest($request);

        dump($category);

        if($formCategory->isSubmitted() && $formCategory->isValid())
        {
            if(!$category->getId())
                $message = "la categorie" . $category->getTitle() . " a été enregistré avec success!!!";
            else
                $message = "la categorie" . $category->getTitle() . "a été modifier avec success!!!";

            $manager->persist($category); // on prepare et on garde en mémoire la requette insert
            $manager->flush();

            $this->addFlash('success', "la categorie a été enregistré avec success !!!");

            return $this->redirectToRoute('admin_category');
        }



        /*
            Insertion d'une categorie en BDD : 
            1. Créer une classe permettant de générer un forumlaire correspondant à l'entité Category (make:form)
            2. dans le controller, faites en sorte d'importer et de créer le formulaire, en le reliant à l'entité
            3. Envoyé le formulaire sur le template (render) et l'afficher en front 
            4. Récupérer et envoyer les données de $_POST dans le bonne entité à la valodation du formulaire (handleRequest + $request)
            5. Générer et executer la requete d'insertion à la validation du formulaire ($manager + persist + flush)
        */



        return $this->render('admin/admin_form_category.html.twig', [
            'formCategory' => $formCategory->createView()
        ]);
    }

    /**
     * 
     * @Route("/admin/comments", name="admin_comments")
     * @Route("/admin/comment/{id}/remove", name="admin__remove_comment")
     */
    public function adminComment(EntityManagerInterface $manager, CommentRepository $repoComment, Comment $comments = null): Response
    {
        /*
            1. Faites en sorte de récupérer les métadonnée de la table Comment afin de récupérer le nom des champs/colonne de la table SQL comment et les transmettre au template
            2. Afficher le nom des champs/colonne sous forme de tableau HTML
            3. Dans le controller, seelctionner tout les commentaires stockés en BDD et les transmettre au template
            4. Afficher tout les commentaires de la BDD sous forme de tableau HTML dans le template
            5. Prévoir 2 liens (modification / suppression) pour chaque commentaire 
            6. Réaliser le traitement permettant de supprimer un commentaire dans la BDD
         */
        $colonnes = $manager->getClassMetadata(Comment::class)->getFieldNames();

        dump($colonnes);

        $commentsBdd = $repoComment->findAll();

        return $this->render('admin/admin_comments.html.twig', [
            'colonnes' => $colonnes,
            'commentsBdd' => $commentsBdd
        ]);
    }


    /**
     * @Route("/admin/comment/{id}/edit", name="admin_edit_comment")
     */
    public function editComment(): Response
    {
        return $this->render('admin/admin_edit_comment.html.twig');
    }
}
