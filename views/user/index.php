<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<main class="main-content container">
  <div class="left-column">
    <h3 class="head-main"><?=Html::encode($user->name)?></h3>
    <div class="user-card">
      <div class="photo-rate">
        <img class="card-photo" src="<?=Html::encode($user->avatar)?>" width="191" height="190" alt="Фото пользователя">
        <div class="card-rate">
          <div class="stars-rating big"><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span>&nbsp;</span></div>
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
        <p class="bio-info"><span class="country-info">Россия</span>, <span class="town-info"><?=$user->town->name?></span>, <span class="age-info"><?=$user->birth_date?></span> лет</p>
      </div>
    </div>
    <?php if($user->customerReviews): ?>
      <h4 class="head-regular">Отзывы заказчиков</h4>
      <?php foreach($user->customerReviews as $review): ?>
        <div class="response-card">
          <img class="customer-photo" src="<?=Html::encode($review->customer->avatar)?>" width="120" height="127" alt="Фото заказчиков">
          <div class="feedback-wrapper">
            <p class="feedback"><?=Html::encode($review->description)?></p>
            <p class="task">Задание «<a href="#" class="link link--small"><?=Html::encode($review->task->name)?></a>» выполнено</p>
          </div>
          <div class="feedback-wrapper">
            <div class="stars-rating small"><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span>&nbsp;</span></div>
            <p class="info-text"><span class="current-time"><?=Html::encode($review->date)?> </span>назад</p>
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
        <dd><i>25 место - править</i></dd>
        <dt>Дата регистрации</dt>
        <dd><?=Html::encode($user->reg_date)?></dd>
        <dt>Статус</dt>
        <dd><i>Открыт для новых заказов - править</i></dd>
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
