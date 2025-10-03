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
 * @property int $town_id
 *
 * @property Town $town
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
      [['name', 'latitude', 'longitude', 'town_id'], 'required'],
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
      'town_id' => 'ID города'
    ];
  }

  /**
   * Gets query for [[Town]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getTown()
  {
    return $this->hasOne(Town::class, ['id' => 'town_id']);
  }
}
