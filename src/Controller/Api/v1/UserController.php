<?php

namespace App\Controller\Api\v1;

use App\Attributes\OpenApi\Adapters as OA;
use App\DTO\DTOFactory;
use App\DTO\UserDTO;
use App\Entity\User;
use App\Enum\ResponseMessageEnum;
use App\Helpers\ControllerHelper;
use App\Service\ResponseService;
use App\Service\UserService;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/v1/api', name: 'user_')]
class UserController extends AbstractController
{
    use ControllerHelper;

    public function __construct(
        private readonly UserService $userService,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseService $responseService
    ) {
    }

    // Public access
    #[Route('/login_check', name: 'api_login_check', methods: ['POST'])]
    #[OA\PostAdapter(
        'User login check',
        UserDTO::class,
        null,
        [
            '200' => ResponseMessageEnum::TOKEN->value,
            '400' => ResponseMessageEnum::USER_FIELDS_REQUIRED->value,
            '401' => ResponseMessageEnum::INVALID_CREDENTIALS->value,
            '500' => ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
        ]
    )]
    public function loginCheck(Request $request): JsonResponse
    {
        $userDTO = $this->dtoFactory->createUserDTOFromRequest($request);
        $token = $this->userService->loginCheck($userDTO);

        return $this->responseService->jsonResponse($token);
    }

    // Public access
    #[Route('/user', name: 'create_user', methods: ['POST'])]
    #[OA\PostAdapter(
        'Create a new user',
        UserDTO::class,
        User::class,
        [
            '201' => ResponseMessageEnum::USER_CREATED_SUCCESSFULLY->value,
            '400' => ResponseMessageEnum::USER_FIELDS_REQUIRED->value . ' / ' .
                ResponseMessageEnum::USER_ALREADY_EXIST->value,
            '500' => ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
        ]
    )]
    public function createUser(Request $request): Response
    {
        $userDTO = $this->dtoFactory->createUserDTOFromRequest($request);
        $user = $this->userService->createUser($userDTO);

        return $this->responseService->jsonResponse($user, Response::HTTP_CREATED);

    }

    #[Route('/users', name: 'get_users', methods: ['GET'])]
    #[OA\GetAdapter(
        'Get list of users',
        User::class,
        [
            '200' => ResponseMessageEnum::USERS_LIST->value,
            '401' =>
                ResponseMessageEnum::JWT_NOT_FOUND->value . ' / ' .
                ResponseMessageEnum::JWT_EXPIRED->value . ' / ' .
                ResponseMessageEnum::JWT_INVALID->value,
            '500' => ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
        ],
        true
    )]
    #[Security(name: 'Bearer')]
    public function getUsers(): Response
    {
        $users = $this->userService->getAllUsers();

        return $this->responseService->jsonResponse($users);
    }

    #[Route('/user/{id}', name: 'get_user', methods: ['GET'])]
    #[OA\GetAdapter(
        'Get a user by ID',
        User::class,
        [
            '200' => ResponseMessageEnum::USER_DETAILS->value,
            '401' =>
                ResponseMessageEnum::JWT_NOT_FOUND->value . ' / ' .
                ResponseMessageEnum::JWT_EXPIRED->value . ' / ' .
                ResponseMessageEnum::JWT_INVALID->value,
            '404' => ResponseMessageEnum::USER_NOT_FOUND->value,
            '500' => ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
        ]
    )]
    #[Security(name: 'Bearer')]
    public function getUserById(int $id): Response
    {
        $user = $this->userService->getUserById($id);

        return $this->responseService->jsonResponse($user);
    }

    #[Route('/user', name: 'get_logged_user', methods: ['GET'])]
    #[OA\GetAdapter(
        'Get a user by ID',
        User::class,
        [
            '200' => ResponseMessageEnum::LOGGED_USER_DETAILS->value,
            '401' =>
                ResponseMessageEnum::JWT_NOT_FOUND->value . ' / ' .
                ResponseMessageEnum::JWT_EXPIRED->value . ' / ' .
                ResponseMessageEnum::JWT_INVALID->value,
            '500' => ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
        ]
    )]
    #[Security(name: 'Bearer')]
    public function getLoggedUser(): Response
    {
        $user = $this->getUserOrThrow();

        return $this->responseService->jsonResponse($user);
    }

    #[Route('/user/{id}', name: 'update_user', methods: ['PUT'])]
    #[OA\PutAdapter(
        'Update a user',
        UserDTO::class,
        User::class,
        [
            '201' => ResponseMessageEnum::USER_UPDATED_SUCCESSFULLY->value,
            '400' => ResponseMessageEnum::USER_FIELDS_REQUIRED->value . ' / ' .
                ResponseMessageEnum::USER_ALREADY_EXIST->value,
            '401' =>
                ResponseMessageEnum::JWT_NOT_FOUND->value . ' / ' .
                ResponseMessageEnum::JWT_EXPIRED->value . ' / ' .
                ResponseMessageEnum::JWT_INVALID->value,
            '404' => ResponseMessageEnum::USER_NOT_FOUND->value,
            '500' => ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
        ]
    )]
    #[Security(name: 'Bearer')]
    public function updateUser(int $id, Request $request): Response
    {
        $userDTO = $this->dtoFactory->createUserDTOFromRequest($request);
        $user = $this->userService->updateUser($id, $userDTO);

        return $this->responseService->jsonResponse($user);
    }

    #[Route('/user/{id}', name: 'delete_user', methods: ['DELETE'])]
    #[OA\DeleteAdapter(
        'Delete a user',
        null,
        [
            '200' => ResponseMessageEnum::USER_DELETED_SUCCESSFULLY->value,
            '401' =>
                ResponseMessageEnum::JWT_NOT_FOUND->value . ' / ' .
                ResponseMessageEnum::JWT_EXPIRED->value . ' / ' .
                ResponseMessageEnum::JWT_INVALID->value,
            '404' => ResponseMessageEnum::USER_NOT_FOUND->value,
            '500' => ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
        ]
    )]
    #[Security(name: 'Bearer')]
    public function deleteUser(int $id): Response
    {
        // Delete the user if there aren't related tasks
        $response = $this->userService->deleteUser($id);

        return $this->responseService->jsonResponse($response);
    }
}
