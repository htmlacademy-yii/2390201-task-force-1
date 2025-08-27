<?php

namespace Romnosk\Models;

use Romnosk\Models\ActionAbstract;
use Romnosk\Models\ActionCancel;
use Romnosk\Models\ActionRespond;
use Romnosk\Models\ActionComplete;
use Romnosk\Models\ActionDecline;

enum Action: string
{
  case Cancel     = 'cancel';
  case Respond    = 'respond';
  case Complete   = 'complete';
  case Decline    = 'decline';

  /**
   * Возвращает объект, соответствующий действию
   * @return ActionAbstract
   */
  public function actionObject(): ActionAbstract
  {
    return match($this) {
      self::Cancel     => new ActionCancel(),
      self::Respond    => new ActionRespond(),
      self::Complete   => new ActionComplete(),
      self::Decline    => new ActionDecline(),
    };
  }

  /**
   * Возвращает карту всех действий (value => actionObject)
   *
   * @return array<string, ActionAbstract> ['value' => actionObject]
   */
  public static function map(): array
  {
    return [
      self::Cancel->value   => self::Cancel->actionObject(),
      self::Respond->value  => self::Respond->actionObject(),
      self::Complete->value => self::Complete->actionObject(),
      self::Decline->value  => self::Decline->actionObject(),
    ];
  }
}
