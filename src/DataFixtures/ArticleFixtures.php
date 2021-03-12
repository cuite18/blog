<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        // j'importe la librairie Faker installer via coposer
        $faker = \Faker\Factory::create('fr_FR');

        // Librairie permette de crée des donnée fictives (nom, adresse, phrases, date, etc...)
        
        //Creation de category
        for($i = 1; $i <= 3; $i++)
        {
            // pour inserer dans la table Catégorie, nous devons remplir des objet issu de son entité Category::class
            $category = new Category;

            //On appel les setteur de l'objet
            $category->setTitle($faker->sentence())// Créer les phrases aléatoire
                     ->setDescription($faker->paragraph());// créer un paragraphe aléatoir

            $manager->persist($category); // on garde en memoire et on prépare les requerts d'insertion

            // Creation de 4 a 6 Article pour chaque Catégorie
            for($j = 1; $j <= mt_rand(4,6); $j++)
            {
                // pour inserer dans la table Article nous devons remplir des objet issu de sont entité article::class
                $article = new Article;

                //On créer 5 paragraphe que lon rassemble en une chaine de caracteres (join)
                $content = '<p>' .join($faker->paragraphs(5), '</p><p>') . '</p>';

                $article->setTitle($faker->sentence())// phrases aléatoire pour le titre de l'article
                        ->setContent($content)// paragraphe aléatoire pour le contenu
                        ->setImage("https://picsum.photos/seed/picsum/600/400
                        ")// image aléatoire
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))// on crée les dates aléaatoire d'article de - de 6mois
                        ->setCategory($category);// onrelie les article au catégorie (clé étrangere)
                
                $manager->persist($article);

                // creation de 4 a 10 comentaire pour chaque article
                for($k = 1; $k <= mt_rand(4,10); $k++)
                {
                    // pour inserer dans la table Comment, nous devons remplire des objet issu de ton entité Comment::class
                    $comment = new comment;

                    // On a créer 2 paragraphe que l'on rasemble en chaine de caractère (join)
                    $content ='<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';

                    $now = new \Datetime;// retourne la date du jour

                    $interval = $now->diff($article->getCreatedAt());// retourne un timestamp (temps en secondes) entre la date de création des articles et aujourd'hui

                    $days = $interval->days;// nombre de jour entre la date de création des articles et aujourd'hui

                    $minimun = "-$days days";/* -100 days le but est d'avoir des commentaires qui à l'interval de la création des articles, des commentaires de - de 6 mois à aujourd'hui */

                    $comment->setAuthor($faker->name)// nom aléatoire auteur
                            ->setContent($content)// paragraphe aléatoire
                            ->setCreatedAt($faker->dateTimeBetween($minimun)) // date aléatoire pour les commentaire entre la date de creation et aujourd'hui
                            ->setArticle($article);// on relie les commentaire au articles (clé étrangère) on transmet les objet $article

                    $manager->persist($comment);
                }

            }
        }

        $manager->flush();// on execute les insertion
    }
    
}
