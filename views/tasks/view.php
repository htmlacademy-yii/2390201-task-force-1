<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\TaskStatusAndAction;
use app\models\TaskResponse;

// Переменные, необходимые для показа кнопок на странице
$taskStatusAndAction = new TaskStatusAndAction(Yii::$app->user->id, $task->customer_id, $task->executor_id);
$taskActions = $taskStatusAndAction->getAvailableActions($task->status_id);
$executorResponded = in_array(Yii::$app->user->id, ArrayHelper::getColumn($task->responses, 'executor_id'));

// Действия, необходимые для показа карты из Яндекс API
$apiKey = Yii::$app->params['yandexGeocoderApiKey'] ?? null;
$hasLocation = $task->location !== null && $task->location->latitude !== null && $task->location->longitude !== null;
$showMap = $apiKey && $hasLocation;

if ($showMap) {
  $latitude = $task->location->latitude;
  $longitude = $task->location->longitude;

  // Регистрируем скрипт API Яндекс.Карт
  $apiUrl = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=' . urlencode($apiKey);
  $this->registerJsFile($apiUrl);

  // Регистрируем инициализацию карты. Блок с картой на странице - <div id="map"></div>
  $js = <<<JS
    ymaps.ready(function () {
      var myMap = new ymaps.Map("map", {
        center: [$latitude, $longitude],
        zoom: 15
      });
    });
    JS;
  $this->registerJs($js, yii\web\View::POS_END);
}?>

<main class="main-content container">
  <div class="left-column">
    <div class="head-wrapper">
      <h3 class="head-main"><?=Html::encode($task->name)?></h3>
      <p class="price price--big"><?=Html::encode($task->budget)?>&nbsp;₽</p>
    </div>
    <p class="task-description"><?=Html::encode($task->description)?></p>
    <?= array_key_exists(TaskStatusAndAction::ACTION_RESPOND, $taskActions) && !$executorResponded
      ? '<a href="#" class="button button--blue action-btn" data-action="act_response">Откликнуться на задание</a>'
      :''
    ?>
    <?= array_key_exists(TaskStatusAndAction::ACTION_DECLINE, $taskActions)
      ? '<a href="#" class="button button--orange action-btn" data-action="refusal">Отказаться от задания</a>'
      :''
    ?>
    <?= array_key_exists(TaskStatusAndAction::ACTION_COMPLETE, $taskActions)
      ? '<a href="#" class="button button--pink action-btn" data-action="completion">Завершить задание</a>'
      :''
    ?>
    <div class="task-map">
      <?= $showMap ? '<div class="map" id="map" style="width: 725px; height: 346px;"></div>' : ''?>
      <?php if($hasLocation):?>
        <p class="map-address town"><?=Html::encode($task->location->name)?></p>
        <p class="map-address">Центр города</p>
      <?php else:?>
        <p class="map-address town">Задание без локации</p>
        <p class="map-address">Допускает удалённую работу,</p>
        <p class="map-address">или заказчик не указал локацию.</p>
      <?php endif;?>
    </div>
    <h4 class="head-regular">Отклики на задание</h4>
    <?php foreach($task->responses as $response): ?>
      <?php if(Yii::$app->user->id === $task->customer_id || Yii::$app->user->id === $response->executor->id):?>
        <div class="response-card">
          <img class="customer-photo" src="<?=Html::encode($response->executor->avatar)?>" width="146" height="156" alt="Фото исполнителя">
          <div class="feedback-wrapper">
            <a href="#" class="link link--block link--big"><?=Html::encode($response->executor->name)?></a>
            <div class="response-wrapper">
              <div class="stars-rating small"><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span>&nbsp;</span></div>
              <p class="reviews"><?=$response->executor->reviewsCount?> отзывов <b>рейтинг</b></p>
            </div>
            <p class="response-message"><?=Html::encode($response->description)?></p>
          </div>
          <div class="feedback-wrapper">
            <p class="info-text"><span class="current-time"></span><?=Html::encode($response->date)?></p>
            <p class="price price--small"><?=Html::encode($response->budget)?> ₽</p>
          </div>
          <?php if (Yii::$app->user->id === $task->customer_id && $task->status_id === TaskStatusAndAction::STATUS_NEW && !$response->declined): ?>
            <div class="button-popup">
              <form method="post" action="<?=Url::to(['tasks/accept-response', 'id' => $task->id]) ?>" >
                <?=Html::hiddenInput(\Yii::$app->request->csrfParam, \Yii::$app->request->getCsrfToken()) ?>
                <?=Html::hiddenInput('response_id', $response->id) ?>
                <?=Html::submitButton('Принять', ['class' => 'button button--blue button--small']) ?>
              </form>
              <form method="post" action="<?=Url::to(['tasks/decline-response', 'id' => $task->id]) ?>" style="display:inline;">
                <?=Html::hiddenInput(\Yii::$app->request->csrfParam, \Yii::$app->request->getCsrfToken()) ?>
                <?=Html::hiddenInput('response_id', $response->id) ?>
                <?=Html::submitButton('Отказать', ['class' => 'button button--orange button--small']) ?>
              </form>
            </div>
          <?php endif; ?>
        </div>
      <?php endif;?>
    <?php endforeach; ?>
  </div>
  <div class="right-column">
    <div class="right-card black info-card">
      <h4 class="head-card">Информация о задании</h4>
      <dl class="black-list">
        <dt>Категория</dt>
        <dd><?=Html::encode($task->category->rus_name)?></dd>
        <dt>Дата публикации</dt>
        <dd><?=Html::encode($task->date)?></dd>
        <dt>Срок выполнения</dt>
        <dd><?=Html::encode($task->deadline)?></dd>
        <dt>Статус</dt>
        <dd><?=Html::encode($task->status->rus_name)?></dd>
      </dl>
    </div>
    <div class="right-card white file-card">
      <h4 class="head-card">Файлы задания</h4>
      <ul class="enumeration-list">
        <li class="enumeration-item">
          <a href="#" class="link link--block link--clip">my_picture.jpg</a>
          <p class="file-size">356 Кб</p>
        </li>
        <li class="enumeration-item">
          <a href="#" class="link link--block link--clip">information.docx</a>
          <p class="file-size">12 Кб</p>
        </li>
      </ul>
    </div>
  </div>
</main>
<section class="pop-up pop-up--refusal pop-up--close">
  <div class="pop-up--wrapper">
    <h4>Отказ от задания</h4>
    <p class="pop-up-text">
      <b>Внимание!</b><br>
      Вы собираетесь отказаться от выполнения этого задания.<br>
      Это действие плохо скажется на вашем рейтинге и увеличит счетчик проваленных заданий.
    </p>
    <form method="post" action="<?=Url::to(['tasks/decline', 'id' => $task->id]) ?>" >
      <?=Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
      <?=Html::submitButton('Отказаться', ['class' => 'button button--pop-up button--orange']) ?>
    </form>
    <div class="button-container">
      <button class="button--close" type="button">Закрыть окно</button>
    </div>
  </div>
</section>
<section class="pop-up pop-up--completion pop-up--close">
  <div class="pop-up--wrapper">
    <h4>Завершение задания</h4>
    <p class="pop-up-text">
      Вы собираетесь отметить это задание как выполненное.
      Пожалуйста, оставьте отзыв об исполнителе и отметьте отдельно, если возникли проблемы.
    </p>
    <div class="completion-form pop-up--form regular-form">
      <?php $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ['tasks/complete', 'id' => $task->id]
      ]);?>
        <?= $form->field($customerReview, 'description')->textarea(['id' => 'completion-comment', 'rows' => 5])->label('Ваш комментарий') ?>
        <?= $form->field($customerReview, 'rating')->textInput(['id' => 'completion-rating', 'type' => 'number', 'min' => 1, 'max' => 5])->label('Оценка работы (от 1 до 5)') ?>
        <?=Html::submitButton('Завершить', ['class' => 'button button--pop-up button--blue']) ?>
      <?php ActiveForm::end(); ?>
    </div>
    <div class="button-container">
      <button class="button--close" type="button">Закрыть окно</button>
    </div>
  </div>
</section>
<section class="pop-up pop-up--act_response pop-up--close">
  <div class="pop-up--wrapper">
    <h4>Добавление отклика к заданию</h4>
    <p class="pop-up-text">
      Вы собираетесь оставить свой отклик к этому заданию.
      Пожалуйста, укажите стоимость работы и добавьте комментарий, если необходимо.
    </p>
    <div class="addition-form pop-up--form regular-form">
      <?php $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ['tasks/respond', 'id' => $task->id]
      ]);?>
        <?= $form->field($taskResponse, 'description')->textarea(['id' => 'addition-comment', 'rows' => 5])->label('Ваш комментарий') ?>
        <?= $form->field($taskResponse, 'budget')->textInput(['id' => 'addition-price', 'type' => 'number', 'min' => 1])->label('Бюджет') ?>
        <?=Html::submitButton('Завершить', ['class' => 'button button--pop-up button--blue']) ?>
      <?php ActiveForm::end(); ?>
    </div>
    <div class="button-container">
      <button class="button--close" type="button">Закрыть окно</button>
    </div>
  </div>
</section>
<div class="overlay"></div>
<script src="js/main.js"></script>
