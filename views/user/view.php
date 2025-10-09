<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\TaskForceHelper;
?>
<main class="main-content container">
  <div class="left-column">
    <h3 class="head-main"><?=Html::encode($user->name)?></h3>
    <div class="user-card">
      <div class="photo-rate">
        <img class="card-photo" src="<?=Html::encode($user->avatar)?>" width="191" height="190" alt="Фото пользователя">
        <div class="card-rate">
        <?=TaskForceHelper::renderStarsRating($user->rating, 'big')?>
        <span class="current-rate"><?=number_format($user->rating / 100, 2, '.', '')?></span>
        </div>
      </div>
      <p class="user-description"><?=Html::encode($user->information)?></p>
    </div>
    <div class="specialization-bio">
      <div class="specialization">
        <p class="head-info">Специализации</p>
        <ul class="special-list">
          <?php foreach($user->categories as $userCategory): ?>
            <li class="special-item">
              <a href="#" class="link link--regular"><?=Html::encode($userCategory->category->rus_name)?></a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="bio">
        <p class="head-info">Био</p>
        <p class="bio-info">
          <span class="country-info">Россия</span>,
          <span class="town-info"><?=Html::encode($user->location->name)?></span>,
          <span class="age-info"><?=Html::encode(TaskForceHelper::getAge($user->birth_date))?></span>
        </p>
      </div>
    </div>
    <?php if($user->customerReviews): ?>
      <h4 class="head-regular">Отзывы заказчиков</h4>
      <?php foreach($user->customerReviews as $review): ?>
        <div class="response-card">
          <img class="customer-photo" src="<?=Html::encode($review->customer->avatar)?>" width="120" height="127" alt="Фото заказчиков">
          <div class="feedback-wrapper">
            <p class="feedback"><?=Html::encode($review->description)?></p>
            <p class="task">Задание «<a href="<?=Url::to(['tasks/view', 'id' => $review->task->id])?>" class="link link--small"><?=Html::encode($review->task->name)?></a>» выполнено</p>
          </div>
          <div class="feedback-wrapper">
            <?=TaskForceHelper::renderStarsRating($review->rating, 'small')?>
            <p class="info-text"><span class="current-time"><?=Html::encode(TaskForceHelper::humanTimeDiff($review->date))?></span></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <div class="right-column">
    <div class="right-card black">
      <h4 class="head-card">Статистика исполнителя</h4>
      <dl class="black-list">
        <dt>Всего заказов</dt>
        <dd><?=$user->tasksCount?> выполнено, <?=$user->failedTasksCount?> провалено</dd>
        <dt>Место в рейтинге</dt>
        <dd>2 место</dd>
        <dt>Дата регистрации</dt>
        <dd><?= Yii::$app->formatter->asDate($user->reg_date, 'php:d.m.Y') ?></dd>
        <dt>Статус</dt>
        <dd>Открыт для новых заказов</dd>
      </dl>
    </div>
    <div class="right-card white">
      <h4 class="head-card">Контакты</h4>
      <ul class="enumeration-list">
        <li class="enumeration-item">
          <a href="#" class="link link--block link--phone"><?=Html::encode($user->phone)?></a>
        </li>
        <li class="enumeration-item">
          <a href="#" class="link link--block link--email"><?=Html::encode($user->email)?></a>
        </li>
        <li class="enumeration-item">
          <a href="#" class="link link--block link--tg"><?=Html::encode($user->telegram)?></a>
        </li>
      </ul>
    </div>
  </div>
</main>
