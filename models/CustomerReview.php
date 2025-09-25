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
    [['customer_id', 'executor_id', 'task_id', 'rating'], 'required'],
    [['customer_id', 'executor_id', 'task_id', 'rating'], 'integer'],
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
}
