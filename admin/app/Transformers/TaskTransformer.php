<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use OpenApi\Annotations as OA;
/**
 * @  OA\Schema(
 *      schema="task-transformer",
 *     type="object",
 *     title="Task Transformer"
 * )
 *
 */
class TaskTransformer extends TransformerAbstract
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
     * @  OA\Property(format="string", example="yes")
     */
    public $completed;
    /**
     * The URL of the task detail page
     * @var string
     * @  OA\Property(format="string", example="http://todo.test/dingoapi/task/1")
     */
    public $link;
}