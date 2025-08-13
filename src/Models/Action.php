<?php

namespace Romnosk\Models;

enum Action: string
{
  case Cancel     = 'cancel';
  case Respond    = 'respond';
  case MarkDone   = 'mark_done';
  case Decline    = 'decline';

  /**
   * Возвращает отображаемое имя действия
   *
   * @return string
   */
  public function label(): string
  {
    return match($this) {
      self::Cancel     => 'Отменить',
      self::Respond    => 'Откликнуться',
      self::MarkDone   => 'Выполнено',
      self::Decline    => 'Отказаться',
    };
  }

  /**
   * Возвращает карту всех действий (value => label)
   *
   * @return array<string, string> ['value' => 'label']
   */
  public static function map(): array
  {
    return [
      self::Cancel->value   => self::Cancel->label(),
      self::Respond->value  => self::Respond->label(),
      self::MarkDone->value => self::MarkDone->label(),
      self::Decline->value  => self::Decline->label(),
    ];
  }
}
