<?php

namespace Romnosk\Models;

class ActionRespond extends ActionAbstract
{
  public function __construct()
  {
    parent::__construct('respond', 'Откликнуться');
  }

  public function actionAllowed(int $userId, int $customerId, ?int $executorId): bool
  {
    return $customerId !== $userId;
  }
}
