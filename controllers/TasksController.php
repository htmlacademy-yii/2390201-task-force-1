<?php

namespace app\controllers;

use Yii;
use app\models\Task;
use app\models\Category;
use app\models\Location;
use app\models\TaskFilter;
use app\models\TaskResponse;
use app\models\TaskStatusAndAction;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

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
      ->where(['status_id' => TaskStatusAndAction::STATUS_NEW])
      ->orderBy(['date' => SORT_DESC]);
    // Применяем условия фильтрации
    $this->TasksFiltering($taskFilterForm, $tasks);
    // Получаем все задачи с применёнными фильтрами
    $tasks = $tasks->all();

    return $this->render('index', compact('tasks', 'taskFilterForm', 'categories'));
  }

  // Просмотр задачи с ID = $id
  public function actionView(int $id)
  {
    $task = Task::findOne($id);
    if (!$task) {
      throw new NotFoundHttpException('Задача с id='.$id.' не найдена.');
    }
    return $this->render('view', compact('task'));
  }

  /**
   * Добавление новой задачи
   */
  public function actionAdd()
  {
    if (Yii::$app->user->identity->is_executor) {
      throw new ForbiddenHttpException('Исполнители не могут создавать новые задачи.');
    }

    $categories = Category::find()->all();
    $task = new Task();

    if (Yii::$app->request->isPost) {
      $task->load(Yii::$app->request->post());
      $task->files = UploadedFile::getInstancesByName('Task[files]');
      $task->addHiddenRequiredFields(Yii::$app->user->id);
      if ($task->validate()) {
        if ($task->save(false) && $task->saveFiles()) {
          return $this->redirect(['view', 'id' => $task->id]);
        }
      }
    }

    return $this->render('add', compact('task','categories'));
  }

  /**
   * Принять отклик на задание (назначить исполнителя)
   */
  public function actionAcceptResponse(int $id)
  {
    $task = Task::findOne($id);
    $response = TaskResponse::findOne(Yii::$app->request->post('response_id'));
    if (!$task || !$response || $response->task_id !== $id) {
      throw new NotFoundHttpException("Задача {$id} или отклик на неё не найдены.");
    }

    $task->acceptNewTaskResponse(Yii::$app->user->id, $response->executor_id);
    if(!$task->save(false)){
      throw new \RuntimeException("Не удалось сохранить запись об исполнителе в БД для задачи {$id}");
    }
    return $this->redirect(['view', 'id' => $id]);
  }

  /**
   * Отклонить отклик на задание
   */
  public function actionDeclineResponse(int $id)
  {
    $response = TaskResponse::findOne(Yii::$app->request->post('response_id'));
    if (!$response || $response->task_id !== $id) {
      throw new NotFoundHttpException("Отклик на задачу {$id} не найден.");
    }

    $response->declined = true;
    if(!$response->save(false)){
      throw new \RuntimeException("Не удалось сохранить запись об отклонении отклика в БД для задачи {$id}");
    }
    return $this->redirect(['view', 'id' => $id]);
  }
}
