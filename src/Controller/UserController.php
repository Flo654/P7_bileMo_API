<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\FinalUser;
use App\Repository\FinalUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;


/**
 * @Route("/api")
 * 
 */
class UserController extends AbstractController
{
    private $finalUserRepository;    

    public function __construct(FinalUserRepository $finalUserRepository)
    {
        $this->finalUserRepository =$finalUserRepository;
        
    }
    
    
    /**
     * List of finalUsers belonging to an user
     * 
     * @Route("/users", name="user-list", methods={"GET"})
     * 
     */
    public function getAll(): Response
    {
        $finalUserList = $this->finalUserRepository->findBy(['user' => $this->getUser()->getId()]);      
        if ($finalUserList === []){
            return $this->json("you don't have any users yet", 404);
        }
        return $this->json($finalUserList, 200, [], ['groups' => 'finalUser:read']);
    }

    /**
     * Details of one FinalUser by id
     * 
     * @Route("/users/{id}", name="user-detail", methods={"GET"})
     * 
     * @return Response
     */
    public function getOne($id): Response
    {
        //verifier que le finalUser existe
        if (!$this->finalUserRepository->find($id)){
            return $this->json("This user does not exist", 404);       
        }
        
        $finalUser = $this->finalUserRepository->findBy(['id' => $id, 'user' => $this->getUser()->getId()]);      
        if (!$finalUser){
            return $this->json("You cannot access this user", 403);
        }
        return $this->json($finalUser, 200, [], ['groups' => 'finalUser:read']);
    }

    /**
     * Create a FinalUser
     * 
     * @Route("/users", name="user-create", methods={"POST"})
     * 
     */
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validatorInterface)
    {
              
        try {
            $finalUser = $serializer->deserialize($request->getContent(), FinalUser::class, 'json');         
            $finalUser
                ->setUser($this->getUser())
                ->setCreatedAt(new DateTimeImmutable('NOW'))
            ;
            $errors = $validatorInterface->validate($finalUser);
            if(count($errors) > 0){
                return $this->json($errors, 400);
            }
        
            $entityManager->persist($finalUser);
            $entityManager->flush();

            return $this->json($finalUser, 201, [], ['groups' => 'finalUser:read']);
       } catch (NotEncodableValueException $e) {
           return $this->json([
               'status' => 400,
               'message' => $e->getMessage()
           ], 400);
       }
    }
    
    /**
     * @Route("/users/{id}", name="user-delete", methods={"DELETE"})
     *
     */
    public function delete($id, EntityManagerInterface $entityManager)
    {
        
        //verifier que le finalUser existe
        if (!$this->finalUserRepository->find($id)){
            return $this->json("This user does not exist", 404);       
        }

        //verifier que l'utilisateur est le createur du finalUser
        $checkUser = $this->finalUserRepository->findOneBy(['id'=> $id, 'user' => $this->getUser()->getId()]);        
        if (!$checkUser){
            return $this->json("You cannot delete this user", 403);
        }

        //verifier que l'utilisateur a le droit d'effacer un final user
        // effacer le final user de la base de données
        $entityManager->remove($checkUser);
        $entityManager->flush();

        return $this->json("You have deleted this user ", 204);
    }

}
