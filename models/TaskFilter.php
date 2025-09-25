<?php

namespace app\models;

use yii\base\Model;

class TaskFilter extends Model
{
  public $categories = [];      // массив ID специализаций
  public $remote = false;       // фильтр: удалённая работа (location_id IS NULL)
  public $no_executor = false;  // фильтр: без исполнителя (executor_id IS NULL)
  public $period = '-365 days';  // период: 1 hour, 12 hours, 24 hours, 365 days

  public function rules()
  {
    return [
      [['categories'], 'each', 'rule' => ['integer']],
      [['remote', 'no_executor'], 'boolean'],
      [['period'], 'in', 'range' => ['-1 hour', '-12 hours', '-24 hours', '-365 days']],
    ];
  }

  // Убираем префикс ?TaskFilter в URL-параметрах
  public function formName()
  {
    return '';
  }
}
