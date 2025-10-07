<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class Location
 *
 * @property int $id
 * @property string $name
 * @property string $latitude
 * @property string $longitude
 *
 * @package app\models
 */
class Location extends ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'locations';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['name', 'latitude', 'longitude'], 'required'],
      [['name'], 'string', 'max' => 255],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'name' => 'Название',
      'latitude' => 'Широта',
      'longitude' => 'Долгота',
    ];
  }

  /**
   * Находит или создаёт локацию по названию и координатам.
   *
   * @param string $name
   * @param string $latitude
   * @param string $longitude
   * @return Location|null
   */
  public static function findOrCreateByName(string $name, string $latitude, string $longitude): ?self
  {
    $location = static::findOne(['name' => $name]);
    if ($location) {
      return $location;
    }

    $location = new static();
    $location->name = $name;
    $location->latitude = $latitude;
    $location->longitude = $longitude;

    return $location->save() ? $location : null;
  }
}
