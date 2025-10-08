<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<main class="main-content container">
  <div class="left-menu">
    <h3 class="head-main head-task">Мои задания</h3>
    <?php if(Yii::$app->user->identity->is_executor):?>
      <ul class="side-menu-list">
        <li class="side-menu-item<?= $status === null || $status === 'in-progress' ? ' side-menu-item--active' : '' ?>">
          <a href="<?= Url::to(['tasks/my', 'status' => 'in-progress']) ?>" class="link link--nav">В процессе</a>
        </li>
        <li class="side-menu-item<?= $status === 'overdue' ? ' side-menu-item--active' : '' ?>">
          <a href="<?= Url::to(['tasks/my', 'status' => 'overdue']) ?>" class="link link--nav">Просрочено</a>
        </li>
        <li class="side-menu-item<?= $status === 'closed' ? ' side-menu-item--active' : '' ?>">
          <a href="<?= Url::to(['tasks/my', 'status' => 'closed']) ?>" class="link link--nav">Закрытые</a>
        </li>
      </ul>
    <?php else:?>
      <ul class="side-menu-list">
        <li class="side-menu-item<?= ($status === null || $status === 'new') ? ' side-menu-item--active' : '' ?>">
          <a href="<?= Url::to(['tasks/my', 'status' => 'new']) ?>" class="link link--nav">Новые</a>
        </li>
        <li class="side-menu-item<?= $status === 'in-progress' ? ' side-menu-item--active' : '' ?>">
          <a href="<?= Url::to(['tasks/my', 'status' => 'in-progress']) ?>" class="link link--nav">В процессе</a>
        </li>
        <li class="side-menu-item<?= $status === 'closed' ? ' side-menu-item--active' : '' ?>">
          <a href="<?= Url::to(['tasks/my', 'status' => 'closed']) ?>" class="link link--nav">Закрытые</a>
        </li>
      </ul>
    <?php endif;?>
  </div>

  <div class="left-column left-column--task">
    <?php if (!$tasks): ?>
      <h3 class="head-main head-task">Заданий нет</h3>
    <?php else: ?>
      <h3 class="head-main head-task">Задания</h3>
      <?php foreach ($tasks as $task): ?>
        <div class="task-card">
          <div class="header-task">
            <a  href="#" class="link link--block link--big"><?= Html::encode($task->name) ?></a>
            <p class="price price--task"><?= $task->budget ? Html::encode($task->budget).' ₽' : 'Не указан' ?></p>
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
    <?php endif; ?>
  </div>
</main>
