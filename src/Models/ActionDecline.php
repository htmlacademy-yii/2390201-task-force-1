<?php

namespace Romnosk\Models;

class ActionDecline extends ActionAbstract
{
  public function __construct()
  {
    parent::__construct('decline', 'Отказаться');
  }

  public function actionAllowed(int $userId, int $customerId, ?int $executorId): bool
  {
    return $executorId && ($executorId === $userId);
  }
}
