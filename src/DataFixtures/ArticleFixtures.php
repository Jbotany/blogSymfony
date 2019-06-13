<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $slugify = new Slugify();
        for ($i=0; $i<count(CategoryFixtures::CATEGORIES); $i++) {
            $references[] = 'categorie_' . $i;
        }


        $faker  =  Faker\Factory::create('fr_FR');
        for ($i=0; $i<=50; $i++) {
            $article = new Article();
            $article->setTitle(mb_strtolower($faker->sentence($nbWords = 1, $variableNbWords = true)));
            $article->setcontent($faker->sentence());
            $article->setSlug($slugify->generate($article->getTitle()));
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
