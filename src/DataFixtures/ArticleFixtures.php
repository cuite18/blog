<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        // j'importe la librairie Faker installer via coposer
        $faker = \Faker\Factory::create('fr_FR');
        
        //Creation de category
        for($i = 1; $i <= 3; $i++)
        {
            $category = new Category;

            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());

            $manager->persist($category);
        }
    }
}
