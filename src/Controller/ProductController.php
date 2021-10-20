<?php

namespace App\Controller;

use OpenApi\Annotations\Tag;
use OpenApi\Annotations as OA;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Schema;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\JsonContent;
use App\Repository\ProductRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



/**
 * @Route("/api")
 * 
 * @Security(name="Bearer")
 * @OA\Tag(name="Products")
 */
class ProductController extends AbstractController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }    
    
    /**
     * @Route("/products", name="product-list", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of all available products",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Product::class)))
     * ) 
     */
    public function getAll(): Response
    {
        return $this->json($this->productRepository->findAll(), 200);
    }

    /**
     * Details of one product by id
     * 
     * @Route("/products/{id}", name="product-detail", methods={"GET"})
     * 
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="product ID",
     *     required=true,
     *     @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     *     response=200,
     *     description="product details",
     *     @Model(type=Product::class)
     * ),
     * 
     * @OA\Response(
     *     response=404,
     *     description="product not Found",
     *     @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="this product does not exist" )
     *     )     *     
     * )
     */
    public function getOne($id): Response
    {
        $product = $this->productRepository->find($id);
        if(!$product){
            return $this->json("this product does not exist", 404);
        }
        return $this->json($product, 200);
    }
}

