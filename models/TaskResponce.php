<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Класс модели для таблицы "task_responces".
 *
 * @property int $id
 * @property int $task_id
 * @property int $executor_id
 * @property string|null $description
 * @property int $budget
 * @property bool $accepted
 * @property \DateTimeInterface $date
 */
class TaskResponce extends ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'task_responces';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['task_id', 'executor_id', 'budget'], 'required'],
      [['task_id', 'executor_id', 'budget'], 'integer'],
      [['description'], 'string', 'max' => 1024],
      [['accepted'], 'boolean'],
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
      'executor_id' => 'ID исполнителя',
      'description' => 'Описание',
      'budget' => 'Бюджет',
      'accepted' => 'Принято',
      'date' => 'Дата',
    ];
  }

  /**
   * Получает задачу через task_id.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getTask()
  {
    return $this->hasOne(Task::className(), ['id' => 'task_id']);
  }


  /**
   * Получает пользователя (исполнитель) через executor_id.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getExecutor()
  {
    return $this->hasOne(User::className(), ['id' => 'executor_id']);
  }
}
