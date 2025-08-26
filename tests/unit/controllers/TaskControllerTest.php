<?php

namespace tests\unit\controllers;

use app\controllers\TaskController;
use app\models\Task;
use app\models\Tag;
use Yii;
use yii\web\Request;
use yii\web\Response;
use yii\web\NotFoundHttpException;

class TaskControllerTest extends \PHPUnit\Framework\TestCase
{
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new TaskController('task', Yii::$app);
        
        // Make sure response is in JSON format
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function testIndexReturnsTasksWithTags()
    {
        $result = $this->controller->actionIndex();

        $this->assertArrayHasKey('tasks', $result);
        $this->assertArrayHasKey('allTags', $result);

        // Check tasks structure
        if (!empty($result['tasks']['items'])) {
            $task = $result['tasks']['items'][0];
            $this->assertArrayHasKey('id', $task);
            $this->assertArrayHasKey('title', $task);
            $this->assertArrayHasKey('tags', $task);
        }
    }

    public function testCreateTask()
    {
        // Create a tag first
        $tag = new Tag(['name' => 'UnitTestTag']);
        $this->assertTrue($tag->save());

        // Mock POST data
        Yii::$app->request->setBodyParams([
            'title' => 'Unit Test Task',
            'status' => 'pending',
            'priority' => 'low',
            'tags' => [$tag->id],
            'due_date' => '2025-12-31'
        ]);

        $task = $this->controller->actionCreate();
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Unit Test Task', $task->title);
        $this->assertNotEmpty($task->tags);
    }

    public function testViewTask()
    {
        $task = Task::find()->one();
        if (!$task) {
            $this->markTestSkipped('No tasks found to test view.');
        }

        $result = $this->controller->actionView($task->id);
        $this->assertEquals($task->id, $result['id']);
        $this->assertArrayHasKey('tags', $result);
    }

    public function testUpdateTask()
    {
        $task = Task::find()->one();
        if (!$task) {
            $this->markTestSkipped('No tasks found to test update.');
        }

        // Mock POST data
        Yii::$app->request->setBodyParams([
            'title' => 'Updated Task Title',
            'status' => 'completed',
            'priority' => 'high',
            'tags' => [],
            'due_date' => '2025-12-31'
        ]);

        $updatedTask = $this->controller->actionUpdate($task->id);
        $this->assertEquals('Updated Task Title', $updatedTask->title);
        $this->assertEquals('completed', $updatedTask->status);
    }

    public function testDeleteAndRetrieveTask()
    {
        $task = new Task([
            'title' => 'Temp Delete Task',
            'status' => 'pending',
            'priority' => 'low'
        ]);
        $this->assertTrue($task->save());

        // Delete
        $deleteResult = $this->controller->actionDelete($task->id);
        $this->assertEquals('Task deleted successfully', $deleteResult['message']);

        // Retrieve
        $retrieveResult = $this->controller->actionRetrieve($task->id);
        $this->assertEquals('Task retrieved successfully', $retrieveResult['message']);
    }

    public function testToggleStatus()
    {
        $task = new Task([
            'title' => 'Temp Status Task',
            'status' => 'pending',
            'priority' => 'medium'
        ]);
        $this->assertTrue($task->save());

        $toggledTask = $this->controller->actionToggleStatus($task->id);
        $this->assertEquals('completed', $toggledTask->status);

        $toggledTask = $this->controller->actionToggleStatus($task->id);
        $this->assertEquals('pending', $toggledTask->status);
    }
}
