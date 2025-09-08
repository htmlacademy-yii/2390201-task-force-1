<?php

namespace Romnosk\Models;

use Romnosk\Models\CSVReader;

// Генерирует .sql - файл с именем tableName.sql для создания таблицы с именем tableName из csv-файла с именем csvFilename.
class DBTableGenerator
{
  protected CSVReader $reader;
  protected string $tableName;
  protected array | false $row; // строка, которую читаем из .csv-файла
  public string $filename; //имя создаваемого файла - скрипта SQL

  public function __construct(string $csvFilename, string $tableName)
  {
    $this->reader = new CSVReader($csvFilename);
    $this->tableName = $tableName;
    $this->filename = sys_get_temp_dir().'/' . $tableName . '.sql';
  }

  // Генерирует заголовок для команды INSERT для вставки таблицы в MySQL
  private function generateHeader(): string
  {
    if (count($this->row) === 0) {
      return '';
    }
    $result = "INSERT INTO ".$this->tableName." (".$this->row[0];
    for($i=1; $i<count($this->row); $i++) {
      $result = $result .",".$this->row[$i];
    }
    $result = $result.")".PHP_EOL." VALUES ";
    return $result;
  }

  // Генерирует строку таблицы БД в формате INSERT для MySQL
  private function generateRow(): string
  {
    if (count($this->row) === 0) {
      return '';
    }
    $result = "('".$this->row[0]."'";
    for($i=1; $i<count($this->row); $i++) {
      $result = $result .",'".$this->row[$i]."'";
    }
    return $result.")";
  }

  public function generate(): void
  {
    try {
      // Используем SplFileObject в режиме записи (создаст или перезапишет файл)
      $file = new \SplFileObject($this->filename, 'w');

      $this->row = $this->reader->next();
      if ($this->row !== false) {
        $file->fwrite($this->generateHeader());
        $this->row = $this->reader->next();

        if ($this->row !== false) { //Первая строка отличается - нет запятой
          $file->fwrite(PHP_EOL . $this->generateRow());
          $this->row = $this->reader->next();
        }
      }
      while ($this->row !== false) {
        $file->fwrite(',' . PHP_EOL . $this->generateRow());
        $this->row = $this->reader->next();
      }
      $file->fwrite(';');

    } catch (Exception $e) {
        throw new \RuntimeException('Не удалось записать в файл: ' . $e->getMessage());
    }
  }
}
