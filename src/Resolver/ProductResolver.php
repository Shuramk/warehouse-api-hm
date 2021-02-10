<?php

namespace App\Resolver;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

class ProductResolver extends ResolverMap {

    private ProductRepository $productRepository;
    private EntityManagerInterface $entityManager;
    private $categoryRepository;
    public function __construct(ProductRepository $productRepository, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    {

        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
    }

    public function map()
    {
        return [
            'Query' => [
                self::RESOLVE_FIELD => function ($value, Argument $args, \ArrayObject $context, ResolveInfo $info) {
                    if ('product' === $info->fieldName) {
                        $product = $this->productRepository->find($args['id']);
                        if ($product) {
                            return $product;
                        }
                    }

                    return null;
                },
                'products' => function ($value, Argument $args) {
                    return $this->productRepository->findAll();
                },
            ],
            'Mutation' => [
                'createProduct' => function($value, Argument $args) {
                    $product = new Product();
                    $product->setName($args['name']);
                    $product->setDescription($args['description']);
                    $product->setPrice($args['price']);
                    $product->setQuantity($args['quantity']);
                    $product->setCategory($this->categoryRepository->find($args['category']));

                    $this->entityManager->persist($product);
                    $this->entityManager->flush();

                    return $product;
                },
                'updateProduct' => function($value, Argument $args) {
                    $product = $this->productRepository->find($args['id']);
                    if ($args['name']) $product->setName($args['name']);
                    if ($args['description']) $product->setDescription($args['description']);
                    if ($args['price']) $product->setPrice($args['price']);
                    if ($args['quantity']) $product->setQuantity($args['quantity']);
                    if ($args['category']) $product->setCategory($this->categoryRepository->find($args['category']));
                    $this->entityManager->flush();
                    return $product;
                },
                'deleteProduct' => function($value, Argument $args) {
                    $product = $this->productRepository->find($args['id']);

                    $this->entityManager->remove($product);
                    $this->entityManager->flush();

                    return ["id" => $args['id']];
                },
            ],

        ];
    }


}