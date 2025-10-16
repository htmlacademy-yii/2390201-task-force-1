<?php

namespace app\controllers;

use Yii;
use app\models\Task;
use app\models\TaskResponse;
use app\models\CustomerReview;
use Romnosk\TaskStatusAndAction;
use app\services\TasksService;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

class TasksController extends SecuredController
{
  private ?TasksService $tasksService = null;

  private function getTasksService(): TasksService
  {
    if ($this->tasksService === null) {
      $this->tasksService = new TasksService();
    }
    return $this->tasksService;
  }

  /**
   * Просмотр списка новых задач с возможностью фильтрации.
   *
   * @return string
   */
  public function actionIndex()
  {
    $taskFilterForm = new \app\models\TaskFilter();
    $taskFilterForm->load(Yii::$app->request->get());

    $service = $this->getTasksService();
    $data = $service->getFilteredNewTasks($taskFilterForm); //Логика фильтрации вынесена в getFilteredNewTasks

    return $this->render('index', array_merge($data, ['taskFilterForm' => $taskFilterForm]));
  }

  /**
   * Просмотр задачи с ID = $id.
   *
   * @param int $id идентификатор задачи
   * @return string
   * @throws NotFoundHttpException если задача не найдена
   */
  public function actionView(int $id)
  {
    $task = Task::findOne($id);
    if (!$task) {
      throw new NotFoundHttpException("Задача {$id} не найдена.");
    }
    $taskFiles = $task->getFiles()->all();
    $taskResponse = new TaskResponse();     // для попап-формы отклика исполнителя
    $customerReview = new CustomerReview(); // для попап-формы отзыва заказчика
    return $this->render('view', compact('task', 'taskResponse', 'customerReview', 'taskFiles'));
  }

  /**
   * Добавление новой задачи.
   *
   * @return string|\yii\web\Response
   * @throws ForbiddenHttpException если пользователь — исполнитель
   */
  public function actionAdd()
  {
    if (Yii::$app->user->identity->is_executor) {
      throw new ForbiddenHttpException('Исполнители не могут создавать новые задачи.');
    }

    $categories = \app\models\Category::find()->all();
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
   * Принять отклик на задание (назначить исполнителя).
   *
   * @param int $id идентификатор задачи
   * @return \yii\web\Response
   * @throws NotFoundHttpException если задача или отклик не найдены
   * @throws \RuntimeException если не удалось сохранить изменения
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
    if (!$task->save(false) || !$response->save(false)) {
      throw new \RuntimeException("Не удалось сохранить запись об отклике исполнителя в БД для задачи {$id}");
    }
    return $this->redirect(['view', 'id' => $id]);
  }

  /**
   * Отклонить отклик на задание.
   *
   * @param int $id идентификатор задачи
   * @return \yii\web\Response
   * @throws NotFoundHttpException если отклик не найден
   * @throws \RuntimeException если не удалось сохранить изменения
   */
  public function actionDeclineResponse(int $id)
  {
    $response = TaskResponse::findOne(Yii::$app->request->post('response_id'));
    if (!$response || $response->task_id !== $id) {
      throw new NotFoundHttpException("Отклик на задачу {$id} не найден");
    }

    $response->declined = true;
    if (!$response->save(false)) {
      throw new \RuntimeException("Не удалось сохранить запись об отклонении отклика в БД для задачи {$id}");
    }
    return $this->redirect(['view', 'id' => $id]);
  }

  /**
   * Исполнителю отказаться от задания.
   *
   * @param int $id идентификатор задачи
   * @return \yii\web\Response
   * @throws NotFoundHttpException если задача не найдена
   * @throws \RuntimeException если не удалось сохранить изменения
   */
  public function actionDecline(int $id)
  {
    $task = Task::findOne($id);
    if (!$task) {
      throw new NotFoundHttpException("Задача {$id} не найдена.");
    }

    $task->executorDecline(Yii::$app->user->id, $task->executor_id);
    if (!$task->save(false)) {
      throw new \RuntimeException("Не удалось сохранить запись об отказе исполнителя в БД для задачи {$id}");
    }
    return $this->redirect(['view', 'id' => $id]);
  }

  /**
   * Исполнителю откликнуться на задание.
   *
   * @param int $id идентификатор задачи
   * @return \yii\web\Response
   * @throws NotFoundHttpException если не удалось загрузить параметры отклика
   * @throws \RuntimeException если не удалось сохранить отклик
   */
  public function actionRespond(int $id)
  {
    $taskResponse = new TaskResponse();
    if (!$taskResponse->load(Yii::$app->request->post())) {
      throw new NotFoundHttpException("Не удалось загрузить параметры отклика на задачу {$id}");
    }

    $taskResponse->executorRespond($id, Yii::$app->user->id);
    if (!$taskResponse->save(false)) {
      throw new \RuntimeException("Не удалось сохранить запись об отклике исполнителя в БД для задачи {$id}");
    }
    return $this->redirect(['view', 'id' => $id]);
  }

  /**
   * Заказчику завершить задание и написать отзыв на исполнителя.
   *
   * @param int $id идентификатор задачи
   * @return \yii\web\Response
   * @throws NotFoundHttpException если задача не найдена или не загружен отзыв
   * @throws \RuntimeException если не удалось сохранить данные
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
    if (!$task->save(false) || !$customerReview->save(false)) {
      throw new \RuntimeException("Не удалось сохранить в БД запись об отзыве на исполнителя для задачи {$id}");
    }
    return $this->redirect(['view', 'id' => $id]);
  }

  /**
   * Отображает задачи текущего пользователя: либо как исполнителя, либо как заказчика.
   *
   * @param string|null $status фильтр по статусу
   * @return string
   */
  public function actionMy(?string $status = null)
  {
    $service = $this->getTasksService();
    $data = $service->getMyTasks($status); // Логика получения задач вынесена в getMyTasks

    return $this->render('my', $data);
  }
}
