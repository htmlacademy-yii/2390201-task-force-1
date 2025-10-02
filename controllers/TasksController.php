<?php

namespace app\controllers;

use app\models\Task;
use app\models\Category;
use app\models\TaskFilter;
use yii\web\Controller;
use yii\web\Request;
use yii\web\NotFoundHttpException;
use Romnosk\Models\Status;

class TasksController extends SecuredController
{
  // Отработка условий выбора задач по фильтрам
  private function TasksFiltering(TaskFilter &$taskFilterForm, \yii\db\ActiveQuery &$tasks) :void
  {
    // Фильтр по категориям (специализациям)
    if (!empty($taskFilterForm->categories)) {
      $tasks->andWhere(['in', 'category_id', $taskFilterForm->categories]);
    }
    // Фильтр по удалённой работе (location_id IS NULL)
    if ($taskFilterForm->remote) {
      $tasks->andWhere(['is', 'location_id', null]);
    }
    // Фильтр по отсутствию исполнителя
    if ($taskFilterForm->no_executor) {
      $tasks->andWhere(['is', 'executor_id', null]);
    }
    // Фильтр по периоду
    $interval = $taskFilterForm->period ?? '-365 days';
    $tasks->andWhere(['>=', 'date', date('Y-m-d H:i:s', strtotime($interval))]);
  }

  // Просмотр списка новых задач с возможностью фильтрации
  public function actionIndex()
  {
    $taskFilterForm = new TaskFilter();
    $taskFilterForm->load(\Yii::$app->request->get());

    $categories = Category::find()->all();

    // Базовый запрос - новые задачи по убыванию.
    $tasks = Task::find()
      ->where(['status_id' => Status::Canceled->id()])
      ->orderBy(['date' => SORT_DESC]);
    // Применяем условия фильтрации
    $this->TasksFiltering($taskFilterForm, $tasks);
    // Получаем все задачи с применёнными фильтрами
    $tasks = $tasks->all();

    return $this->render('index', [
      'tasks' => $tasks,
      'taskFilterForm' => $taskFilterForm,
      'categories' => $categories,
    ]);
  }

  // Просмотр задачи с ID = $id
  public function actionView(int $id)
  {
    $task = Task::findOne($id);

    if (!$task) {
      throw new NotFoundHttpException('Задача с id='.$id.' не найдена.');
    }

    return $this->render('view', [
      'task' => $task
    ]);
  }
}
