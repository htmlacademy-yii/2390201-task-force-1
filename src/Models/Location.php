<?php

namespace Romnosk\Models;

class Location
{
  protected int $id;
  protected string $name;
  protected string $latitude;
  protected string $longitude;

  public function __construct(
    int $id,
    string $name,
    string $latitude,
    string $longitude
  )
  {
    $this->id = $id;
    $this->name = $name;
    $this->latitude = $latitude;
    $this->longitude = $longitude;
  }
}
