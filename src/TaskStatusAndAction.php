<?php
namespace Romnosk;

class TaskStatusAndAction
{
  // Константы статусов
  const STATUS_NEW      = 1;
  const STATUS_CANCELED = 2;
  const STATUS_IN_WORK  = 3;
  const STATUS_DONE     = 4;
  const STATUS_FAILED   = 5;

  // Константы действий
  const ACTION_CANCEL     = 1;
  const ACTION_RESPOND    = 2;
  const ACTION_COMPLETE   = 3;
  const ACTION_DECLINE    = 4;

  // Карта статусов (внутреннее имя => отображаемое имя)
  private static $statusMap = [
    self::STATUS_NEW      => 'Новое',
    self::STATUS_CANCELED => 'Отменено',
    self::STATUS_IN_WORK  => 'В работе',
    self::STATUS_DONE     => 'Выполнено',
    self::STATUS_FAILED   => 'Провалено',
  ];

  // Карта действий (внутреннее имя => отображаемое имя)
  private static $actionMap = [
    self::ACTION_CANCEL     => 'Отменить',
    self::ACTION_RESPOND    => 'Откликнуться',
    self::ACTION_COMPLETE   => 'Выполнено',
    self::ACTION_DECLINE    => 'Отказаться',
  ];

  // Определение доступных действий и переходов
  // Ключ — текущий статус, значение — массив действий, каждое действие указывает:
  // - действие (ключ)
  // - на какой статус перейдёт задача (значение)
  private static $transitions = [
    self::STATUS_NEW => [
      self::ACTION_CANCEL => self::STATUS_CANCELED,
      self::ACTION_RESPOND => self::STATUS_IN_WORK,
    ],
    self::STATUS_IN_WORK => [
      self::ACTION_COMPLETE => self::STATUS_DONE,
      self::ACTION_DECLINE => self::STATUS_FAILED,
    ],
    // В этих статусах нет доступных действий
    self::STATUS_CANCELED => [],
    self::STATUS_DONE     => [],
    self::STATUS_FAILED   => [],
  ];

  protected int $userId;
  protected int $customerId;
  protected ?int $executorId;

  public function __construct(int $userId, int $customerId, ?int $executorId)
  {
    $this->userId = $userId;
    $this->customerId = $customerId;
    $this->executorId = $executorId;
  }

  /**
   * Возвращает статус, в который перейдёт задание после выполнения действия
   *
   * @param int $action ID действия
   * @param int $currentStatus ID статуса
   * @return int|null ID нового статуса или null, если действие недоступно
   */
  public function getNextStatus(int $action, int $currentStatus): ?int {
    $availableActions = self::$transitions[$currentStatus] ?? [];

    if (isset($availableActions[$action])) {
      return $availableActions[$action];
    }

    return null;
  }

  /**
   * Возвращает список доступных действий для указанного статуса. Если задан статус, для
   * которого нет действий - вернёт пустой массив.
   *
   * @param int $status ID статуса, для которого нужно вернуть действия
   * @return array Массив действий в формате [action_id => 'display_name']
   */
  public function getAvailableActions(int $status): array {
    $availableActions = self::$transitions[$status] ?? [];

    $result = [];
    foreach ($availableActions as $action => $data) {
      if (isset(self::$actionMap[$action]) &&
          self::actionAllowed($action, $this->userId, $this->customerId, $this->executorId)) {
        $result[$action] = self::$actionMap[$action];
      }
    }

    return $result;
  }

  /**
   * Определяет, является ли действие над задачей дпустимым для пользователя userId,
   * если у задачи заказчик - customerId, а исполнитель - executorId
   *
   * @param int $action - id действия
   * @param int $userId - id текущего пользователя
   * @param int $customerId - id заказчика задачи
   * @param int $executorId - id исполнителя задачи
   *
   * @return bool true - действие допустимо, false - нет
   */
  private static function actionAllowed (int $action, int $userId, int $customerId, ?int $executorId): bool {
    if ($action === self::ACTION_CANCEL) {
      return $customerId === $userId;
    }
    if ($action === self::ACTION_RESPOND) {
      return $customerId !== $userId;
    }
    if ($action === self::ACTION_COMPLETE) {
      return $customerId === $userId;
    }
    if ($action === self::ACTION_DECLINE) {
      return $executorId !== null && $executorId === $userId;
    }
    return false;
  }
}
