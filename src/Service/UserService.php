<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Enum\ResponseMessageEnum;
use App\Enum\UserKeysEnum;
use App\Exception\User\InvalidCredentialsException;
use App\Exception\User\UserAlreadyExistsException;
use App\Exception\User\UserHasTasksException;
use App\Exception\User\UserNotFoundException;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private TaskRepository $taskRepository,
        private JWTTokenManagerInterface $JWTManager
    ) {
    }

    public function loginCheck(UserDTO $userDTO): string
    {
        $user = $this->userRepository->findUserByUsername($userDTO->getUsername());
        if (!$user) {
            throw new UserNotFoundException();
        }

        if (!password_verify($userDTO->getPassword(), $user->getPassword())) {
            throw new InvalidCredentialsException();
        }

        return $this->JWTManager->create($user);
    }

    public function getAllUsers(): array
    {
        $users = $this->userRepository->findAll();

        if (empty($users)) {
            throw new UserNotFoundException();
        }

        return $users;
    }

    public function getUserById(int $id): User
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function createUser(UserDTO $userDTO): User
    {
        $userName = $userDTO->getUsername();

        $user = $this->userRepository->findBy([
            UserKeysEnum::USERNAME->value => $userName
        ]);

        if (!empty($user)) {
            throw new UserAlreadyExistsException(
                sprintf(
                    ResponseMessageEnum::USER_ALREADY_EXIST->value . ' %s ',
                    $userName
                )
            );
        }

        $newUser = new User();
        $newUser->setUsername($userDTO->getUsername());
        $newUser->setPassword(password_hash($userDTO->getPassword(), PASSWORD_DEFAULT));

        $this->entityManager->persist($newUser);
        $this->entityManager->flush();

        return $newUser;
    }

    public function updateUser(int $id, UserDTO $userDTO): User
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $userName = $user->getUsername();
        $newUserName = $userDTO->getUsername();

        if ($userName !== $newUserName) {
            $existsUser = $this->userRepository->findBy([
                UserKeysEnum::USERNAME->value => $newUserName
            ]);
            if (!empty($existsUser)) {
                throw new UserAlreadyExistsException(
                    sprintf(
                        ResponseMessageEnum::USER_ALREADY_EXIST->value . ' %s ',
                        $newUserName
                    )
                );
            }
        }

        $user->setUsername($newUserName);
        if (!empty($userDTO->getPassword())) {
            $user->setPassword(password_hash($userDTO->getPassword(), PASSWORD_DEFAULT));
        }

        $this->entityManager->flush();

        return $user;
    }

    public function deleteUser(int $id): string
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $tasks = $this->taskRepository->findBy([UserKeysEnum::USER->value => $user->getId()]);
        if (!empty($tasks)) {
            throw new UserHasTasksException();
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return ResponseMessageEnum::USER_DELETED_SUCCESSFULLY->value;
    }
}
