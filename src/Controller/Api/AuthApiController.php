<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Traits\ResponseTrait;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthApiController extends AbstractController
{
    use ResponseTrait;

    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate the required fields
        if (empty($data['email']) || empty($data['password'])) {
            return $this->json(['error' => 'Missing required fields: email, password!'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Create a new user and set their data
        $user = new User();
        $user->setEmail($data['email']);
       
        // Hash the password and set it on the user entity
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Set the default role (you can modify this as needed)
        $user->setRoles(['ROLE_USER']);  // Optional: Set a default role

        // Persist the user to the database
        $entityManager->persist($user);
        $entityManager->flush();

        $userData = [
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        // Return a successful response with the user data
        return $this->successResponseWithData('User registered successfully', $userData);
    }
}
