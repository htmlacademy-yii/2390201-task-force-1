<?php

namespace Romnosk\Models;

class ActionCancel extends ActionAbstract
{
  public function __construct()
  {
    parent::__construct('cancel', 'Отменить');
  }

  public function actionAllowed(int $userId, int $customerId, ?int $executorId): bool
  {
    return $customerId === $userId;
  }
}
