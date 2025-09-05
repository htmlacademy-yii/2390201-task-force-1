<html>
<body>
<?php
// Включаем показ ошибок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

use Romnosk\Models\Task;
use Romnosk\Models\Status;
use Romnosk\Models\Action;
use Romnosk\Models\CSVReader;
use Romnosk\Models\DBTableGenerator;

$customerId = 1;
$executorId = 2;
$userId = 1;

$task = new Task($customerId);
$task->setExecutorId($executorId);
$task->setCurrentStatus(Status::InWork);
echo "Текущий статус: " . $task->getCurrentStatus()->label() . "<br>";

// Доступные действия в текущем статусе
$actions = $task->getAvailableActions($task->getCurrentStatus(),$userId);
echo "Доступные действия для статуса ".$task->getCurrentStatus()->label().":<br>";
foreach ($actions as $action => $actionObject) {
  $next = $task->getNextStatus($action);
  echo "-- {$actionObject->getLabel()} ({$action}) перейдёт в статус: " . $next->label() . "<br>";
}

$csvFilename = './data/categories.csv';
$reader = new CSVReader($csvFilename);
$row = $reader->next();
while ($row !== false) {
  echo json_encode($row, JSON_UNESCAPED_UNICODE)."<br>";
  $row = $reader->next();
}

$tableName = 'specializations';
$generator = new DBTableGenerator($csvFilename, $tableName);

try {
  $generator->generate();
  echo "Данные успешно записаны в файл ".$generator->filename."<br>";
} catch (RuntimeException $e) {
  echo "Ошибка: " . $e->getMessage() . "<br>";
}


?>
</body>
</html>
