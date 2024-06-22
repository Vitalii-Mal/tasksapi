<?php

namespace App\Enum;

enum ResponseMessageEnum: string
{
    // Common messages
    case TOKEN = 'Token';
    case USER_FIELDS_REQUIRED = 'Field "username/password" is required';
    case INVALID_CREDENTIALS = 'Invalid credentials';
    case INTERNAL_SERVER_ERROR = 'An unexpected error occurred';
    case JWT_NOT_FOUND = 'JWT Token not found';
    case JWT_EXPIRED = 'Expired JWT Token';
    case JWT_INVALID = 'Invalid JWT Token';
    case INVALID_JSON = 'Invalid JSON data';

    // Message for user
    case USER_NOT_FOUND = 'User not found';
    case USER_CREATED_SUCCESSFULLY = 'User created successfully';
    case USER_UPDATED_SUCCESSFULLY = 'User updated successfully';
    case USER_DELETED_SUCCESSFULLY = 'User deleted successfully';
    case USERS_LIST = 'Returns the list of users';
    case USER_DETAILS = 'Returns the user details';
    case LOGGED_USER_DETAILS = 'Returns the logged-in user details';
    case USER_ALREADY_EXIST = 'User already exists with the name';
    case USER_HAS_TASKS = 'User has tasks and cannot be deleted';
    case USER_IS_NOT_AUTHORIZED = 'User is not authorized';

    // Message for task
    case TASK_NOT_FOUND = 'Task not found';
    case PARENT_TASK_NOT_FOUND = 'Parent task with not found';
    case PARENT_TASK_BELONG_ANOTHER_USER = 'Parent task belong another user';
    case TASK_CREATED_SUCCESSFULLY = 'Task created successfully';
    case TASK_UPDATED_SUCCESSFULLY = 'Task updated successfully';
    case TASK_COMPLETED_SUCCESSFULLY = 'Task marked as completed successfully';
    case TASK_DELETED_SUCCESSFULLY = 'Task deleted successfully';
    case TASKS_LIST = 'Returns the list of tasks';
    case TASK_TREE = 'Returns the task tree';
    case TASK_FIELD_REQUIRED = 'Field "title/description/status/priority" is required';
    case CANNOT_COMPLETE_TASK = 'Cannot complete task with incomplete subtasks';
    case CANNOT_DELETE_COMPLETED_TASK = 'Cannot delete a completed task';
    case TASK_HAS_SUBTASKS = 'Task has subtasks';
    case TASK_IS_ALREADY_COMPLETED = 'Task is already completed';
}
