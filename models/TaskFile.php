<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "tasks_files".
 *
 * @property int $id
 * @property int $task_id
 * @property string $file_path
 * @property int $file_size
 * @property string $user_filename
 *
 * @property-read Task $task
 */
class TaskFile extends ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'tasks_files';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['task_id', 'file_path', 'file_size', 'user_filename'], 'required'],
      [['task_id', 'file_size'], 'integer'],
      [['file_path', 'user_filename'], 'string', 'max' => 256],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'task_id' => 'ID задачи',
      'file_path' => 'Путь к файлу на сервере',
      'file_size' => 'Размер файла в байтах',
      'user_filename' => 'Имя файла от пользователя'
    ];
  }

  /**
   * Связь с моделью Task
   *
   * @return \yii\db\ActiveQuery
   */
  public function getTask()
  {
    return $this->hasOne(Task::class, ['id' => 'task_id']);
  }

  // app/models/TaskFile.php

  /**
   * Создаёт новую запись для файла, привязанного к задаче.
   *
   * @param int $taskId ID задачи
   * @param string $relativePath Относительный путь к файлу (от корня web/)
   * @param int $fileSize Размер файла в байтах
   * @param string $userFilename Имя файла, под которым его загрузил пользователь
   * * @return bool Успешно ли сохранено
   */
  public static function createForTask(int $taskId, string $relativePath, int $fileSize, string $userFilename): bool
  {
    $model = new self();
    $model->task_id = $taskId;
    $model->file_path = $relativePath;
    $model->file_size = $fileSize;
    $model->user_filename = $userFilename;
    return $model->save();
  }
}
