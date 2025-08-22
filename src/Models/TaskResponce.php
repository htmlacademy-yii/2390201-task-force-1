<?php

namespace Romnosk\Models;

class TaskResponce
{
  protected int $id;
  protected int $executorId;
  protected string $description;
  protected int $budget;
  protected bool $accepted;
  protected DateTimeImmutable $date;

  public function __construct(
    int $id,
    int $executorId,
    string $description,
    int $budget,
    bool $accepted,
    ?DateTimeImmutable $date
  )
  {
    $this->id = $id;
    $this->executorId = $executorId;
    $this->description = $description;
    $this->budget = $budget;
    $this->accepted = $accepted;
    $this->date = new DateTimeImmutable();
  }
}
