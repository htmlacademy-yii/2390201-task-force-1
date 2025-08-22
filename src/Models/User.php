<?php

namespace Romnosk\Models;

class User
{
  protected int $id;
  protected string $name;
  protected string $email;
  protected string $password;
  protected int $townId;
  protected bool $isExecutor;
  protected DateTimeImmutable $registrationDate;
  protected ?string $avatar;
  protected ?DateTimeImmutable $birthDate;
  protected ?string $phone;
  protected ?string $telegram;
  protected ?string $information;
  protected ?int $rating;

  public function __construct(
    int $id,
    string $name,
    string $email,
    string $password,
    int $townId,
    bool $isExecutor,
    ?string $avatar,
    ?DateTimeImmutable $birthDate,
    ?string $phone,
    ?string $telegram,
    ?string $information
  )
  {
    $this->id = $id;
    $this->name = $name;
    $this->email = $email;
    $this->password = $password;
    $this->townId = $townId;
    $this->isExecutor = $isExecutor;
    $this->registrationDate = new DateTimeImmutable();
    $this->avatar = $avatar;
    $this->birthDate = $birthDate;
    $this->phone = $phone;
    $this->telegram = $telegram;
    $this->information = $information;
    $this->rating = 0;
  }
}
