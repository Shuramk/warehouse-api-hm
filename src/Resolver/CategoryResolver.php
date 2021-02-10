<?php

namespace App\Resolver;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

class CategoryResolver extends ResolverMap {

    private CategoryRepository $categoryRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    {
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }

    public function map()
    {
        return [
            'Query' => [
                self::RESOLVE_FIELD => function ($value, Argument $args, \ArrayObject $context, ResolveInfo $info) {
                    if ('category' === $info->fieldName) {
                        $category = $this->categoryRepository->find($args['id']);
                        if ($category) {
                            return $category;
                        }
                    }

                    return null;
                },
                'categories' => function ($value, Argument $args) {
                    $categories = $this->categoryRepository->findAll();

                    return $categories;
                },
            ],
            'Mutation' => [
                'createCategory' => function($value, Argument $args) {
                    $category = new Category();
                    $category->setName($args['name']);

                    $this->entityManager->persist($category);
                    $this->entityManager->flush();

                    return $category;
                },
                'updateCategory' => function($value, Argument $args) {
                    $category = $this->categoryRepository->find($args['id']);

                    $category->setName($args['name']);
                    $this->entityManager->flush();

                    return $category;
                },
                'deleteCategory' => function($value, Argument $args) {
                    $category = $this->categoryRepository->find($args['id']);

                    $this->entityManager->remove($category);
                    $this->entityManager->flush();

                    return ["id" => $args['id']];
                },
            ],
        ];
    }


}