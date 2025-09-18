<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class TaskFixture extends ActiveFixture
{
  public $modelClass = 'app\models\Task';
  public $tableName = 'tasks';
}

// 1. Генерация файла с данными выполняется командой:
//    php yii fixture/generate tasks --count=10
// где tasks ОБЯЗАТЕЛЬНО должно совпадать с именем таблицы, и является именем
// файла шаблона fixtures/templates/tasks.php
// 2. запись данных в БД выполняется командой:
//    php yii fixture/load Task
// и тут Task - обязательно с большой буквы (для TaskFixture)
