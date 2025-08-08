<?php

class Task
{
  // Константы статусов
  const STATUS_NEW      = 'new';
  const STATUS_CANCELED = 'canceled';
  const STATUS_IN_WORK  = 'in_work';
  const STATUS_DONE     = 'done';
  const STATUS_FAILED   = 'failed';

  // Константы действий
  const ACTION_CANCEL     = 'cancel';
  const ACTION_RESPOND    = 'respond';
  const ACTION_MARK_DONE  = 'mark_done';
  const ACTION_DECLINE    = 'decline';

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
    self::ACTION_MARK_DONE  => 'Выполнено',
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
      self::ACTION_MARK_DONE => self::STATUS_DONE,
      self::ACTION_DECLINE => self::STATUS_FAILED,
    ],
    // В этих статусах нет доступных действий
    self::STATUS_CANCELED => [],
    self::STATUS_DONE     => [],
    self::STATUS_FAILED   => [],
  ];

  // Свойства экземпляра класса
  private $customerId;
  private $executorId;
  private $currentStatus;

  /**
   * Конструктор
   *
   * @param int $customerId ID заказчика
   */
  public function __construct(int $customerId) {
    $this->customerId = $customerId;
    $this->executorId = null;
    $this->currentStatus = self::STATUS_NEW;
  }

  /**
   * Возвращает карту статусов (внутреннее имя => отображаемое имя)
   *
   * @return array
   */
  public static function getStatusMap(): array {
    return self::$statusMap;
  }

  /**
   * Возвращает карту действий (внутреннее имя => отображаемое имя)
   *
   * @return array
   */
  public static function getActionMap(): array {
    return self::$actionMap;
  }

  /**
   * Возвращает текущий статус задания
   *
   * @return string
   */
  public function getCurrentStatus(): string {
    return $this->currentStatus;
  }

  /**
   * Устанавливает текущий статус задания
   *
   * @param string $status
   * @return void
   */
  public function setCurrentStatus(string $status): void {
    $this->currentStatus = $status;
  }

  /**
   * Возвращает ID заказчика
   *
   * @return int
   */
  public function getCustomerId(): int {
    return $this->customerId;
  }

  /**
   * Возвращает ID исполнителя
   *
   * @return int|null
   */
  public function getExecutorId(): ?int {
    return $this->executorId;
  }

  /**
   * Устанавливает ID исполнителя
   *
   * @param int $executorId
   * @return void
   */
  public function setExecutorId(int $executorId): void {
    $this->executorId = $executorId;
  }

  /**
   * Возвращает статус, в который перейдёт задание после выполнения действия
   *
   * @param string $action Внутреннее имя действия
   * @return string|null Внутреннее имя нового статуса или null, если действие недоступно
   */
  public function getNextStatus(string $action): ?string {
    $availableActions = self::$transitions[$this->currentStatus] ?? [];

    if (isset($availableActions[$action])) {
      return $availableActions[$action];
    }

    return null;
  }

  /**
   * Возвращает список доступных действий для указанного статуса. Если задан статус, для
   * которого нет действий - вернёт пустой массив.
   *
   * @param string $status Статус, для которого нужно вернуть действия
   * @return array Массив действий в формате ['action' => 'display_name']
   */
  public function getAvailableActions(string $status): array {
    $availableActions = self::$transitions[$status] ?? [];

    $result = [];
    foreach ($availableActions as $action => $data) {
      if (isset(self::$actionMap[$action])) {
        $result[$action] = self::$actionMap[$action];
      }
    }

    return $result;
  }
}
