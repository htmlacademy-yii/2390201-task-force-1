<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Класс модели для таблицы "task_responses".
 *
 * @property int $id
 * @property int $task_id
 * @property int $executor_id
 * @property string|null $description
 * @property int $budget
 * @property bool $accepted
 * @property bool $declined
 * @property \DateTimeInterface $date
 */
class TaskResponse extends ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'task_responses';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['task_id', 'executor_id', 'budget'], 'required', 'message' => 'Поле не может быть пустым'],
      [['task_id', 'executor_id', 'budget'], 'integer', 'message' => 'Поле должно быть целым числом'],
      [['description'], 'string', 'max' => 1024],
      [['accepted', 'declined'], 'boolean'],
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
      'declined' => 'Отклонено',
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

  /**
   * При направлении отклика исполнителем, заполняет необходимые параметры отклика - кроме
   * description и budget, которые заполняются в форме
   *
   * @param int $taskId ID задачи из формы
   * @param int $executorId ID исполнителя из сессии
   * @return $this
   */
  public function executorRespond(int $taskId, int $executorId): self
  {
    $this->task_id = $taskId;
    $this->executor_id = $executorId;
    $this->accepted = false;
    $this->declined = false;
    $this->date = date('Y-m-d H:i:s');
    return $this;
  }
}
