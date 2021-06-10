<?php
namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * ,错误时可能会有code属性：代表具体的错误编号
 * ,同时result对象中会有code属性：代表具体的错误编号
 *
 * 文档基本信息（@  OA\Info全局唯一）
 * @OA\Info(
 *      version="1.0",
 *      title="接口说明文档",
 *      description="对外开放接口;<br/>返回数据<hr/>apistatus：1代表成功，其它非1（具体的错误编号）代表失败；<br/>result：成功时的各种数据；<br/>errorMsg：失败时的具体文字描述。",
 *      @OA\Contact(
 *          name="邹燕开发支持",
 *          url="http://xueyuanjun.com",
 *          email="305463219@qq.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 */

/**
 * Api路径前缀（可多个）
 * @OA\Server(
 *      url="http://qualitycontrol.admin.cunwo.net",
 *      description="开发环境"
 * )
 *
 *  @OA\Server(
 *      url="https://projects.dev/api/v1",
 *      description="测试环境"
 * )
 *
 *  @OA\Server(
 *      url="https://projects.dev/api/v2",
 *      description="正式环境"
 * )
 */

/**
 * 额外文档  可用于Tag中
 * 外部文件
 * @  OA\ExternalDocumentation(
 *     description="Find out more about Swagger",
 *     url="http://swagger.io"
 * )
 */
/**
 * 接口安全中间件，L5-swagger不能使用，只能配置文件中配置
 * @  OA\SecurityScheme(
 *    type="oauth2",
 *    description="Use a global client_id / client_secret and your username / password combo to obtain a token",
 *    name="Password Based",
 *    in="header",
 *    scheme="https",
 *    securityScheme="Password Based",
 *    @  OA\Flow(
 *        flow="password",
 *        authorizationUrl="/oauth/authorize",
 *        tokenUrl="/oauth/token",
 *        refreshUrl="/oauth/token/refresh",
 *        scopes={}
 *    )
 * )
 */

/**
 *
 * @  OA\SecurityScheme(
 *      type="oauth2",
 *      description="Use a global client_id / client_secret and your email / password combo to obtain a token",
 *      name="passport",
 *      in="header",
 *      scheme="http",
 *     securityScheme="passport",
 *     @  OA\Flow(
 *         flow="password",
 *         authorizationUrl="/oauth/authorize",
 *         tokenUrl="/oauth/token",
 *         refreshUrl="/oauth/token/refresh",
 *         scopes={}
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     in="header",
 *     securityScheme="bearerAuth"
 * )
 *
 * @OA\SecurityScheme(
 *   securityScheme="api_key",
 *   type="apiKey",
 *   in="header",
 *   name="api_key"
 * )
 *
 */

/**
 * 标签
 * @  OA\Tag(
 *     name="project",
 *     description="Everything about your Projects",
 *     @  OA\ExternalDocumentation(
 *         description="Find out more",
 *         url="http://swagger.io"
 *     )
 * )
 *
 * @  OA\Tag(
 *     name="user",
 *     description="Operations about user",
 *     @  OA\ExternalDocumentation(
 *         description="Find out more about",
 *         url="http://swagger.io"
 *     )
 * )
 */

/**
 * @OA\Tag(
 *     name="帐号注册登录",
 *     description="进行帐号注册登录的相关接口",
 * )
 *
 */

/**
 * @  OA\Get(
 *      path="/projects",
 *      operationId="getProjectsList",
 *      tags={"Projects","user"},
 *      summary="Get list of projects",
 *      description="Returns list of projects",
 *      @  OA\Response(
 *          response=200,
 *          description="successful operation"
 *       ),
 *       @  OA\Response(response=400, description="Bad request"),
 *       security={
 *           {"api_key_security_example": {}}
 *       }
 *     )
 *
 * Returns list of projects
 */

/**
 * @  OA\Get(
 *      path="/projects/{id}",
 *      operationId="getProjectById",
 *      tags={"Projects"},
 *      summary="Get project information",
 *      description="Returns project data",
 *      @  OA\Parameter(
 *          name="id",
 *          description="Project id",
 *          required=true,
 *          in="path",
 *          @  OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @  OA\Response(
 *          response=200,
 *          description="successful operation"
 *       ),
 *      @  OA\Response(response=400, description="Bad request"),
 *      @  OA\Response(response=404, description="Resource Not Found"),
 *      security={
 *         {
 *             "oauth2_security_example": {"write:projects", "read:projects"}
 *         }
 *     },
 * )
 */
class OASController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/show",
     *     tags={"XXAPI"},
     *     summary="获取时间接口",
     *     description="获取时间接口",
     *     operationId="TimeShow",
     *     deprecated=false,
     *     @OA\Parameter(
     *         name="access_token",
     *         in="query",
     *         description="用户授权",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="操作成功返回"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="发生错误"
     *     )
     * )
     */
    public function show()
    {
        echo date('Y-m-d H:i:s', time());
    }

    /**
     * @ OA\Get(
     *     path="/api/hello",
     *     tags={"XXAPI"},
     *     summary="说你好接口",
     *     description="说你好接口",
     *     operationId="SayHello",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/common_Parameter_access_token"),
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/common_Parameter_field"),
     *     @ OA\Parameter(ref="#/components/parameters/common_Parameter_keyword"),
     *     @ OA\Parameter(ref="#/components/parameters/common_Parameter_page"),
     *     @ OA\Parameter(ref="#/components/parameters/common_Parameter_pagesize"),
     *     @ OA\Response(response=200,ref="#/components/responses/Response_RunBuy_list_brands"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_RunBuy_multi_brands"}
     */
    public function hello()
    {
        echo "hello";
    }

    /**
     * @  OA\Get(
     *     path="/",
     *     operationId="getTaskList",
     *     tags={"Tasks"},
     *     summary="Get list of tasks",
     *     description="Returns list of tasks",
     *     @  OA\Parameter(
     *         name="Accept",
     *         description="Accept header to specify api version",
     *         required=false,
     *         in="header",
     *         @  OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @  OA\Parameter(
     *         name="page",
     *         description="The page num of the list",
     *         required=false,
     *         in="query",
     *         @  OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @  OA\Parameter(
     *         name="limit",
     *         description="The item num per page",
     *         required=false,
     *         in="query",
     *         @  OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @  OA\Response(
     *         response=200,
     *         description="The result of tasks"
     *     ),
     *     security={
     *         {"passport": {}},
     *     }
     * )
     */
//    public function index(Request $request)
//    {
//        $limit = $request->input('limit') ? : 10;
//        // 获取认证用户实例
//        $user = $request->user('api');
//        $tasks = Task::where('user_id', $user->id)->paginate($limit);
//        return $this->response->paginator($tasks, new TaskTransformer());
//    }

    /**
     * @  OA\Get(
     *     path="/{id}",
     *     operationId="getTaskItem",
     *     tags={"Tasks"},
     *     summary="Get Task",
     *     description="Get specify task by id",
     *     @  OA\Parameter(
     *         name="Accept",
     *         description="Accept header to specify api version",
     *         required=false,
     *         in="header",
     *         @  OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @  OA\Parameter(
     *         name="id",
     *         description="The id of the task",
     *         required=true,
     *         in="path",
     *         @  OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @  OA\Response(
     *         response=200,
     *         description="The task item",
     *         @  OA\JsonContent(ref="#/components/schemas/task-transformer")
     *     ),
     *     @  OA\Response(
     *         response=404,
     *         description="404 not found"
     *     ),
     *     security={
     *         {"passport": {}},
     *     }
     * )
     *
     */
//    public function show($id)
//    {
//        $task = Task::findOrFail($id);
//        return $this->response->item($task, new TaskTransformer());
//    }

    /**
     * @  OA\Post(
     *     path="/",
     *     operationId="newTaskItem",
     *     tags={"Tasks"},
     *     summary="Add New Task",
     *     description="create new task",
     *     @  OA\Parameter(
     *         name="Accept",
     *         description="Accept header to specify api version",
     *         required=false,
     *         in="header",
     *         @  OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @  OA\RequestBody(
     *         request="text",
     *         required=true,
     *         description="The text of the task",
     *         @  OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @  OA\RequestBody(
     *         request="is_completed",
     *         required=true,
     *         description="If the task is completed or not",
     *         @  OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @  OA\Response(
     *         response=200,
     *         description="The task item created",
     *         @  OA\JsonContent(ref="#/components/schemas/task-transformer")
     *     ),
     *     security={
     *         {"passport": {}},
     *     }
     * )
     */
    public function store(CreateTaskRequest $request)
    {
        $request->validate([
            'text' => 'required|string'
        ]);

        $task = Task::create([
            'text' => $request->post('text'),
            'user_id' => auth('api')->user()->id,
            'is_completed' => Task::NOT_COMPLETED
        ]);

        return $this->response->item($task, new TaskTransformer());
    }
    /**
     * @  OA\Put(
     *     path="/{id}",
     *     operationId="updateTaskItem",
     *     tags={"Tasks"},
     *     summary="Update Task",
     *     description="update existed task by id",
     *     @  OA\Parameter(
     *         name="Accept",
     *         description="Accept header to specify api version",
     *         required=false,
     *         in="header",
     *         @  OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @  OA\Parameter(
     *         name="id",
     *         description="The id of the task",
     *         required=true,
     *         in="path",
     *         @  OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @  OA\RequestBody(
     *         request="task_in_body",
     *         required=true,
     *         description="The task to update",
     *         @  OA\JsonContent(
     *             ref="#/components/schemas/task-model"
     *         )
     *     ),
     *     @  OA\Response(
     *         response=200,
     *         description="The task item updated",
     *         @  OA\JsonContent(ref="#/components/schemas/task-transformer")
     *     ),
     *     @  OA\Response(
     *         response=404,
     *         description="404 not found"
     *     ),
     *     security={
     *         {"passport": {}},
     *     }
     * )
     */
//    public function update(Request $request, $id)
//    {
//        $task = Task::findOrFail($id);
//        $updatedTask = tap($task)->update(request()->only(['is_completed', 'text']))->fresh();
//        return $this->response->item($updatedTask, new TaskTransformer());
//    }

    /**
     * @  OA\Delete(
     *     path="/{id}",
     *     operationId="deleteTaskItem",
     *     tags={"Tasks"},
     *     summary="Delete Task",
     *     description="delete existed task by id",
     *     @  OA\Parameter(
     *         name="Accept",
     *         description="Accept header to specify api version",
     *         required=false,
     *         in="header",
     *         @  OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @  OA\Parameter(
     *         name="id",
     *         description="The id of the task",
     *         required=true,
     *         in="path",
     *         @  OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @  OA\Response(
     *         response=200,
     *         description="The task is deleted successful"
     *     ),
     *     @  OA\Response(
     *         response=404,
     *         description="404 not found"
     *     ),
     *     security={
     *         {"passport": {}},
     *     }
     * )
     */
//    public function destroy($id)
//    {
//        $task = Task::findOrFail($id);
//        $task->delete();
//        return response()->json(['message' => 'Task deleted'], 200);
//    }
}
