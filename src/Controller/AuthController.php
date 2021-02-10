<?php

namespace App\Controller;

namespace App\Controller;


use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use OpenApi\Annotations as OA;


/**
 * Class AuthController
 * @package App\Controller
 *
 * @OA\Tag(name="Пользователи")
 */
class AuthController extends ApiController
{


    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     *
     * @OA\Post (path="/api/register",
     *     operationId="addUser",
     *     summary="Регистрация",
     *     @OA\RequestBody(
     *         description="Новый пользователь",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property (
     *                      property="name",
     *                      type="string",
     *                  ),
     *                  @OA\Property (
     *                      property="username",
     *                      type="string",
     *                  ),
     *                  @OA\Property (
     *                      property="password",
     *                      type="string",
     *                  ),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User successfully created",
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation errors"
     *      ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error"
     *     )
     * )
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);

        $username = $request->get('username');
        $password = $request->get('password');
        $name = $request->get('name');

        if (empty($username) || empty($password) || empty($name)){
            return $this->respondValidationError("Invalid Username or Password or Name");
        }

        $user = new User();
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setName($name);
        $user->setUsername($username);
        $em->persist($user);
        $em->flush();
        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getName()));
    }


}
