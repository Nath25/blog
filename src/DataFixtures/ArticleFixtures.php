<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\Category;
use App\Service\Slugify;

use  Faker;



class ArticleFixtures extends Fixture
{

    /**
     * @var Slugify
     */
    private $slugify;

    /**
     * AppFixtures constructor.
     * @param Slugify $slugify
     */
    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }


    public function load (ObjectManager $manager)
    {
        $faker  =  Faker\Factory::create('fr_FR');
        for ($i = 1; $i <= 1000; $i++) {
            $category = new Category();
            $category->setName("category " . $i);
            $manager->persist($category);

            $tag = new Tag();
            $tag->setName("tag " . $i);
            $manager->persist($tag);

            $article = new Article();
            $article->setTitle("article " . $i);
            $article->setSlug($this->slugify->generate($article->getTitle()));
            $article->setContent("article " . $i . " content");
            $article->setCategory($category);
            $article->addTag($tag);
            $manager->persist($article);
        }

        $manager->flush();



    }

}
