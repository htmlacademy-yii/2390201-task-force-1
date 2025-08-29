<?php

namespace Romnosk\Models;

class ActionComplete extends ActionAbstract
{
  public function __construct()
  {
    parent::__construct('complete', 'Выполнено');
  }

  public function actionAllowed(int $userId, int $customerId, ?int $executorId): bool
  {
    return $customerId === $userId;
  }
}
