<?php

namespace Romnosk\Models;

class User
{
  protected int $id;
  protected int $customerId;
  protected int $executorId;
  protected int $taskId;
  protected string $desription;
  protected int $rating;
  protected DateTimeImmutable $date;

  public function __construct(
    int $id,
    int $customerId,
    int $executorId,
    int $taskId,
    string $desription,
    int $rating
  )
  {
    $this->id = $id;
    $this->customerId = $customerId;
    $this->executorId = $executorId;
    $this->taskId = $taskId;
    $this->desription = $desription;
    $this->rating = $rating;
    $this->date = new DateTimeImmutable();
  }
}
