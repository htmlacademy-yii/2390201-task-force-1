<?php

namespace Romnosk\Models;

enum Status: string
{
  case New      = 'new';
  case Canceled = 'canceled';
  case InWork   = 'in_work';
  case Done     = 'done';
  case Failed   = 'failed';

  /**
   * Возвращает ID статуса
   *
   * @return int
   */
  public function id(): int
  {
    return match($this) {
      self::New      => 1,
      self::Canceled => 2,
      self::InWork   => 3,
      self::Done     => 4,
      self::Failed   => 5,
    };
  }

  /**
   * Возвращает отображаемое имя статуса
   *
   * @return string
   */
  public function label(): string
  {
    return match($this) {
      self::New      => 'Новое',
      self::Canceled => 'Отменено',
      self::InWork   => 'В работе',
      self::Done     => 'Выполнено',
      self::Failed   => 'Провалено',
    };
  }

  /**
   * Возвращает карту всех статусов (value => label)
   *
   * @return array<string, string> ['value' => 'label']
   */
  public static function map(): array
  {
    return [
      self::New->value      => self::New->label(),
      self::Canceled->value => self::Canceled->label(),
      self::InWork->value   => self::InWork->label(),
      self::Done->value     => self::Done->label(),
      self::Failed->value   => self::Failed->label(),
    ];
  }
}
