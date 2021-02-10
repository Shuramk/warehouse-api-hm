<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * Class CategoryController
 * @package App\Controller
 * @Route("/api", name="api/")
 *
 * @OA\Tag(name="Категории")
 */
class CategoryController extends ApiController
{

    /**
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     * @Route("/categories", name="categories", methods={"GET"})
     *
     * @OA\Get(path="/api/categories",
     *   operationId="getCategories",
     *   summary="Список всех (созданных к данном моменту) категорий",
     *     @OA\Response(
     *         response=200,
     *         description="Categories response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Category")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     )
     * )
     *
     */
    public function getCategories(CategoryRepository $categoryRepository): JsonResponse
    {
        $data = $categoryRepository->findAll();
        return $this->response($data);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @Route("/categories", name="categories_add", methods={"POST"})
     *
     * @OA\Post (path="/api/categories",
     *   operationId="addCategory",
     *   summary="Добавление категории",
     *   @OA\RequestBody(
     *         description="Category to add to the warehouse",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/NewCategory")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Add category response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Category")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error"
     *     )
     * )
     *
     */
    public function addCategory(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        try{
            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('name')){
                throw new Exception();
            }

            $category = new Category();
            $category->setName($request->get('name'));
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->response($category);

        }catch (Exception $e){
            return $this->respondValidationError("Data no valid");
        }

    }

    /**
     * @param CategoryRepository $categoryRepository
     * @param $id
     * @return JsonResponse
     * @Route("/categories/{id}", name="categories_get", methods={"GET"})
     *
     * @OA\Get(path="/api/categories/{id}",
     *   operationId="getCategory",
     *   summary="Получить категорию по id",
     *     @OA\Response(
     *         response=200,
     *         description="Category response",
     *         @OA\JsonContent(
     *             @OA\Items(ref="#/components/schemas/Category")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     )
     * )
     *
     */
    public function getCategory(CategoryRepository $categoryRepository, $id): JsonResponse
    {
        $category = $categoryRepository->find($id);

        if (!$category){
            return $this->respondNotFound("Category not found");
        }
        return $this->response($category);

    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param CategoryRepository $categoryRepository
     * @param $id
     * @return JsonResponse
     * @Route("/categories/{id}", name="categories_put", methods={"PUT"})
     *
     * @OA\Put (path="/api/categories/{id}",
     *   operationId="updateCategory",
     *   summary="Изменить категорию",
     *     @OA\RequestBody(
     *         description="updateCategory",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/NewCategory")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category response",
     *         @OA\JsonContent(
     *             @OA\Items(ref="#/components/schemas/Category")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     )
     * )
     *
     */
    public function updateCategory(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, $id): JsonResponse
    {

        try {
            $category = $categoryRepository->find($id);

            if (!$category) {
                return $this->respondNotFound("Category not found");
            }

            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('name')) {
                throw new Exception();
            }

            $category->setName($request->get('name'));

            $entityManager->flush();

            return $this->response($category);

        } catch (Exception $e) {
            return $this->respondValidationError("Data no valid");
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param CategoryRepository $categoryRepository
     * @param $id
     * @return JsonResponse
     * @Route("/categories/{id}", name="categories_delete", methods={"DELETE"})
     *
     * @OA\Delete  (path="/api/categories/{id}",
     *   operationId="deleteCategory",
     *   summary="Удалить категорию",
     *     @OA\Response(
     *         response=204,
     *         description="Category deleted",
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     )
     * )
     *
     */
    public function deleteCategory(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, $id): JsonResponse
    {
        $category = $categoryRepository->find($id);

        if (!$category){
            return $this->respondNotFound("Category not found");
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $this->setStatusCode(204);
        return $this->response("Category removed successfully");
    }

}
