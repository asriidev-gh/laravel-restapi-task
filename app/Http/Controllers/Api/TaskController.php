<?php

namespace App\Http\Controllers\Api;

use App\Task;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;

/**
* @group Task API
*
* APIs for front page
*/
class TaskController extends Controller
{
    /**
     * Fetch Task
     * 
     * Display Task List
     * 
     * @authenticated
     * 
     * @responseFile responses/tasks.get.json
     * @responseFile 401 responses/401.json
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TaskResource::collection(auth()->user()->tasks()->latest()->paginate(4));
    }

     /**
     * Add Task
     * 
     * Store a newly created resource in storage.
     * 
     * @authenticated
     * 
     * @bodyParam title string required title of the task. Example: my first task
     * @bodyParam description text description of the task
     * @bodyParam due string date string. Example: next friday
     * 
     * @responseFile responses/tasks.store.json
     * @responseFile 401 responses/401.json
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255'
        ]);

        $input = $request->all();

        if ($request->has('due')) {
            $input['due'] = Carbon::parse($request->due)->toDateTimeString();
        }

        $task = auth()->user()->tasks()->create($input);

        return new TaskResource($task->load('creator'));
    }

    /**
     * Display the specified task.
     * 
     * @authenticated
     * 
     * @urlParam task required id of the task
     * 
     * @response {"data":{"id":5,"user_id":1,"title":"sec task","description":"desc of sec task","due":"2019-12-15 00:00:00","created_at":"2019-12-10 23:57:19","updated_at":"2019-12-10 23:57:19","creator":{"id":1,"name":"hitesh","email":"hitesh@gmail.com","email_verified_at":null,"created_at":"2019-10-17 03:46:51","updated_at":"2019-10-17 03:46:51"}}}
     * @responseFile 401 responses/401.json
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return new TaskResource($task->load('creator'));
    }

    /**
     * Update Specific Task
     *
     * To update the specific task.
     * 
     * @urlParam task required id of the task. Example: 1
     * 
     * @response {"data":[{
     *   "id": 1,
     *   "user_id": 1,
     *   "title": "update title new",
     *   "description": "updated desc new",
     *   "due": null,
     *   "created_at": "2020-10-31T06:33:06.000000Z",
     *   "updated_at": "2020-11-02T07:23:11.000000Z",
     *   "creator": {
     *       "id": 1,
     *       "name": "asriidev",
     *       "email": "asriidev@gm.com",
     *       "email_verified_at": null,
     *       "created_at": "2020-10-31T06:31:44.000000Z",
     *       "updated_at": "2020-10-31T06:31:44.000000Z"
     *   }
     *}]}     
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|max:255'
        ]);

        $input = $request->all();

        if ($request->has('due')) {
            $input['due'] = Carbon::parse($request->due)->toDateTimeString();
        }

        $task->update($input);

        return new TaskResource($task->load('creator'));
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response(['message' => 'Deleted!']);
    }
}
