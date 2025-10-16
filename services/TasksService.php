<?php

namespace app\services;

use Yii;
use app\models\Constants;
use app\models\Task;
use app\models\Category;
use app\models\TaskFilter;
use Romnosk\TaskStatusAndAction;
use yii\data\Pagination;

class TasksService
{
  /**
   * Применяет фильтры для вывода списка задач.
   *
   * @param TaskFilter $taskFilterForm форма с фильтрами
   * @param \yii\db\ActiveQuery $tasks запрос к задачам
   * @return void
   */
  public function applyFilters(TaskFilter $taskFilterForm, \yii\db\ActiveQuery $tasks): void
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

  /**
   * Получает список новых задач с фильтрацией и пагинацией.
   *
   * @param TaskFilter $taskFilterForm форма с фильтрами
   * @return array данные для представления: задачи, категории, пагинация
   */
  public function getFilteredNewTasks(TaskFilter $taskFilterForm): array
  {
    $categories = Category::find()->all();

    // Базовый запрос — новые задачи, из города пользователя или удалённые, по убыванию даты
    $tasksQuery = Task::find()
      ->where(['status_id' => TaskStatusAndAction::STATUS_NEW])
      ->andWhere(['or',
        ['location_id' => Yii::$app->user->identity->location_id],
        ['is', 'location_id', null]
      ])
      ->orderBy(['date' => SORT_DESC]);

    $this->applyFilters($taskFilterForm, $tasksQuery);

    $pagination = new Pagination([
      'totalCount' => $tasksQuery->count(),
      'pageSize' => Constants::TASKS_ON_PAGE,
    ]);

    $tasks = $tasksQuery
      ->offset($pagination->offset)
      ->limit($pagination->limit)
      ->all();

    return compact('tasks', 'categories', 'pagination');
  }

  /**
   * Применяет фильтрацию задач для исполнителя (ссылка Мои Задачи).
   *
   * @param string|null $status статус задачи
   * @param \yii\db\ActiveQuery $tasks запрос к задачам
   * @return void
   */
  public function applyExecutorFilters(?string $status, \yii\db\ActiveQuery $tasks): void
  {
    switch ($status) {
      case 'overdue':
        $tasks->andWhere(['status_id' => TaskStatusAndAction::STATUS_IN_WORK])
              ->andWhere(['<', 'deadline', date('Y-m-d')]);
        break;
      case 'closed':
        $tasks->andWhere(['in', 'status_id', [
          TaskStatusAndAction::STATUS_DONE,
          TaskStatusAndAction::STATUS_FAILED
        ]]);
        break;
      case 'in-progress':
      default:
        $tasks->andWhere(['status_id' => TaskStatusAndAction::STATUS_IN_WORK]);
        break;
    }
  }

  /**
   * Применяет фильтрацию задач для заказчика (ссылка Мои Задачи).
   *
   * @param string|null $status статус задачи
   * @param \yii\db\ActiveQuery $tasks запрос к задачам
   * @return void
   */
  public function applyCustomerFilters(?string $status, \yii\db\ActiveQuery $tasks): void
  {
    switch ($status) {
      case 'in-progress':
        $tasks->andWhere(['status_id' => TaskStatusAndAction::STATUS_IN_WORK]);
        break;
      case 'closed':
        $tasks->andWhere(['in', 'status_id', [
          TaskStatusAndAction::STATUS_CANCELED,
          TaskStatusAndAction::STATUS_DONE,
          TaskStatusAndAction::STATUS_FAILED
        ]]);
        break;
      case 'new':
      default:
        $tasks->andWhere(['status_id' => TaskStatusAndAction::STATUS_NEW])
              ->andWhere(['is', 'executor_id', null]);
        break;
    }
  }

  /**
   * Получает задачи текущего пользователя с фильтрацией по статусу.
   *
   * @param string|null $status фильтр по статусу
   * @return array данные для представления: задачи и статус
   */
  public function getMyTasks(?string $status = null): array
  {
    $user = Yii::$app->user->identity;
    $tasksQuery = Task::find();

    if ($user->is_executor) {
      $tasksQuery->andWhere(['executor_id' => $user->id]);
      $this->applyExecutorFilters($status, $tasksQuery);
    } else {
      $tasksQuery->andWhere(['customer_id' => $user->id]);
      $this->applyCustomerFilters($status, $tasksQuery);
    }

    $tasks = $tasksQuery->orderBy(['date' => SORT_DESC])->all();

    return compact('tasks', 'status');
  }
}
