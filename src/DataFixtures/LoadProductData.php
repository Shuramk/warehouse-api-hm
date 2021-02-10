<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadProductData extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        //currency
        $product = new Product();
        $product->setName('"Старый город" 30мм серый');
        $product->setPrice(150);
        $product->setDescription('«Старый город» – плитка этой формы имеет скругленные углы, что делает ее похожей на средневековый камень. Неприхотлива к дизайнерским разработкам, брусчатка «Старый город» всегда будет выглядеть оригинально, ведь каждый участок – это каждый раз другой узор из различных камней. Разноцветный микс, двух или трех, четырех цветные выкладки, одноцветные массивы с единичными вставками контрастных камней – все это несет в себе изысканная плитка «Старый город».');
        $product->setQuantity(934.12);
        $product->setCategory($this->getReference("category1"));
        $manager->persist($product);

        $product = new Product();
        $product->setName('"Старый город" 40мм серый');
        $product->setPrice(170);
        $product->setDescription('«Старый город» – плитка этой формы имеет скругленные углы, что делает ее похожей на средневековый камень. Неприхотлива к дизайнерским разработкам, брусчатка «Старый город» всегда будет выглядеть оригинально, ведь каждый участок – это каждый раз другой узор из различных камней. Разноцветный микс, двух или трех, четырех цветные выкладки, одноцветные массивы с единичными вставками контрастных камней – все это несет в себе изысканная плитка «Старый город».');
        $product->setQuantity(321.54);
        $product->setCategory($this->getReference("category1"));
        $manager->persist($product);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return array(
            LoadCategoryData::class,
        );
    }
}