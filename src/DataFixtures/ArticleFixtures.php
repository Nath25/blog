<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Article;
use  Faker;


class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load (ObjectManager $manager)
    {
        $faker  =  Faker\Factory::create('fr_FR');
        for ($j = 0; $j <5; $j++) {
            for ($i = 0; $i < 50; $i++) {
                $article = new Article();
                $article->setCategory($this->getReference('categorie_'.$j));
                //$article->setTitle('framework Php : symfony 4');
                $article->setTitle(mb_strtolower($faker->sentence()));
                //$article->setContent('Symfony 4 est la nouvelle version du framework lancée le 30 Novembre 2017');
                $article->setContent(mb_strtolower($faker->sentence()));
                $manager->persist($article);
            }
            $manager->flush();
        }



    }

    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }
}
