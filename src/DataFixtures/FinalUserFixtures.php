<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\FinalUser;
use Faker\Provider\fr_FR\Company;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FinalUserFixtures extends Fixture implements DependentFixtureInterface
{
    
    private $hasher;
    private $userRepository;

    public function __construct(UserPasswordHasherInterface $hasher, UserRepository $userRepository)
    {
        $this->hasher = $hasher;
        $this->userRepository = $userRepository;
    }    
    
    public function load(ObjectManager $manager): void
    {        
        $faker = Factory::create('fr_FR');
        $users = $this->userRepository->findAll();
        
        for($finalUsers=0; $finalUsers<20; $finalUsers++)
        {
            $finalUser = new FinalUser;
            $finalUser
                ->setEmail($faker->email())
                ->setPassword($this->hasher->hashPassword($finalUser, "password"))
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setCreatedAt(new DateTimeImmutable('NOW'))
                ->setUser($faker->randomElement($users))
            ;
            $manager->persist($finalUser);
        }        
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}