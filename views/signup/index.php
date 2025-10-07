<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Location;
?>

<main class="container container--registration">
  <div class="center-block">
    <div class="registration-form regular-form">
      <?php $form = ActiveForm::begin([
        'method' => 'post',
        'fieldConfig' => [
          'template' => "{label}\n{input}\n{error}",
          'labelOptions' => ['class' => 'control-label'],
          'errorOptions' => ['class' => 'help-block'],
        ],
      ]); ?>

        <h3 class="head-main head-task">Регистрация нового пользователя</h3>

        <?= $form->field($signupForm, 'name')->textInput(['id' => 'username']) ?>

        <div class="half-wrapper">
          <?= $form->field($signupForm, 'email')->textInput(['id' => 'email-user', 'type' => 'email']) ?>

          <?= $form->field($signupForm, 'location_id')->dropDownList(
            ArrayHelper::map(Location::find()->all(), 'id', 'name'),
            ['id' => 'town-user', 'prompt' => 'Выберите город']
          ) ?>
        </div>

        <div class="half-wrapper">
          <?= $form->field($signupForm, 'password')->passwordInput(['id' => 'password-user']) ?>
        </div>

        <div class="half-wrapper">
          <?= $form->field($signupForm, 'password_repeat')->passwordInput(['id' => 'password-repeat-user']) ?>
        </div>

        <div class="form-group">
          <label class="control-label checkbox-label" for="response-user">
            <?= Html::activeCheckbox($signupForm, 'is_executor', [
              'id' => 'response-user',
              'class' => 'checkbox-input',
            ]) ?>
            я собираюсь откликаться на заказы
          </label>
        </div>

        <?= Html::submitButton('Создать аккаунт', ['class' => 'button button--blue']) ?>
      <?php ActiveForm::end(); ?>
    </div>
  </div>
</main>
