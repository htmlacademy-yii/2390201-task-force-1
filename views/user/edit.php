<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Location;
?>
<main class="main-content main-content--left container">
  <div class="left-menu left-menu--edit">
    <h3 class="head-main head-task">Настройки</h3>
    <ul class="side-menu-list">
      <li class="side-menu-item side-menu-item--active">
        <a class="link link--nav">Мой профиль</a>
      </li>
      <li class="side-menu-item">
        <a href="#" class="link link--nav">Безопасность</a>
      </li>
    </ul>
  </div>

  <div class="my-profile-form">
    <?php $form = ActiveForm::begin([
      'options' => ['enctype' => 'multipart/form-data'],
      'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'labelOptions' => ['class' => 'control-label'],
        'errorOptions' => ['class' => 'help-block'],
        'options' => ['class' => 'form-group'],
      ],
    ]); ?>

    <h3 class="head-main head-regular">Мой профиль</h3>

    <div class="photo-editing">
      <div>
        <p class="form-label">Аватар</p>
        <img class="avatar-preview" src="<?= $user->avatar ? Yii::getAlias('@web/' . $user->avatar) : '/img/man-glasses.png' ?>" width="83" height="83">
      </div>
      <?= $form->field($user, 'avatar')->fileInput(['id' => 'button-input', 'style' => 'display:none'])->label(false) ?>
      <label for="button-input" class="button button--black"> Сменить аватар</label>
    </div>

    <?= $form->field($user, 'name')->textInput(['id' => 'profile-name']) ?>

    <div class="half-wrapper">
      <?= $form->field($user, 'email')->input('email', ['id' => 'profile-email']) ?>
      <?= $form->field($user, 'birth_date_view')->textInput(['placeholder' => 'дд.мм.гггг']) ?>
    </div>

    <div class="half-wrapper">
      <?= $form->field($user, 'phone')->input('tel', ['id' => 'profile-phone']) ?>
      <?= $form->field($user, 'telegram')->textInput(['id' => 'profile-tg']) ?>
    </div>

    <?= $form->field($user, 'information')->textarea(['id' => 'profile-info', 'rows' => 4]) ?>

    <div class="form-group">
      <p class="form-label">Выбор специализаций</p>
      <div class="checkbox-profile">
        <!--?= $form->field($user, 'selectedCategoryIds')->checkboxList(
          ArrayHelper::map($allCategories, 'id', 'rus_name'),
          ['unselect' => null]
        )->label(false) ?-->
        <!--?= $form->field($user, 'selectedCategoryIds')
          ->label(false)
          ->checkboxList(ArrayHelper::map($allCategories, 'id', 'rus_name'))
        ?-->
        <?= $form->field($user, 'selectedCategoryIds')
          ->label(false)
          ->checkboxList(
            ArrayHelper::map($allCategories, 'id', 'rus_name'),
            [
              'item' => function ($index, $label, $name, $checked, $value) {
                return '<label class="control-label">'
                  . Html::checkbox($name, $checked, ['value' => $value, 'class' => ''])
                  . ' ' . Html::encode($label)
                  . '</label>';
              },
              'unselect' => null,
            ]
          )
        ?>
      </div>
    </div>

    <?= Html::submitButton('Сохранить', ['class' => 'button button--blue']) ?>

    <?php ActiveForm::end(); ?>
  </div>
</main>
