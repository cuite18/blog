<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        // creation d 10 faux article 
        for($i = 1; $i <= 10; $i++)
        {
            // pour pouvoir inseret dans la table SQL 'article' nous devons instancier un objet isssu de cette classe
            // l'entité 'Article'reflete la table SQL 'Article'
            // nous avons besoin de renseigner tout les setteurs et tout les objet $arcticle afin de pouvoir généré les innserrion en BDD
            $article = new Article; // ctrl + alt + i (pc) pour importer la classe (PHP namespace Resolver)

            // On remplit les objets articles grace au setteur
            $article->setTitle("Titre de l'article n $i")
                    ->setContent("<p>Contenu de l'article $i</p>")
                    ->setImage("https://picsum.photos/200/300")
                    ->setCreatedAt(new \DateTime);


                // En Symfony, nous avons besoin d'un manager qui permet de manipuler les lignes de la BDD (insertion, modification, suppression)
                // persist() est une méthode issue de la classe ObjectManager qui permet de garder en mémoire les objets ârticle crées et préparer les requetes d'insertion (INSERT INTO)

            
            $manager->persist($article);
            
        }

        // flush() est une méthode issue de la classe ObjectManager qui permet véritablement d'executer les insertions en BDD (similaire à execute() en PHP)
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();


        // une fois les fixtures réaliseés, il faut les charger en BDD grace à doctrine (ORM) par la commande : 
        // php bin/console doctrine:fixtures:load
    }
}
