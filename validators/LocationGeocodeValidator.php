<?php

namespace app\validators;

use Yii;
use yii\validators\Validator;
use app\models\Location;

class LocationGeocodeValidator extends Validator
{
  public function validateAttribute($model, $attribute)
  {
    $name = trim($model->$attribute);
    if (empty($name)) {
      return;
    }

    try {
      $coordinates = $this->geocode($name);
      if (!$coordinates) {
        $this->addError($model, $attribute, 'Не удалось определить координаты по указанному адресу.');
        return;
      }

      // Создаём или находим локацию
      $location = Location::findOrCreateByName($name, $coordinates['lat'], $coordinates['lon']);
      if (!$location) {
        $this->addError($model, $attribute, 'Не удалось сохранить местоположение.');
        return;
      }

      // Присваиваем ID локации в задаче
      $model->location_id = $location->id;
    } catch (\Exception $e) {
      // throw new \Exception("Geocoding error: " . $e->getMessage()); // для отладки
      $this->addError($model, $attribute, 'Сервис геокодирования временно недоступен. Попробуйте позже.');
    }
  }

  protected function geocode($address)
  {
    $apiKey = Yii::$app->params['yandexGeocoderApiKey'] ?? null;
    if (!$apiKey) {
      throw new \yii\base\InvalidConfigException('Не указан API-ключ Яндекс.Геокодера в params.');
    }

    $url = 'https://geocode-maps.yandex.ru/v1/';
    $query = http_build_query([
      'apikey' => $apiKey,
      'geocode' => $address,
      'format' => 'json',
      'results' => 1,
    ]);

    $context = stream_context_create([
      'http' => [
        'timeout' => 10,
        'user_agent' => 'Yii2 App',
      ],
    ]);

    $response = file_get_contents($url . '?' . $query, false, $context);
    if ($response === false) {
      throw new \RuntimeException('Не удалось подключиться к Яндекс.Геокодеру.');
    }

    $data = json_decode($response, true);
    if (!isset($data['response']['GeoObjectCollection']['featureMember'][0])) {
      return null;
    }

    $pos = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
    $coordinates = explode(' ', $pos);

    return [
      'lon' => $coordinates[0],
      'lat' => $coordinates[1],
    ];
  }
}
