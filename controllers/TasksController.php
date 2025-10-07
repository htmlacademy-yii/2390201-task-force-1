<?php

namespace app\controllers;

use Yii;
use app\models\Task;
use app\models\Category;
use app\models\Location;
use app\models\TaskFilter;
use app\models\TaskResponse;
use app\models\CustomerReview;
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
      throw new NotFoundHttpException("Задача {$id} не найдена.");
    }
    $taskResponse = new TaskResponse();     // для попап-формы отклика исполнителя
    $customerReview = new CustomerReview(); // для попап-формы отзыва заказчика
    return $this->render('view', compact('task', 'taskResponse', 'customerReview'));
  }

  /**
   * Добавление новой задачи.
   *
   * @return string|\yii\web\Response
   * @throws \yii\web\ForbiddenHttpException если пользователь — исполнитель
   */
  public function actionAdd()
  {
    if (Yii::$app->user->identity->is_executor) {
      throw new ForbiddenHttpException('Исполнители не могут создавать новые задачи.');
    }

    $categories = Category::find()->all();
    $task = new Task();

    if (!Yii::$app->request->isPost) {
      return $this->render('add', compact('task', 'categories'));
    }

    $task->load(Yii::$app->request->post());
    $task->files = UploadedFile::getInstancesByName('Task[files]');
    $task->addHiddenRequiredFields(Yii::$app->user->id);
    // Локация заполняется при вызове $task->validate() при валидации поля locationName
    if ($task->validate() && $task->save(false) && $task->saveFiles()) {
      return $this->redirect(['view', 'id' => $task->id]);
    }

    return $this->render('add', compact('task', 'categories'));
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
    $response->accepted = true;
    if(!$task->save(false) || !$response->save(false)){
      throw new \RuntimeException("Не удалось сохранить запись об отклике исполнителя в БД для задачи {$id}");
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
      throw new NotFoundHttpException("Отклик на задачу {$id} не найден");
    }

    $response->declined = true;
    if(!$response->save(false)){
      throw new \RuntimeException("Не удалось сохранить запись об отклонении отклика в БД для задачи {$id}");
    }
    return $this->redirect(['view', 'id' => $id]);
  }

  /**
   * Исполнителю отказаться от задания
   */
  public function actionDecline(int $id)
  {
    $task = Task::findOne($id);
    if (!$task) {
      throw new NotFoundHttpException("Задача {$id} не найдена.");
    }

    $task->executorDecline(Yii::$app->user->id, $task->executor_id);
    if(!$task->save(false)){
      throw new \RuntimeException("Не удалось сохранить запись об отказе исполнителя в БД для задачи {$id}");
    }
    return $this->redirect(['view', 'id' => $id]);
  }

  /**
   * Исполнителю откликнуться на задание
   */
  public function actionRespond(int $id)
  {
    $taskResponse = new TaskResponse();
    if(!$taskResponse->load(Yii::$app->request->post())){
      throw new NotFoundHttpException("Не удалось загрузить параметры отклика на задачу {$id}");
    }

    $taskResponse->executorRespond($id, Yii::$app->user->id);
    if(!$taskResponse->save(false)){
      throw new \RuntimeException("Не удалось сохранить запись об отклике исполнителя в БД для задачи {$id}");
    }
    return $this->redirect(['view', 'id' => $id]);
  }

  /**
   * Заказчику завершить задание и написать отзыв на исполнителя
   */
  public function actionComplete(int $id)
  {
    $task = Task::findOne($id);
    $customerReview = new CustomerReview();
    if (!$task || !$customerReview->load(Yii::$app->request->post())) {
      throw new NotFoundHttpException("Задача {$id} не найдена или не удалось загрузить параметры отзыва на исполнителя");
    }

    $task->completeByCustomer(Yii::$app->user->id);
    $customerReview->addReview(Yii::$app->user->id, $task->executor_id, $id);
    if(!$task->save(false) || !$customerReview->save(false)){
      throw new \RuntimeException("Не удалось сохранить в БД запись об отзыве на исполнителя для задачи {$id}");
    }
    return $this->redirect(['view', 'id' => $id]);
  }
}
