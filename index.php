<html>
<body>
<?php
require_once 'src/task.php';

$task = new Task(customerId: 1);

$task->setCurrentStatus(Task::STATUS_IN_WORK);
echo "Текущий статус: " . Task::getStatusMap()[$task->getCurrentStatus()] . "<br>";

// Доступные действия в текущем статусе
$actions = $task->getAvailableActions($task->getCurrentStatus());
echo "Доступные действия для статуса ".$task->getCurrentStatus().":<br>";
foreach ($actions as $action => $rus_action) {
  $next = $task->getNextStatus($action);
  echo "-- {$rus_action} ({$action}) перейдёт в статус: " . Task::getStatusMap()[$next] . "<br>";
}
?>
</body>
</html>
