<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class Winner
{
    public function __construct(protected int $userId, protected int $score, protected ?\DateTime $createdAt = null, protected ?int $id = null)
    {
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt ?? new \DateTime();
    }

}
