<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
?>

<main id="main" class="main-content container" role="main">
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
            <p class="info-text category-text"><?= Html::encode($task->category->rus_name ?? 'Не указана') ?></p>
            <a href="#" class="button button--black">Смотреть Задание</a>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="pagination-wrapper">
        <?= LinkPager::widget([
          'pagination' => $pagination,
          'options' => ['class' => 'pagination-list'],
          'linkContainerOptions' => ['class' => 'pagination-item'],
          'linkOptions' => ['class' => 'link link--page'],
          'activePageCssClass' => 'pagination-item--active',
          'disabledPageCssClass' => 'disabled',
          'prevPageLabel' => '&laquo;',
          'nextPageLabel' => '&raquo;',
        ]) ?>
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
            <?= $form->field($taskFilterForm, 'categories')
              ->label(false)
              ->checkboxList(
                ArrayHelper::map($categories, 'id', 'rus_name'),
                [
                  'item' => function ($index, $label, $name, $checked, $value) {
                    return '<label class="control-label">'
                      . Html::checkbox($name, $checked, ['value' => $value, 'class' => ''])
                      . ' ' . Html::encode($label)
                      . '</label><br>';
                  },
                  'unselect' => null,
                ]
              )
            ?>
          </div>
        </div>        <h4 class="head-card">Дополнительно</h4>
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
</main>
