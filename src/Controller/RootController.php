<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RootController extends AbstractController
{
    #[Route('/', name: 'root', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Welcome to the API. Please refer to the API documentation for usage details.',
            'available_routes' => [
                'GET /v1/api/users' => 'Get list of users',
                'POST /v1/api/user' => 'Create a new user',
                'GET /v1/api/user/{id}' => 'Get a user by ID',
                'PUT /v1/api/user/{id}' => 'Update a user',
                'DELETE /v1/api/user/{id}' => 'Delete a user',
                'GET /v1/api/tasks' => 'Get list of tasks',
                'POST /v1/api/tasks' => 'Create a new task',
                'PUT /v1/api/tasks/{id}' => 'Update a task',
                'DELETE /v1/api/tasks/{id}' => 'Delete a task',
                'PATCH /v1/api/tasks/{id}/complete' => 'Complete a task',
                'GET /v1/api/tasks/tree' => 'Get task tree',
                'POST /v1/api/login_check' => 'User login check',
            ],
            'filtering_and_sorting' => [
                'GET /v1/api/tasks' => [
                    'description' => 'Get list of tasks with optional filters and sorting',
                    'parameters' => [
                        'filters' => [
                            'status' => 'Filter by task status (e.g., "todo", "done")',
                            'priority' => 'Filter by task priority (1 to 5)',
                            'title' => 'Filter by task title (full-text search)',
                            'description' => 'Filter by task description (full-text search)',
                        ],
                        'sorting' => [
                            'sorts' => [
                                'createdAt' => 'Sort by creation date (e.g., "asc", "desc")',
                                'completedAt' => 'Sort by completion date (e.g., "asc", "desc")',
                                'priority' => 'Sort by task priority (e.g., "asc", "desc")',
                            ],
                            'multiple_fields' => 'You can sort by multiple fields by providing multiple sorting parameters. For example, sorts[priority]=desc&sorts[createdAt]=asc'
                        ]
                    ],
                    'example' => 'GET /v1/api/tasks?status=done&sorts[completedAt]=desc'
                ]
            ]
        ]);
    }
}
