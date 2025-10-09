<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;
use app\validators\UserDateValidator;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $location_id
 * @property bool $is_executor
 * @property string $reg_date
 * @property string|null $avatar
 * @property string|null $birth_date
 * @property string|null $phone
 * @property string|null $telegram
 * @property int|null $vk_id
 * @property string|null $information
 *
 * @property ExecutorCategory[] $categories
 */
class User extends ActiveRecord implements IdentityInterface
{
  /**
   * Виртуальное поле для отображения и ввода даты рождения в формате дд.мм.гггг
   */
  public ?string $birth_date_view = null;
  /**
   * Виртуальное поле для выбранных категорий в форме
   */
  public array $selectedCategoryIds = [];

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'users';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['name', 'email', 'password', 'location_id', 'is_executor'], 'required', 'message' => 'Поле не должно быть пустым'],
      [['location_id'], 'integer'],
      [['is_executor'], 'boolean'],
      [['reg_date', 'birth_date'], 'safe'],
      [['name', 'avatar', 'phone', 'telegram'], 'string', 'max' => 256],
      [['email'], 'string', 'max' => 128],
      [['password'], 'string', 'max' => 128],
      [['information'], 'string', 'max' => 1024],
      [['email'], 'unique'],
      [['email'], 'email'],
      [['vk_id'], 'integer'],
      [['vk_id'], 'unique'],

      // Правила для сценария редактирования профиля
      [['name', 'email'], 'required', 'on' => 'edit'],
      ['phone', 'match', 'pattern' => '/^\+7\d{10}$/', 'message' => 'Номер телефона должен быть в формате +7 и 10 цифр', 'on' => 'edit'],      ['telegram', 'string', 'max' => 64, 'on' => 'edit'],
      ['information', 'string', 'max' => 1024, 'on' => 'edit'],
      ['avatar', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, bmp', 'maxSize' => 1024 * 1024 * 2, 'on' => 'edit'],
      ['birth_date_view', UserDateValidator::class, 'on' => 'edit'],
      [['selectedCategoryIds'], 'safe', 'on' => 'edit'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'name' => 'Ваше имя',
      'email' => 'Email',
      'password' => 'Пароль',
      'location_id' => 'Город',
      'is_executor' => 'Является исполнителем',
      'reg_date' => 'Дата регистрации',
      'avatar' => 'Аватар',
      'birth_date' => 'Дата рождения',
      'birth_date_view' => 'Дата рождения',
      'phone' => 'Номер телефона',
      'telegram' => 'Telegram',
      'vk_id' => 'VK ID',
      'information' => 'Информация о себе',
    ];
  }

  /**
   * Инициализация модели: заполняем виртуальное поле из birth_date
   */
  public function init()
  {
    parent::init();
    if (!empty($this->birth_date)) {
      $this->birth_date_view = $this->formatBirthDateForView($this->birth_date);
    }
  }

  /**
   * Преобразует дату из БД в формат дд.мм.гггг для передачи в представление
   */
  public function formatBirthDateForView(?string $dbDate): string
  {
    if (empty($dbDate)) {
      return '';
    }
    foreach (['Y-m-d H:i:s', 'Y-m-d'] as $format) {
      $date = \DateTime::createFromFormat($format, $dbDate);
      if ($date !== false) {
        return $date->format('d.m.Y');
      }
    }
    return '';
  }

  /**
   * Связь с моделью Location
   */
  public function getLocation()
  {
    return $this->hasOne(Location::className(), ['id' => 'location_id']);
  }

  /**
   * Получает все отзывы заказчиков, полученные этим пользователем как исполнителем
   */
  public function getCustomerReviews()
  {
    return $this->hasMany(CustomerReview::className(), ['executor_id' => 'id'])->inverseOf('executor');
  }

  /**
   * Возвращает количество отзывов на пользователя как на исполнителя
   */
  public function getReviewsCount()
  {
    return $this->getCustomerReviews()->count();
  }

  /**
   * Получает все задачи, принятые этим пользователем как исполнителем
   */
  public function getTasksAccepted()
  {
    return $this->hasMany(Task::className(), ['executor_id' => 'id'])->inverseOf('executor');
  }

  /**
   * Возвращает количество принятых в работу пользователем задач как исполнителем
   */
  public function getTasksCount()
  {
    return $this->getTasksAccepted()->count();
  }

  /**
   * Возвращает количество проваленных пользователем задач как исполнителем
   */
  public function getFailedTasksCount()
  {
    return $this->getTasksAccepted()
      ->andWhere(['status_id' => TaskStatusAndAction::STATUS_FAILED])
      ->count();
  }

  /**
   * Возвращает рейтинг пользователя как исполнителя (0–500)
   */
  public function getRating()
  {
    $ratingSum = $this->getCustomerReviews()->sum('rating') ?? 0;
    $ratingDivider = $this->getReviewsCount() + $this->getFailedTasksCount();
    return ($ratingDivider !== 0) ? (int) round($ratingSum / $ratingDivider) : 0;
  }

  /**
   * Получает все специализации этого пользователя как исполнителя
   */
  public function getCategories()
  {
    return $this->hasMany(ExecutorCategory::className(), ['user_id' => 'id'])->inverseOf('user');
  }

  /**
   * Находит пользователя по его ID
   */
  public static function findIdentity($id)
  {
    return self::findOne($id);
  }

  /**
   * Находит пользователя по токену доступа (не требуется для OAuth)
   */
  public static function findIdentityByAccessToken($token, $type = null)
  {
    return null;
  }

  /**
   * Возвращает идентификатор пользователя
   */
  public function getId()
  {
    return $this->getPrimaryKey();
  }

  /**
   * Возвращает ключ аутентификации пользователя (не требуется для OAuth)
   */
  public function getAuthKey()
  {
    return null;
  }

  /**
   * Проверяет, соответствует ли переданный ключ аутентификации ключу пользователя
   * (не требуется для OAuth)
   */
  public function validateAuthKey($authKey)
  {
    return null;
  }

  /**
   * Проверяет корректность пароля пользователя
   */
  public function validatePassword($password)
  {
    return Yii::$app->security->validatePassword($password, $this->password);
  }

  /**
   * Сохраняет загруженный аватар и возвращает флаг успеха.
   * Если файл не загружен — возвращает true, т.к. аватар можно не обновлять
   *
   * @param UploadedFile|null $uploadedFile
   * @return bool true при успехе, иначе false
   */
  public function avatarFileEmptyOrSaved(?UploadedFile $uploadedFile): bool
  {
    if ($uploadedFile === null) {
      // $this->avatar = $this->getOldAttribute('avatar');
      return true; // Пользователь не обновлял аватар - это допустимо
    }

    $avatarName = 'avatar_' . $this->id . '_' . time() . '.' . $uploadedFile->extension;
    $this->avatar = 'uploads/avatars/' . $avatarName;
    $fullPath = Yii::getAlias('@webroot/' . $this->avatar);

    if (!$uploadedFile->saveAs($fullPath)) {
      throw new \RuntimeException("Не удалось сохранить файл аватара: {$fullPath}");
      return false;
    }
    return true;
  }

  /**
   * Создаёт нового пользователя на основе данных из ВКонтакте
   */
  public static function createFromVk(array $attributes)
  {
    $user = new static();
    $user->vk_id = $attributes['id'];
    $user->name = ($attributes['first_name'] ?? '') . ' ' . ($attributes['last_name'] ?? '');
    $user->email = $attributes['email'];
    $user->password = Yii::$app->security->generateRandomString(60);
    $user->location_id = 1;
    $user->is_executor = false;
    $user->reg_date = date('Y-m-d H:i:s');
    $user->avatar = $attributes['photo_max'] ?? null;

    if (!empty($attributes['city']['title'])) {
      $cityName = $attributes['city']['title'];
      $location = Location::findOne(['name' => $cityName]);
      $user->location_id = $location ? $location->id : 1;
    }

    if (!$user->save(false)) {
      throw new \RuntimeException("Не удалось сохранить запись о пользователе {$user->name} в БД");
    }
    return $user;
  }

  /**
   * Находит в БД или создаёт пользователя на основе данных из ВКонтакте
   */
  public static function findOrCreateFromVk(array $attributes)
  {
    $vkId = $attributes['id'];
    $email = trim($attributes['email'] ?? '');
    if ($email === '') {
      return null; //email у нас обязательный атрибут, без него не создаём пользователя
    }

    $user = static::findOne(['vk_id' => $vkId]);
    if ($user === null) {
      $user = static::createFromVk($attributes);
    }
    return $user;
  }
}
