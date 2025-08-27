<?php

namespace Romnosk\Models;
use Romnosk\Models\Status;
use Romnosk\Models\Action;

class Task
{
  protected int $customerId;
  protected ?int $executorId;
  protected Status $currentStatus;

  // Определение доступных действий и переходов
  // Ключ — текущий статус, значение — массив действий, каждое действие указывает:
  // - действие (ключ)
  // - на какой статус перейдёт задача (значение)
  protected static array $transitions = [
    Status::New->value => [
      Action::Cancel->value  => Status::Canceled->value,
      Action::Respond->value => Status::InWork->value,
    ],
    Status::InWork->value => [
      Action::Complete->value => Status::Done->value,
      Action::Decline->value  => Status::Failed->value,
    ],
    Status::Canceled->value => [],
    Status::Done->value     => [],
    Status::Failed->value   => [],
  ];

  public function __construct(int $customerId)
  {
    $this->customerId = $customerId;
    $this->executorId = null;
    $this->currentStatus = Status::New;
  }

  public function getCustomerId(): int
  {
    return $this->customerId;
  }

  public function getExecutorId(): ?int
  {
    return $this->executorId;
  }

  public function setExecutorId(int $executorId): void
  {
    $this->executorId = $executorId;
  }

  public function getCurrentStatus(): Status
  {
    return $this->currentStatus;
  }

  public function setCurrentStatus(Status $status): void
  {
    $this->currentStatus = $status;
  }

  /**
   * Возвращает статус, в который перейдёт задание из текущего статуса после выполнения
   * действия
   *
   * @param string $action Внутреннее имя действия
   * @return Status|null   Объект нового статуса или null, если действие недоступно
   */
  public function getNextStatus(string $action): ?Status
  {
    $availableActions = self::$transitions[$this->currentStatus->value] ?? [];
    $nextStatusValue = $availableActions[$action] ?? null;
    return $nextStatusValue ? Status::tryFrom($nextStatusValue) : null;
  }

  /**
   * Возвращает список доступных действий для указанного статуса и пользователя.
   * Если задан статус, для которого нет действий, либо их не может выполнять
   * пользователь с указанным Id - вернёт пустой массив.
   *
   * @param Status $status Статус, для которого нужно вернуть действия
   * @param int $userId Пользователь, который должен сделать действия
   * @return array<string, ActionAbstract> Массив действий в формате ['action' => actionObject]
   */
  public function getAvailableActions(Status $status, int $userId): array
  {
    $availableActions = self::$transitions[$status->value] ?? [];
    $result = [];

    foreach ($availableActions as $actionValue => $statusValue) {
      $actionEnum = Action::tryFrom($actionValue);
      if ($actionEnum && $actionEnum->actionObject()->actionAllowed($userId, $this->customerId, $this->executorId)) {
        $result[$actionValue] = $actionEnum->actionObject();
      }
    }

    return $result;
  }
}
