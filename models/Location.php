<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class Location
 *
 * @property int $id
 * @property string|null $name
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
            [['latitude', 'longitude'], 'required'],
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
}
