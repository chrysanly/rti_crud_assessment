<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Tag;

class TagController extends Controller
{
    public function actionIndex()
    {
        $tags = Tag::find()->all();
        return $this->render('index', compact('tags'));
    }

    public function actionCreate()
    {
        $tag = new Tag();
        if ($tag->load(Yii::$app->request->post()) && $tag->save()) {
            return $this->redirect(['index']);
        }
        return $this->render('create', compact('tag'));
    }

    public function actionUpdate($id)
    {
        $tag = $this->findModel($id);
        if ($tag->load(Yii::$app->request->post()) && $tag->save()) {
            return $this->redirect(['index']);
        }
        return $this->render('update', compact('tag'));
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Tag::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Tag not found.');
    }
}
