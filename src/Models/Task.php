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
      Action::MarkDone->value => Status::Done->value,
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
   * Возвращает список доступных действий для указанного статуса. Если задан статус, для
   * которого нет действий - вернёт пустой массив.
   *
   * @param Status $status Статус, для которого нужно вернуть действия
   * @return array<string, string> Массив действий в формате ['action' => 'label']
   */
  public function getAvailableActions(Status $status): array
  {
    $availableActions = self::$transitions[$status->value] ?? [];
    $result = [];

    foreach ($availableActions as $actionValue => $statusValue) {
      $actionObj = Action::tryFrom($actionValue);
      if ($actionObj) {
        $result[$actionValue] = $actionObj->label();
      }
    }

    return $result;
  }
}
