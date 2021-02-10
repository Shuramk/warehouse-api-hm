<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;



/**
 * Class ProductController
 * @package App\Controller
 * @Route("/api", name="api/")
 *
 * @OA\Tag(name="Продукция")
 *
 */
class ProductController extends ApiController
{

    /**
     * @param ProductRepository $productRepository
     * @return JsonResponse
     * @Route("/products", name="products", methods={"GET"})
     *
     * @OA\Get(path="/api/products",
     *   operationId="getProducts",
     *   summary="Список всех (созданных к данному моменту) продуктов",
     *     @OA\Response(
     *         response=200,
     *         description="Products response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     )
     * )
     */
    public function getProducts(ProductRepository $productRepository): JsonResponse
    {

        $data = $productRepository->findAll();
        return $this->response($data);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     * @Route("/products", name="poducts_add", methods={"POST"})
     *
     * @OA\Post (path="/api/products",
     *   operationId="addProduct",
     *   summary="Добавление продукта",
     *   @OA\RequestBody(
     *         description="Product to add to the warehouse",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/NewProduct")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Add product response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error"
     *     )
     * )
     */
    public function addProduct(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): JsonResponse
    {

        try{
            $request = $this->transformJsonBody($request);
            if (!$request || !$request->get('name') || !$request->get('price')){
                throw new Exception();
            }

            $product = new Product();
            $product->setName($request->get('name'));
            $product->setDescription($request->get('description'));
            $product->setPrice($request->get('price'));
            $product->setQuantity($request->get('quantity'));
            $product->setCategory($categoryRepository->find($request->get('category')));
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->response($product);

        }catch (Exception $e){
            return $this->respondValidationError("Data no valid");
        }

    }

    /**
     * @param ProductRepository $productRepository
     * @param $id
     * @return JsonResponse
     * @Route("/products/{id}", name="products_get", methods={"GET"})
     *
     *  @OA\Get(path="/api/products/{id}",
     *   operationId="getProduct",
     *   summary="Получить продукт по id",
     *     @OA\Response(
     *         response=200,
     *         description="Products response",
     *         @OA\JsonContent(
     *             @OA\Items(ref="#/components/schemas/Product")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     )
     * )
     */
    public function getProduct(ProductRepository $productRepository, $id): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product){
            return $this->respondNotFound("Product not found");

        }
        return $this->response($product);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     * @param $id
     * @return JsonResponse
     * @Route("/products/{id}", name="products_put", methods={"PUT"})
     *
     * * @OA\Put (path="/api/products/{id}",
     *   operationId="updateProduct",
     *   summary="Изменить продукт",
     *     @OA\RequestBody(
     *         description="updateProduct",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/NewProduct")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product response",
     *         @OA\JsonContent(
     *             @OA\Items(ref="#/components/schemas/Product")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     )
     * )
     */
    public function updateProduct(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, ProductRepository $productRepository, $id): JsonResponse
    {

        try {
            $product = $productRepository->find($id);

            if (!$product) {
                return $this->respondNotFound("Product not found");
            }

            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('name')) {
                throw new Exception();
            }

            $product->setName($request->get('name'));
            $product->setDescription($request->get('description'));
            $product->setPrice($request->get('price'));
            $product->setQuantity($request->get('quantity'));
            $product->setCategory($categoryRepository->find($request->get('category')));
            $entityManager->flush();

            return $this->response($product);

        } catch (Exception $e) {
            return $this->respondValidationError("Data no valid");
        }
    }


    /**
     * @param EntityManagerInterface $entityManager
     * @param ProductRepository $productRepository
     * @param $id
     * @return JsonResponse
     * @Route("/products/{id}", name="products_delete", methods={"DELETE"})
     *
     * @OA\Delete  (path="/api/products/{id}",
     *   operationId="deleteProduct",
     *   summary="Удалить продукт",
     *     @OA\Response(
     *         response=204,
     *         description="Product deleted",
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *     )
     * )
     */
    public function deleteProduct(EntityManagerInterface $entityManager, ProductRepository $productRepository, $id): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product){
            return $this->respondNotFound("Product not found");
        }

        $entityManager->remove($product);
        $entityManager->flush();

        $this->setStatusCode(204);
        return $this->response("Product removed successfully");
    }

}
