<?php

namespace app\validators;

use yii\validators\Validator;
use DateTime;

class UserDateValidator extends Validator
{
  public string $inputFormat = 'd.m.Y';
  public string $outputFormat = 'Y-m-d H:i:s';

  public function validateAttribute($model, $attribute)
  {
    $value = $model->$attribute;

    if (empty($value)) {
      $model->birth_date = null;
      return;
    }

    $date = DateTime::createFromFormat($this->inputFormat, $value);

    if ($date === false || $date->format($this->inputFormat) !== $value) {
      $this->addError($model, $attribute, 'Неверный формат даты. Используйте дд.мм.гггг.');
      return;
    }
    $model->birth_date = $date->format($this->outputFormat);
  }
}
