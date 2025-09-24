<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $town_id
 * @property bool $is_executor
 * @property string $reg_date
 * @property string|null $avatar
 * @property string|null $birth_date
 * @property string|null $phone
 * @property string|null $telegram
 * @property string|null $information
 * @property int|null $rating
 */
class User extends ActiveRecord
{
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
      [['name', 'email', 'password', 'town_id', 'is_executor'], 'required'],
      [['town_id', 'rating'], 'integer'],
      [['is_executor'], 'boolean'],
      [['reg_date', 'birth_date'], 'safe'],
      [['name', 'avatar', 'phone', 'telegram'], 'string', 'max' => 256],
      [['email'], 'string', 'max' => 128],
      [['password'], 'string', 'max' => 128],
      [['information'], 'string', 'max' => 1024],
      [['email'], 'unique'],
      [['email'], 'email'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'name' => 'Ф.И.О.',
      'email' => 'Email',
      'password' => 'Пароль',
      'town_id' => 'Город',
      'is_executor' => 'Является исполнителем',
      'reg_date' => 'Дата регистрации',
      'avatar' => 'Аватар',
      'birth_date' => 'Дата рождения',
      'phone' => 'Телефон',
      'telegram' => 'Telegram',
      'information' => 'Информация',
      'rating' => 'Рейтинг',
    ];
  }

  /**
   * Связь с моделью Town
   */
  public function getTown()
  {
    return $this->hasOne(Town::className(), ['id' => 'town_id']);
  }

  /**
 * Получает все отзывы заказчиков, полученные этим пользователем как исполнителем - связь с моделью CustomerReview
 *
 * @return \yii\db\ActiveQuery
 */
  public function getCustomerReviews()
  {
    return $this->hasMany(CustomerReview::className(), ['executor_id' => 'id']);
  }

  /**
 * Возвращает количество отзывов на пользователя как на исполнителя.
 *
 * @return int
 */
  public function getReviewsCount()
  {
    return $this->getCustomerReviews()->count();
  }
}
