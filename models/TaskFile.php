<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "tasks_files".
 *
 * @property int $id
 * @property int $task_id
 * @property string $file_path
 *
 * @property-read Task $task
 */
class TaskFile extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'file_path'], 'required'],
            [['task_id'], 'integer'],
            [['file_path'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'file_path' => 'File Path',
        ];
    }

    /**
     * Связь с моделью Task
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }
}
