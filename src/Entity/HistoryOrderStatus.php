<?php

namespace App\Entity;

use App\Repository\HistoryOrderStatusRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * История статусов заказа
 */
#[ORM\Entity(repositoryClass: HistoryOrderStatusRepository::class)]
#[ORM\HasLifecycleCallbacks]
class HistoryOrderStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue('IDENTITY')]
    #[
        ORM\Column(
            type: Types::INTEGER,
            nullable: false,
            options: ['comment' => 'Идентификатор в истории статуса заказа']
        )
    ]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?OrderStatus $status = null;

    #[
        ORM\Column(
            type: Types::DATETIME_MUTABLE,
            nullable: false,
            options: ['comment' => 'Дата/время изменения статуса заказа']
        )
    ]
    private ?DateTimeInterface $dtCreate = null;

    #[
        ORM\ManyToOne(
            inversedBy: 'historyOrderStatus',
            cascade: ['persist']
        )
    ]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    /**
     * Получить идентификатор в истории статуса заказа
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Получить статус заказа
     *
     * @return OrderStatus|null
     */
    public function getStatus(): ?OrderStatus
    {
        return $this->status;
    }

    /**
     * Записать статус заказа
     *
     * @param OrderStatus|null $status
     * @return self
     */
    public function setStatus(?OrderStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Получить заказ
     *
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * Записать заказ
     *
     * @param Order|null $order
     * @return self
     */
    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Получить дату/время изменения статуса заказа
     *
     * @return DateTimeInterface|null
     */
    public function getDtCreate(): ?DateTimeInterface
    {
        return $this->dtCreate;
    }

    /**
     * Записать дату/время изменения статуса заказа
     *
     * @return self
     */
    #[ORM\PrePersist]
    public function setDtCreate(): self
    {
        $this->dtCreate = new DateTime();

        return $this;
    }
}
