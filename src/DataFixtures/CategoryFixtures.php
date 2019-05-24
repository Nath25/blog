<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Category;


class CategoryFixtures extends Fixture
{
    public function load (ObjectManager $manager)
    {
       foreach (self :: categories as $key => $categoryName) {
            $category = new Category();
            $category->setname($categoryName);
            $manager->persist($category);
            $this->addReference('categorie_' . $key, $category);
            $manager->flush();
        }
    }

    private $categories;

    CONST categories = [
        'PHP',
        'Java',
        'Javascript',
        'Ruby',
        'DevOps'
    ];

}



