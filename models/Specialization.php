<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "specializations".
 *
 * @property int $id
 * @property string $name
 * @property string $icon
 *
 * @package app\models
 */
class Specialization extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'specializations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'icon'], 'required'],
            [['name', 'icon'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Специализация',
            'icon' => 'Иконка',
        ];
    }
}
