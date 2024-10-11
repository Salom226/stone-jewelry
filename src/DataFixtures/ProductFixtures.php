<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            $product->setName('Product '.$i);
            $product->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda consequatur libero possimus molestias incidunt culpa porro, quidem officiis, minus inventore dolores! Aperiam iste perspiciatis minima harum expedita nobis sit velit.'.$i);
            $product->setPrice(mt_rand(10, 100));
            $product->setStock(mt_rand(1, 50));

            $product->setImage($faker->image('public/uploads/images', 640, 480, 'products', false));
            
            $manager->persist($product);
        }

        $manager->flush();
    }
}
