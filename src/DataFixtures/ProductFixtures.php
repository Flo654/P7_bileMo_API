<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\fr_FR\Company;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');
        
        
        for($products =0; $products < 25; $products++){
            $product = new Product;
            $product
                ->setColor($faker->colorName())
                ->setName($faker->word(2, true))
                ->setDescription($faker->paragraph(6, true))
                ->setPrice($faker->randomFloat(2,399,1299))
                ->setQuantityAvailable($faker->numberBetween(0,49))
                
            ;
            $manager->persist($product);
        }

        $manager->flush();
    }
}
