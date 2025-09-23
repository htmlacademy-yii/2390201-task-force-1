<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $category_id
 * @property int $location_id
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
 */
class Task extends ActiveRecord
{
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
            [['name', 'description', 'category_id', 'location_id', 'customer_id', 'status_id'], 'required'],
            [['description'], 'string'],
            [['category_id', 'location_id', 'customer_id', 'executor_id', 'status_id'], 'integer'],
            [['budget'], 'integer'],
            [['deadline', 'date'], 'safe'],
            [['name'], 'string', 'max' => 256],
        ];
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
            'category_id' => 'Специализация',
            'location_id' => 'Местоположение',
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
}
