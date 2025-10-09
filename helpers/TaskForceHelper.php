<?php
namespace app\helpers;

use DateTime;

class TaskForceHelper
{

  /**
   * Возвращает корректную форму множественного числа
   * Ограничения: только для целых чисел
   *
   * Пример использования:
   * $remaining_minutes = 5;
   * echo "Я поставил таймер на {$remaining_minutes} " .
   *     getNounPluralForm(
   *         $remaining_minutes,
   *         'минута',
   *         'минуты',
   *         'минут'
   *     );
   * Результат: "Я поставил таймер на 5 минут"
   *
   * @param int $number Число, по которому вычисляем форму множественного числа
   * @param string $one Форма единственного числа: яблоко, час, минута
   * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
   * @param string $many Форма множественного числа для остальных чисел
   *
   * @return string Рассчитанная форма множественного числа
   */
  public static function getNounPluralForm(int $number, string $one, string $two, string $many): string
  {
      $number = (int) $number;
      $mod10 = $number % 10;
      $mod100 = $number % 100;

      switch (true) {
          case ($mod100 >= 11 && $mod100 <= 20):
              return $many;

          case ($mod10 > 5):
              return $many;

          case ($mod10 === 1):
              return $one;

          case ($mod10 >= 2 && $mod10 <= 4):
              return $two;

          default:
              return $many;
      }
  }

  /**
   * Определяет разницу в "человеческом" формате между текущем временем и $make_time
   *
   */
  public static function humanTimeDiff(string $make_time): string
  {
    $timestamp = strtotime($make_time);
    if (!$timestamp) {
      return "Неверная дата";
    }

    $now = time();
    $diff = $timestamp - $now;
    $abs_diff = abs($diff);

    if ($diff >= 0) {
      return "Только что";
    }
    if ($abs_diff < 60) {
      return "$abs_diff " . self::getNounPluralForm($abs_diff, "секунда", "секунды", "секунд") . " назад";
    }
    $minutes = floor($abs_diff / 60);
    if ($abs_diff < 3600) {
      return "$minutes " . self::getNounPluralForm($minutes, "минута", "минуты", "минут") . " назад";
    }
    $hours = floor($abs_diff / 3600);
    if ($abs_diff < 86400) {
      return "$hours " . self::getNounPluralForm($hours, "час", "часа", "часов") . " назад";
    }
    return date("d.m.Y \в H:i", $timestamp);
  }

  /**
   * Возвращает строку возраста в годах человека с датой рождения $birthDate
   *
   * @param string $birthDate — строка в формате, понятном DateTime, например 'Y-m-d'
   * @return string строка вида: 1 год / 2-3-4 года / 5-6-7-8-9-10 лет
   */
  public static function getAge(string $birthDate): string
  {
    if (!$birthDate) {
      return '';
    }

    $birth = new DateTime($birthDate);
    $now = new DateTime();
    if ($birth > $now) {
        return '';
    }

    $age = $now->diff($birth)->y;
    return $age.' '.self::getNounPluralForm($age, 'год', 'года', 'лет');
  }

  /**
   * Рендерит рейтинг в строку - выдаёт его в виде звёздочек
   *
   * @param string $rating - число от 0 до 500 - так мы храним рейтинг в отзыве
   * @param string $size - строка, должна быть либо 'big' либо 'small'
   * @return string строка блока <div> для вставки в представление по типу:
   *                <div class="stars-rating big">
   *                  <span class="fill-star">&nbsp;</span>
   *                  <span class="fill-star">&nbsp;</span>
   *                  <span class="fill-star">&nbsp;</span>
   *                  <span class="fill-star">&nbsp;</span>
   *                  <span>&nbsp;</span>
   *                 </div>
   */
  public static function renderStarsRating(int $rating, string $size): string
  {
    $filledStars = (int)round($rating/ 100);
    $output = '<div class="stars-rating '.$size.'">';

    for ($i = 1; $i <= 5; $i++) {
      $output .= ($i<=$filledStars)
        ? '<span class="fill-star">&nbsp;</span>'
        : '<span>&nbsp;</span>';
    }
    $output .= '</div>';
    return $output;
  }
}
