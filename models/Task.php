<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\validators\LocationGeocodeValidator;

/**
 * Класс модели для таблицы "tasks".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $category_id
 * @property int|null $location_id
 * @property int|null $budget
 * @property string|null $deadline
 * @property int $customer_id
 * @property int|null $executor_id
 * @property int $status_id
 * @property string $date
 *
 * @property Category $category
 * @property Location $location
 * @property User $customer
 * @property User $executor
 * @property TaskStatus $status
 * @property TaskResponse[] $responses
 * @property TaskFile[] $files
 */
class Task extends ActiveRecord
{
  /**
   * Загруженные файлы, прикреплённые к заданию.
   * @var \yii\web\UploadedFile[]|null
   */
  public $files;

  /**
   * Виртуальное поле для ввода названия локации (города).
   * @var string|null
   */
  public $locationName;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'tasks';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['name', 'description', 'category_id'], 'required', 'message' => 'Поле не может быть пустым'],
      [['description'], 'string'],
      [['category_id', 'customer_id', 'executor_id'], 'integer'],
      [['deadline', 'date'], 'safe'],
      [['budget'], 'integer'],
      [['budget'], 'default', 'value' => null],
      [['deadline'], 'validateDeadline'],
      [['name'], 'string', 'max' => 256],
      [['name'], 'validateMinNonWhitespace', 'params' => ['min' => 10]],
      [['description'], 'validateMinNonWhitespace', 'params' => ['min' => 30]],
      [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
      // Валидация названия локации через геокодер
      ['locationName', LocationGeocodeValidator::class],
      ['location_id', 'integer'],
      [['files'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
    ];
  }

  /**
   * Валидация дедлайна: не раньше текущего дня.
   */
  public function validateDeadline($attribute, $params)
  {
    if (!$this->$attribute) {
      return;
    }
    $deadline = strtotime($this->$attribute);
    $today = strtotime(date('Y-m-d'));
    if ($deadline < $today) {
      $this->addError($attribute, 'Срок исполнения не может быть раньше текущего дня.');
    }
  }

  /**
   * Валидация минимального количества непробельных символов.
   */
  public function validateMinNonWhitespace($attribute, $params)
  {
    $min = $params['min'] ?? 1;
    $text = preg_replace('/\s+/', '', $this->$attribute);
    if (mb_strlen($text) < $min) {
      $this->addError($attribute, "Поле должно содержать не менее {$min} непробельных символов.");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'name' => 'Название задачи',
      'description' => 'Описание',
      'category_id' => 'ID Специализации',
      'location_id' => 'ID Местоположения',
      'locationName' => 'Местоположение',
      'budget' => 'Бюджет',
      'deadline' => 'Дедлайн',
      'customer_id' => 'Заказчик',
      'executor_id' => 'Исполнитель',
      'status_id' => 'Статус',
      'date' => 'Дата создания',
    ];
  }

  /**
   * Gets query for [[category]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getCategory()
  {
    return $this->hasOne(Category::class, ['id' => 'category_id']);
  }

  /**
   * Gets query for [[Location]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getLocation()
  {
    return $this->hasOne(Location::class, ['id' => 'location_id']);
  }

  /**
   * Gets query for [[Customer]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getCustomer()
  {
    return $this->hasOne(User::class, ['id' => 'customer_id']);
  }

  /**
   * Gets query for [[Executor]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getExecutor()
  {
    return $this->hasOne(User::class, ['id' => 'executor_id']);
  }

  /**
   * Gets query for [[Status]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getStatus()
  {
    return $this->hasOne(TaskStatus::class, ['id' => 'status_id']);
  }

  /**
   * Получает перечень откликов на задачу.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getResponses() {
    return $this->hasMany(TaskResponse::class, ['task_id' => 'id'])->inverseOf('task');
  }

  /**
   * Получает перечень файлов, прикреплённых к задаче.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getFiles() {
    return $this->hasMany(TaskFile::class, ['task_id' => 'id'])->inverseOf('task');
  }

  // app/models/Task.php

  /**
   * Инициализирует ID заказчика, статус и дату создания новой задачи.
   *
   * @param int $customerId ID заказчика
   * @return $this
   */
  public function addHiddenRequiredFields(int $customerId): self
  {
    $this->customer_id = $customerId;
    $this->status_id = TaskStatusAndAction::STATUS_NEW;
    $this->date = date('Y-m-d H:i:s');
    return $this;
  }

  /**
   * Сохраняет загруженные файлы в директорию /web/uploads/tasks
   * и создаёт записи в таблице tasks_files.
   *
   * @return bool true при успехе, иначе false
   */
  public function saveFiles(): bool
  {
    if (empty($this->files)) {
      return true;
    }

    $uploadDir = Yii::getAlias('@webroot') . '/uploads/tasks';

    foreach ($this->files as $file) {
      if (!$file || !$file->tempName) {
        continue;
      }

      $uniqFilename = uniqid('upload') . '.' . $file->extension;
      $filePath = $uploadDir . DIRECTORY_SEPARATOR . $uniqFilename;

      if (!$file->saveAs($filePath)) {
        throw new \RuntimeException("Не удалось сохранить файл: {$filePath}");
        return false;
      }

      if (!TaskFile::createForTask($this->id, 'uploads/tasks/' . $uniqFilename, $file->size, $file->name)) {
        throw new \RuntimeException("Не удалось сохранить запись о файле в БД для задачи {$this->id}");
        return false;
      }
    }
    return true;
  }

  /**
   * При подтвержении заказчиком отклика, устанавливает ID исполнителя и статус
   * "в работе". Проверяет ошибки, на случай, если была подмена данных в POST
   *
   * @param int $customerId ID заказчика из сессии
   * @param int $executorId ID исполнителя из POST
   * @return $this
   */
  public function acceptNewTaskResponse(int $customerId, int $executorId): self
  {
    if ($this->customer_id !== $customerId) {
      throw new \RuntimeException("Только заказчик задачи {$this->id} может принимать на неё отклики.");
    }
    if ($this->status_id !== TaskStatusAndAction::STATUS_NEW) {
      throw new \RuntimeException("Задача {$this->id} не новая, нельзя принять отклик");
    }
    $this->executor_id = $executorId;
    $this->status_id = TaskStatusAndAction::STATUS_IN_WORK;
    return $this;
  }

  /**
   * При подтвержении заказчиком отклика, устанавливает ID исполнителя и статус
   * "в работе". Проверяет ошибки, на случай, если была подмена данных в POST
   *
   * @param int $userId ID пользователя из сессии
   * @param int $executorId ID исполнителя задачи из БД
   * @return $this
   */
  public function executorDecline(int $userId, int $executorId): self
  {
    if ($this->executor_id !== $userId) {
      throw new \RuntimeException("Только исполнитель задачи {$this->id} может от неё отказаться");
    }
    if ($this->status_id !== TaskStatusAndAction::STATUS_IN_WORK) {
      throw new \RuntimeException("Задача {$this->id} не в работе, нельзя отказаться");
    }
    $this->status_id = TaskStatusAndAction::STATUS_FAILED;
    return $this;
  }

  /**
   * При завершении задачи заказчиком, устанавливает  статус "завершено".
   * Проверяет ошибки, на случай, если была подмена данных в POST
   *
   * @param int $customerId ID заказчика из сессии
   * @return $this
   */
  public function completeByCustomer(int $customerId): self
  {
    if ($this->customer_id !== $customerId) {
      throw new \RuntimeException("Задачу {$this->id} может завершить только её заказчик");
    }
    if ($this->status_id !== TaskStatusAndAction::STATUS_IN_WORK) {
      throw new \RuntimeException("Задача {$this->id} не в работе, нельзя завершить");
    }
    $this->status_id = TaskStatusAndAction::STATUS_DONE;
    return $this;
  }
}
