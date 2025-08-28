<?php

namespace Romnosk\Models;

class User
{
  protected int $id;
  protected int $customerId;
  protected int $executorId;
  protected int $taskId;
  protected string $desсription;
  protected int $rating;
  protected DateTimeImmutable $date;

  public function __construct(
    int $id,
    int $customerId,
    int $executorId,
    int $taskId,
    string $desсription,
    int $rating
  )
  {
    $this->id = $id;
    $this->customerId = $customerId;
    $this->executorId = $executorId;
    $this->taskId = $taskId;
    $this->desсription = $desсription;
    $this->rating = $rating;
    $this->date = new DateTimeImmutable();
  }
}
