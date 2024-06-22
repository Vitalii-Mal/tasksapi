# Todo List API

## Project Description
This is a REST API for managing users and tasks. It allows users to perform various operations on tasks, such as creating, editing, marking as completed, and deleting tasks. Users can also be managed through this API.

## Introduction
The Todo List API is designed to help developers manage tasks and users efficiently. It is built with scalability and flexibility in mind, allowing for easy integration with other applications.

## Environment Requirements
This project utilizes Docker containers for its environment setup.

## Prerequisites
Ensure you have the following installed:
- Docker
- Docker Compose
- PHP 8.2 or higher
- Composer

## Download and Setup Instructions
Follow these steps to download and set up the project:
1. Clone the repository from Git.
    ```sh
    git clone https://github.com/Vitalii-Mal/tasksapi.git
    cd tasksapi
    ```
2. Install dependencies.
    ```sh
    composer install
    ```
3. Start the Docker containers.
    ```sh
    docker-compose up -d
    ```
4. Create the database.
    ```sh
    php bin/console doctrine:database:create
    ```
5. Make migration.
    ```sh
    mkdir migrations
    php bin/console make:migration
    ```
6. Migrate migration.
    ```sh
    php bin/console doctrine:migrations:migrate
    ```
7. Populate the database with sample data.
    ```sh
    php bin/console doctrine:fixtures:load
    ```

## Running Tests
To run tests, use the following command:
```sh
php bin/phpunit
```

## Using the API

This API provides endpoints for managing users and tasks. Below is the documentation for the available endpoints:

### Users Endpoints

- **Get list of users**
    - **Endpoint**: `GET /v1/api/users`
    - **Response Format for Success**: `['status' => 'success', 'data' => {user_data}]`
    - **Response Format for Error**: `['status' => 'error', 'message' => {error_message}]`

- **Create a new user**
    - **Endpoint**: `POST /v1/api/user`
    - **Response Format for Success**: `['status' => 'success', 'data' => {created_user_data}]`
    - **Response Format for Error**: `['status' => 'error', 'message' => {error_message}]`

- **Get user details**
    - **Endpoint**: `GET /v1/api/user/{id}`
    - **Response Format for Success**: `['status' => 'success', 'data' => {user_data}]`
    - **Response Format for Error**: `['status' => 'error', 'message' => {error_message}]`

- **Update user details**
    - **Endpoint**: `PUT /v1/api/user/{id}`
    - **Response Format for Success**: `['status' => 'success', 'data' => {updated_user_data}]`
    - **Response Format for Error**: `['status' => 'error', 'message' => {error_message}]`

- **Delete a user**
    - **Endpoint**: `DELETE /v1/api/user/{id}`
    - **Response Format for Success**: `['status' => 'success', 'message' => 'User deleted successfully']`
    - **Response Format for Error**: `['status' => 'error', 'message' => {error_message}]`

### Tasks Endpoints

- **Get list of tasks with optional filters and sorting**
    - **Endpoint**: `GET /v1/api/tasks`
    - **Description**: Get a list of tasks with optional filtering and sorting.
    - **Parameters**:
        - `status`: Filter by task status (e.g., "todo", "done").
        - `priority`: Filter by task priority (1 to 5).
        - `title`: Filter by task title (full-text search).
        - `description`: Filter by task description (full-text search).
    - **Sorting**:
        - `createdAt`: Sort by creation date (e.g., "asc", "desc").
        - `completedAt`: Sort by completion date (e.g., "asc", "desc").
        - `priority`: Sort by task priority (e.g., "asc", "desc").
        - You can sort by multiple fields by providing multiple sorting parameters. For example, `sorts[priority]=desc&sorts[createdAt]=asc`.
    - **Example**: `GET /v1/api/tasks?status=done&sorts[completedAt]=desc`
    - **Response Format for Success**: `['status' => 'success', 'data' => {task_data}]`
    - **Response Format for Error**: `['status' => 'error', 'message' => {error_message}]`

- **Create a new task**
    - **Endpoint**: `POST /v1/api/tasks`
    - **Response Format for Success**: `['status' => 'success', 'data' => {created_task_data}]`
    - **Response Format for Error**: `['status' => 'error', 'message' => {error_message}]`

- **Update a task**
    - **Endpoint**: `PUT /v1/api/tasks/{id}`
    - **Response Format for Success**: `['status' => 'success', 'data' => {updated_task_data}]`
    - **Response Format for Error**: `['status' => 'error', 'message' => {error_message}]`

- **Delete a task**
    - **Endpoint**: `DELETE /v1/api/tasks/{id}`
    - **Response Format for Success**: `['status' => 'success', 'message' => 'Task deleted successfully']`
    - **Response Format for Error**: `['status' => 'error', 'message' => {error_message}]`

## API Authentication

To authenticate and obtain an access token for API requests, follow these steps:

### Request Token

- **Endpoint:** `POST /v1/api/login_check`
- **Request Body:**

```json
{
  "username": "User1",
  "password": "1"
}
```

- **Response Format for Success:**

```json
{
  "status": "success",
  "data": "{your_access_token}"
}
```

- **Response Format for Error:**

```json
{
  "status": "error",
  "message": "{error_message}"
}
```

## Error Handling

Errors are returned in the following format:

```json
{
  "status": "error",
  "message": "Error message here"
}
```

## Examples

Here are some example requests and responses:

### Get List of Users

**Request:**

```sh
curl -X GET "http://localhost:8000/v1/api/users" -H "accept: application/json"
```

**Response:**

```json
{
  "status": "success",
  "data": [...]
}
```

### Create a New Task

**Request:**

```sh
curl -X POST "http://localhost:8000/v1/api/tasks" -H "accept: application/json" -H "Content-Type: application/json" -d "{ \"title\": \"New Task\", \"description\": \"Task description\", \"priority\": 3 }"
```

**Response:**

```json
{
  "status": "success",
  "data": { "id": 1, "title": "New Task", "description": "Task description", "priority": 3, ... }
}
```

### Obtain Access Token

**Request:**

```sh
curl -X POST "http://localhost:8000/v1/api/user" -H "accept: application/json" -H "Content-Type: application/json" -d "{ \"username\": \"User1\", \"password\": \"1\" }"
```

**Response:**

```json
{
  "status": "success",
  "data": "{your_access_token}"
}
```