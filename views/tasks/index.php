<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>

<div class="left-column">
  <?php if (!$tasks): ?>
    <h3 class="head-main head-task">Новых заданий нет</h3>
  <?php else: ?>
    <h3 class="head-main head-task">Новые задания</h3>
    <?php foreach ($tasks as $task): ?>
      <div class="task-card">
        <div class="header-task">
          <a  href="#" class="link link--block link--big"><?= Html::encode($task->name) ?></a>
          <p class="price price--task"><?= $task->budget ? $task->budget . ' ₽' : 'Не указан' ?></p>
        </div>
        <p class="info-text"><span class="current-time"><?= Yii::$app->formatter->asDate($task->date) ?></span></p>
        <p class="task-text"><?= Html::encode($task->description) ?></p>
        <div class="footer-task">
          <p class="info-text town-text"><?= Html::encode($task->location->name ?? 'Не указана') ?></p>
          <p class="info-text category-text"><?= Html::encode($task->specializing->name ?? 'Не указана') ?></p>
          <a href="#" class="button button--black">Смотреть Задание</a>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="pagination-wrapper">
      <ul class="pagination-list">
        <li class="pagination-item mark">
          <a href="#" class="link link--page"></a>
        </li>
        <li class="pagination-item">
          <a href="#" class="link link--page">1</a>
        </li>
        <li class="pagination-item pagination-item--active">
          <a href="#" class="link link--page">2</a>
        </li>
        <li class="pagination-item">
          <a href="#" class="link link--page">3</a>
        </li>
        <li class="pagination-item mark">
          <a href="#" class="link link--page"></a>
        </li>
      </ul>
    </div>
  <?php endif; ?>
</div>

<div class="right-column">
  <div class="right-card black">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['tasks/index']),
        'options' => ['class' => 'search-form']
    ]); ?>

      <h4 class="head-card">Категории</h4>
      <div class="form-group">
          <div class="checkbox-wrapper">
          <?= $form->field($taskFilterForm, 'specializations')
            ->label(false)
            ->checkboxList(ArrayHelper::map($specializations, 'id', 'rus_name'))
          ?>
          </div>
      </div>
      <h4 class="head-card">Дополнительно</h4>
      <div class="form-group">
        <?= $form->field($taskFilterForm, 'remote')->checkbox([
          'label' => 'Удалённая работа',
          'uncheck' => null,
        ]) ?>

        <?= $form->field($taskFilterForm, 'no_executor')->checkbox([
          'label' => 'Без откликов',
          'uncheck' => null,
        ]) ?>
      </div>

      <h4 class="head-card">Период</h4>
      <div class="form-group">
        <?= $form->field($taskFilterForm, 'period')->dropDownList([
          '-1 hour' => '1 час',
          '-12 hours' => '12 часов',
          '-24 hours' => '24 часа',
          '-365 days' => 'За год',
        ], ['prompt' => 'Выберите период']) ?>
      </div>

      <div class="form-group">
        <?= Html::submitButton('Искать', ['class' => 'button button--blue']) ?>
      </div>

    <?php ActiveForm::end(); ?>
  </div>
</div>
