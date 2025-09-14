<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "executor_specializing".
 *
 * @property int $id
 * @property int $user_id
 * @property int $specializing_id
  */
class ExecutorSpecializing extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'executor_specializing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'specializing_id'], 'required'],
            [['user_id', 'specializing_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID Пользователя',
            'specializing_id' => 'ID Специализации',
        ];
    }

    /**
     * Gets the related User.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets the related Specializing.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecializing()
    {
        return $this->hasOne(Specialization::class, ['id' => 'specializing_id']);
    }
}
