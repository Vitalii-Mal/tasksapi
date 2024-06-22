<?php

namespace App\Enum;

enum TaskKeysEnum: string
{
    case ID = 'id';
    case TITLE = 'title';
    case DESCRIPTION = 'description';
    case STATUS = 'status';
    case PRIORITY = 'priority';

    // Both 'parentTaskId' and 'parentTask' correspond to the same database column
    // and should be synchronized if either is changed.
    case PARENT_TASK_ID = 'parentTaskId';
    case PARENT_TASK = 'parentTask';


    case COMPLETED_AT = 'completedAt';

    // Both 'createdAt' and 'created_at' correspond to the same database column
    // and should be synchronized if either is changed.
    case CREATED_AT = 'createdAt';
    case CREATED_AT_INDEX = 'created_at';

    case SUBTASKS = 'subtasks';

    case USER_ID_INDEX = 'user_id';
}
