<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;
use App\DataFixtures\CategoryFixtures;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        for ($i=0; $i<count(CategoryFixtures::CATEGORIES); $i++) {
            $references[] = 'categorie_' . $i;
        }


        $faker  =  Faker\Factory::create('fr_FR');
        for ($i=0; $i<=50; $i++) {
            $article = new Article();
            $article->setTitle(mb_strtolower($faker->sentence($nbWords = 1, $variableNbWords = true)));
            $article->setcontent($faker->sentence());
            $manager->persist($article);
            $article->setCategory($this->getReference($faker->randomElement($references)));
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }
}
