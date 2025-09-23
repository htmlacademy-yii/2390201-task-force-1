<?php

namespace app\controllers;

use app\models\Task;
use app\models\Category;
use app\models\TaskFilter;
use yii\web\Controller;
use yii\web\Request;

class TasksController extends Controller
{
  // Отработка условий выбора задач по фильтрам
  private function FormFiltering(TaskFilter &$taskFilterForm, \yii\db\ActiveQuery &$tasks) :void
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

  public function actionIndex()
  {
    $taskFilterForm = new TaskFilter();
    $taskFilterForm->load(\Yii::$app->request->get());

    $categories = Category::find()->all();

    // Базовый запрос - новые задачи по убыванию.
    $tasks = Task::find()
      ->where(['status_id' => 1])
      ->orderBy(['date' => SORT_DESC]);
    // Применяем условия фильтрации
    $this->FormFiltering($taskFilterForm, $tasks);
    // Получаем все задачи с применёнными фильтрами
    $tasks = $tasks->all();

    return $this->render('index', [
      'tasks' => $tasks,
      'taskFilterForm' => $taskFilterForm,
      'categories' => $categories,
    ]);
  }
}
