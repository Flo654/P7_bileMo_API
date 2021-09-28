<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/api")
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
     */
    public function getAll(): Response
    {
        return $this->json($this->productRepository->findAll(), 200);
    }


    /**
     * @Route("/products/{id}", name="product-detail", methods={"GET"})
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

