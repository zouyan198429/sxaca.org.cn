<?php
namespace App\ModelsVerify;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;
/**
 * @  OA\Schema(
 *     schema="task-model",
 *     type="object",
 *     title="Task model"
 * )
 */
class Task extends Model
{
    /**
     * The id of the task
     * @var integer
     * @  OA\Property(format="int64", example=1)
     */
    public $id;

    /**
     * The text of the task
     * @var string
     * @  OA\Property(format="string", example="Test Task")
     */
    public $text;

    /**
     * If the task is completed or not
     * @var string
     * @  OA\Property(format="boolean", example="1")
     */
    public $is_completed;
}