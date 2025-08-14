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

$task = new Task(customerId: 1);

$task->setCurrentStatus(Status::New);
echo "Текущий статус: " . $task->getCurrentStatus()->label() . "<br>";

// Доступные действия в текущем статусе
$actions = $task->getAvailableActions($task->getCurrentStatus());
echo "Доступные действия для статуса ".$task->getCurrentStatus()->label().":<br>";
foreach ($actions as $action => $rus_action) {
  $next = $task->getNextStatus($action);
  echo "-- {$rus_action} ({$action}) перейдёт в статус: " . $next->label() . "<br>";
}
?>
</body>
</html>
