<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "executor_category".
 *
 * @property int $id
 * @property int $user_id
 * @property int $specializing_id
  */
class ExecutorCategory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'executor_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'category_id'], 'required'],
            [['user_id', 'category_id'], 'integer'],
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
            'category_id' => 'ID Специализации',
        ];
    }

    /**
     * Получает соответствующего пользователя - связь с моделью User
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Получает соответствующую категорию - связь с моделью Category.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }
}
