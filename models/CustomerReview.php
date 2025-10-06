<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Класс модели для таблицы "customer_reviews".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $executor_id
 * @property int $task_id
 * @property string|null $description
 * @property int $rating
 * @property string $date
 *
 */
class CustomerReview extends ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'customer_reviews';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
  return [
    [['customer_id', 'executor_id', 'task_id', 'rating'], 'required', 'message' => 'Поле не может быть пустым'],
    [['customer_id', 'executor_id', 'task_id'], 'integer', 'message' => 'Поле должно быть целым числом от 1 до 5 (звёзд)'],
    [['rating'], 'integer', 'min' => 1, 'max' => 5],
    [['description'], 'string', 'max' => 1024],
    [['date'], 'safe'],
  ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
  return [
    'id' => 'ID',
    'customer_id' => 'ID заказчика',
    'executor_id' => 'ID исполнителя',
    'task_id' => 'ID задачи',
    'description' => 'Описание',
    'rating' => 'Рейтинг',
    'date' => 'Дата',
  ];
  }

  /**
   * Gets the related Customer.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getCustomer()
  {
    return $this->hasOne(User::class, ['id' => 'customer_id']);
  }

  /**
   * Gets the related Executor.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getExecutor()
  {
    return $this->hasOne(User::class, ['id' => 'executor_id']);
  }

  /**
   * Gets the related Task.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getTask()
  {
    return $this->hasOne(Task::class, ['id' => 'task_id']);
  }

  /**
   * При завершении задачи заказчиком, заполняет необходимые параметры отзыва на исполнителя - кроме
   * description и rating, которые заполняются в форме
   *
   * @param int $customerId ID заказчика из сессии
   * @param int $executorId ID исполнителя из задачи
   * @param int $taskId ID задачи из формы
   * @return $this
   */
  public function addReview(int $customerId, int $executorId, int $taskId): self
  {
    $this->customer_id = $customerId;
    $this->executor_id = $executorId;
    $this->task_id = $taskId;
    $this->rating = $this->rating * 100; // рейтинг в отзывах хранится в виде трёхзначного числа для дальнейшего корректного отображения и вычисления
    $this->date = date('Y-m-d H:i:s');
    return $this;
  }
}
