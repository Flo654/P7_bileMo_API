<?php

namespace App\Controller;

use App\Entity\Product;
use OpenApi\Annotations\Tag;
use OpenApi\Annotations as OA;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Schema;
use App\Repository\ProductRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @Route("/api")
 * 
 * @Security(name="Bearer")
 * @OA\Tag(name="Products")
 * 
 */
class ProductController extends AbstractController
{
    private $productRepository;
    

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }    
    
    /**
     * List of all products 
     * 
     * @Route("/products", name="product-list", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of all available products",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Product::class)))
     * )     
     */
    public function getAll(CacheInterface $cacheInterface): Response
    {
        $productsInCache = $cacheInterface->get('productList',function (ItemInterface $item){
            $item->expiresAfter(30);
            return $this->productRepository->findAll();
        });
        
        return $this->json($productsInCache, 200);
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
    public function getOne($id, CacheInterface $cacheInterface): Response
    {
        $product = $this->productRepository->find($id);
        if(!$product){
            return $this->json("this product does not exist", 404);
        }
        $productInCache = $cacheInterface->get('productdetails'. $id,function (ItemInterface $item) use($product){
            $item->expiresAfter(300);
            return $product;
        });
        return $this->json($productInCache, 200);
    }
}
