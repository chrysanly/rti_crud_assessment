<?php

namespace app\controllers;

use app\models\Tag;
use Yii;
use app\models\Task;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\User;

class TaskController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        return $behaviors;
    }

    public function actionIndex()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;

        $params = [
            'showDeleted' => filter_var($request->get('showDeleted', false), FILTER_VALIDATE_BOOLEAN),
            'hideDeleted' => filter_var($request->get('hideDeleted', false), FILTER_VALIDATE_BOOLEAN),
            'status' => $request->get('status'),
            'priority' => $request->get('priority'),
            'from' => $request->get('from'),
            'to' => $request->get('to'),
            'keyword' => $request->get('keyword'),
            'sort' => $request->get('sort', 'created_at'),
            'order' => $request->get('order', 'DESC'),
            'page' => (int) $request->get('page', 0),
            'limit' => (int) $request->get('limit', 10),
        ];

        $tasksData = Task::getTasks($params);

        $tasksWithTags = [];
        foreach ($tasksData['items'] as $task) {
            $taskArray = $task->attributes ?? $task; // support array from getTasks
            $taskArray['tags'] = $task instanceof \app\models\Task
                ? $task->getTags()->select(['id', 'name'])->asArray()->all()
                : [];
            $tasksWithTags[] = $taskArray;
        }
        $tasksData['items'] = $tasksWithTags;

        // Get all tags
        $allTags = \app\models\Tag::find()
            ->select(['id', 'name'])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();

        return [
            'tasks' => $tasksData,
            'allTags' => $allTags,
        ];
    }


    public function actionView($id)
{
    $task = Task::find()
        ->where(['id' => $id, 'is_deleted' => false])
        ->with('tags')  // eager-load tags
        ->one();

    if (!$task) {
        throw new NotFoundHttpException("Task not found");
    }

    // Optionally format the response to include tags
    return [
        'id' => $task->id,
        'title' => $task->title,
        'status' => $task->status,
        'priority' => $task->priority,
        'due_date' => $task->due_date,
        'tags' => array_map(function($tag) {
            return ['id' => $tag->id, 'name' => $tag->name];
        }, $task->tags),  // include tags as array
    ];
}


    public function actionCreate()
    {
        $task = new Task();
        $task->load(Yii::$app->request->post(), '');

        if ($task->validate() && $task->save()) {
            $this->logAction($task->id, 'created', json_encode($task->attributes));

            // Get tags as array
            $tagIds = Yii::$app->request->post('tags', []); // default empty array

            if (!empty($tagIds) && is_array($tagIds)) {
                foreach ($tagIds as $tagId) {
                    $tag = Tag::findOne($tagId);
                    if ($tag) {
                        $task->link('tags', $tag);
                    }
                }
            }

            Yii::$app->response->statusCode = 201;
            return $task;
        }

        Yii::$app->response->statusCode = 422;
        return $task->errors;
    }



   public function actionUpdate($id)
{
    $task = Task::findOne(['id' => $id, 'is_deleted' => false]);
    if (!$task) throw new NotFoundHttpException("Task not found");

    // Load posted data
    $task->load(Yii::$app->request->bodyParams, '');
    
    if ($task->validate() && $task->save()) {

        $this->logAction($task->id, 'updated', json_encode($task->attributes));

        // Handle tags (like in create)
        $tagIds = Yii::$app->request->post('tags', []); // array of tag IDs
        if (!empty($tagIds) && is_array($tagIds)) {

            // Unlink all existing tags first
            $task->unlinkAll('tags', true);

            // Link new tags
            foreach ($tagIds as $tagId) {
                $tag = Tag::findOne($tagId);
                if ($tag) {
                    $task->link('tags', $tag);
                }
            }
        }

        Yii::$app->response->statusCode = 200;
        return $task;
    }

    Yii::$app->response->statusCode = 422;
    return $task->errors;
}


    public function actionDelete($id)
    {
        $task = Task::findOne(['id' => $id, 'is_deleted' => false]);
        if (!$task) throw new NotFoundHttpException("Task not found");

        $task->is_deleted = true;
        $task->save(false);
        $this->logAction($task->id, 'deleted', json_encode($task->attributes));

        return ['message' => 'Task deleted successfully'];
    }

    public function actionRetrieve($id)
    {
        $task = Task::findOne(['id' => $id, 'is_deleted' => true]);
        if (!$task) {
            throw new NotFoundHttpException("Task not found or not deleted");
        }

        $task->is_deleted = false;
        $task->save(false);

        $this->logAction($task->id, 'retrieved', json_encode($task->attributes));

        return ['message' => 'Task retrieved successfully'];
    }

    protected function findModel($id)
    {
        if (($model = Task::findOne(['id' => $id, 'is_deleted' => false])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException("Task with ID $id not found.");
    }

    public function actionToggleStatus($id)
    {
        $task = Task::findOne(['id' => $id, 'is_deleted' => false]);
        if (!$task) throw new NotFoundHttpException("Task not found");

        $task->status = $task->status === 'completed' ? 'pending' : 'completed';
        $task->save(false);
        $this->logAction($task->id, 'status_toggle', $task->status);

        return $task;
    }

    private function logAction($taskId, $action, $data)
    {
        Yii::$app->db->createCommand()->insert('{{%audit_log}}', [
            'task_id' => $taskId,
            'action' => $action,
            'data' => $data
        ])->execute();
    }
}
