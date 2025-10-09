<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $task app\models\Task */
/* @var $categories app\models\Category[] */
?>

<main class="main-content main-content--center container">
  <div class="add-task-form regular-form">
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'control-label'],
            'errorOptions' => ['class' => 'help-block'],
        ],
    ]);?>

    <h3 class="head-main">Публикация нового задания</h3>

    <?= $form->field($task, 'name')->textInput(['id' => 'essence-work'])->label('Мне нужно') ?>

    <?= $form->field($task, 'description')->textarea(['id' => 'username', 'rows' => 5])->label('Подробности задания') ?>

    <?= $form->field($task, 'category_id')->dropDownList(
      ArrayHelper::map($categories, 'id', 'rus_name'),
      ['id' => 'town-user', 'prompt' => 'Выберите категорию']
    )->label('Категория') ?>

    <?= $form->field($task, 'locationName')->textInput([
      'id' => 'location',
      'class' => 'location-icon'
    ])->label('Локация') ?>

    <div class="half-wrapper">
      <?= $form->field($task, 'budget')->textInput(['id' => 'budget', 'class' => 'budget-icon', 'type' => 'number', 'min' => 1])->label('Бюджет') ?>

      <?= $form->field($task, 'deadline')->textInput(['id' => 'period-execution', 'type' => 'date'])->label('Срок исполнения') ?>
    </div>


    <p class="form-label">Файлы</p>
    <label for="file-input" class="custom-file-button">
      <div class="new-file">
        <input type="file" name="Task[files][]" id="file-input" multiple style="display:none;">
        Добавить файлы задания. Используйте Ctrl для множественного выбора.
      </div>
    </label>

    <?= Html::submitButton('Опубликовать', ['class' => 'button button--blue']) ?>

    <?php ActiveForm::end(); ?>
  </div>
</main>
