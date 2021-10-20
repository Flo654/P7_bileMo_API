<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\FinalUser;
use OpenApi\Annotations as OA;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Schema;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\MediaType;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\JsonContent;
use OpenApi\Annotations\RequestBody;
use App\Repository\FinalUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
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
 * @Security(name="Bearer")
 * @OA\Tag(name="Users")
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
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of all finalUsers's user",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=FinalUser::class, groups={"finalUser:read"})))
     * )
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
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="FinalUser ID",
     *     required=true,
     *     @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     *     response=200,
     *     description="details finalUsers's user",
     *     @OA\JsonContent(ref=@Model(type=FinalUser::class, groups={"finalUser:read"}))
     * ),
     * @OA\Response(
     *     response=404,
     *     description="This user does not exist",
     *     @OA\JsonContent(@OA\Property(property="message", type="string", example="this user does not exist" ))   
     * ),
     * @OA\Response(
     *     response=403,
     *     description="You cannot access this user",
     *     @OA\JsonContent(@OA\Property(property="message", type="string", example="You cannot access this user" ))   
     * ),
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
     * @OA\RequestBody(
     *     description="The new user to create",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/Json",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="email",
     *                 description="User's email address",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 description="FinalUser's password",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="first_name",
     *                 description="finalUser's first name",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="last_name",
     *                 description="finalUser's last name",
     *                 type="string"
     *             )
     *             
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Returns the created FinalUser",
     *     @OA\JsonContent(ref=@Model(type=FinalUser::class, groups={"finalUser:read"}))
     * ),
     * @OA\Response(
     *     response=400,
     *     description="bad request",
     *     @OA\JsonContent(@OA\Property(property="message", type="string", example="bad request" ))   
     * ),
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
     * delete an user's finalUser
     * 
     * @Route("/users/{id}", name="user-delete", methods={"DELETE"})
     * 
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="FinalUser ID",
     *     required=true,
     *     @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     *     response=204,
     *     description="delete finalUser",
     *     @OA\JsonContent(@OA\Property(property="message", type="string", example="You have deleted this user" ))
     * ),
     * @OA\Response(
     *     response=404,
     *     description="This user does not exist",
     *     @OA\JsonContent(@OA\Property(property="message", type="string", example="this user does not exist" ))   
     * ),
     * @OA\Response(
     *     response=403,
     *     description="You cannot delete this user",
     *     @OA\JsonContent(@OA\Property(property="message", type="string", example="You cannot access this user" ))   
     * ),
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
