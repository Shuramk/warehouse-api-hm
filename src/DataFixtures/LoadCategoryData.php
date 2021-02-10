<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LoadCategoryData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName('Вибропрессованнвя тротуарная плитка');
        $this->addReference("category1", $category);
        $manager->persist($category);

        $category = new Category();
        $category->setName('Заборы');
        $this->addReference("category2", $category);
        $manager->persist($category);

        $manager->flush();
    }
}