<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "towns".
 *
 * @property int $id
 * @property string $name
 *
 * @package app\models
 */
class Town extends ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'towns';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['name'], 'required'],
      [['name'], 'string', 'max' => 128],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'name' => 'Город',
    ];
  }
}
