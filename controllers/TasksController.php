<?php

namespace app\controllers;

use app\models\Task;
use yii\web\Controller;

class TasksController extends Controller
{
  public function actionIndex()
  {
    $tasks = Task::find()
      ->where(['is', 'executor_id', null])
      ->orderBy(['date' => SORT_DESC])
      ->all();

    return $this->render('index', ['tasks' => $tasks]);
  }
}
