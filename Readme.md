# Личный проект «TaskForce»
Для запуска приложения в директорию `config` необходимо поместить файлы:<br>
1. `web.local.php`, который содержит
```php
<?php
return [
  'components' => [
    'request' => [
      'cookieValidationKey' => 'ваш_ключ_валидации_cookie',
    ],
    'authClientCollection' => [
      'class' => 'yii\authclient\Collection',
      'clients' => [
        'vkontakte' => [
          'class' => 'yii\authclient\clients\VKontakte',
          'clientId' => 'ваш_id_приложения_ВК',       // ID приложения из ВК-приложения
          'clientSecret' => 'ваш_защищённый_ключ_ВК', // защищённый ключ из ВК-приложения
          'scope' => 'email',
        ],
      ],
    ],
  ]
];
```
2. `params.local.php`, который содержит
```php
<?php
return [
  'yandexGeocoderApiKey' => 'ваш_ключ_API_Геокодера_Яндекс',
];
```
допустимы пустые ключи, но тогда соответствующий функционал не будет работать.

В файле `config/db.php` нужно указать параметры подключения к вашей базе данных MySQL.

* Студент: [Роман Носков](https://htmlacademy.ru/profile/id2390201).
* Наставник: [Сергей Попов](https://htmlacademy.ru/profile/id1181399).

---

<a href="https://htmlacademy.ru/intensive/php2"><img align="left" width="50" height="50" alt="HTML Academy" src="https://up.htmlacademy.ru/static/img/intensive/yii/logo-for-github-2.png"></a>

Репозиторий создан для обучения на профессиональном онлайн‑курсе «[PHP, уровень 2](https://htmlacademy.ru/intensive/php2)» от [HTML Academy](https://htmlacademy.ru).
