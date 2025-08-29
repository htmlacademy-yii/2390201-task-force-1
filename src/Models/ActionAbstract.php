<?php

namespace Romnosk\Models;

abstract class ActionAbstract
{
  protected string $name;
  protected string $label;

  public function __construct(string $name, string $label)
  {
    $this->name = $name;
    $this->label = $label;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getLabel(): string
  {
    return $this->label;
  }

  abstract public function actionAllowed(int $userId, int $customerId, ?int $executorId): bool;
}
