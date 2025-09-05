<?php

namespace Romnosk\Models;

class CSVReader
{
  protected $handler;

  public function __construct(string $filename) {
    try {
      $handler = fopen($filename, 'r');
      if (false === $handler) {
        throw new \RuntimeException('Невозможно открыть файл:'.$filename);
      }
      $this->handler = $handler;
      rewind($this->handler);

    } catch (\Throwable $e) {
        throw $e;
    }
  }

  // Возвращает в виде массива строку, на которую указывает указатель $this->handler, пока не достигнут конец файла. Если достигнут конец файла, возвращает false.
  public function next() {
    try {
      return fgetcsv($this->handler, null, ',', '"', '\\');

    } catch (\Throwable $e) {
        echo $e->getMessage();
    }
  }

  public function __destruct() {
    fclose($this->handler);
  }
}
