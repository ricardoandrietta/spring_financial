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

    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): User
    {
        $this->age = $age;
        return $this;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): User
    {
        $this->score = $score;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): User
    {
        $this->address = $address;
        return $this;
    }

    public function getQrCodePath(): ?string
    {
        return $this->qrCodePath;
    }

    public function setQrCodePath(?string $qrCodePath): User
    {
        $this->qrCodePath = $qrCodePath;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): User
    {
        $this->id = $id;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'age' => $this->age,
            'score' => $this->score,
            'address' => $this->address,
            'qrCodePath' => $this->qrCodePath,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            age: $data['age'],
            score: $data['score'] ?? 0,
            address: $data['address'],
            qrCodePath: $data['qrCodePath'] ?? null,
            id: $data['id'] ?? null
        );
    }

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'age' => 'required|integer|min:1|max:100',
            'address' => 'required|string|max:350',
        ];
    }
}
