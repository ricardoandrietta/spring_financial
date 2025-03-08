<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class User
{
    public function __construct(
        protected string $name,
        protected int $age,
        protected int $score,
        protected string $address,
        protected ?string $qrCodePath = null,
        protected ?int $id = null
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getQrCodePath(): ?string
    {
        return $this->qrCodePath;
    }

    public function setQrCodePath(?string $qrCodePath): void
    {
        $this->qrCodePath = $qrCodePath;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'age' => $this->age,
            'score' => $this->score,
            'address' => $this->address,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            age: $data['age'],
            score: $data['score'] ?? 0,
            address: $data['address'],
            id: $data['id'] ?? null
        );
    }
}
