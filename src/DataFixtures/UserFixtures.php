<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\fr_FR\Company;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    
    
    public function load(ObjectManager $manager): void
    {        
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Company($faker));

        $admin = new User;
        $admin
            ->setCreatedAt(new DateTimeImmutable('NOW'))
            ->setEmail('test@test.com')
            ->setPassword($this->hasher->hashPassword($admin,'test'))
            ->setCompagnyName($faker->company())
            ->setRoles(["ROLE_ADMIN"])
        ;
        $manager->persist($admin);

        for($users=0; $users<5; $users++){
            $user = new User;
            $user
                ->setEmail($faker->email())
                ->setRoles(["ROLE_ADMIN"])
                ->setPassword($this->hasher->hashPassword($user,'azerty'))
                ->setCompagnyName($faker->company())
                ->setCreatedAt(new DateTimeImmutable('NOW'))
            ;
            $manager->persist($user);
            
        }
        
        $manager->flush();
    }
}
