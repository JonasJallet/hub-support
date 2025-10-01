<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'form')]
#[HasLifecycleCallbacks]
class SupportCase
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $subject;

    #[ORM\Column(type: Types::INTEGER)]
    private int $message;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private mixed $file = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'forms')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

     #[PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTimeImmutable();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): int
    {
        return $this->subject;
    }

    public function setSubject(int $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getMessage(): int
    {
        return $this->message;
    }

    public function setMessage(int $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getFile(): mixed
    {
        return $this->file;
    }

    public function setFile(mixed $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        $this->user?->addForm($this);

        return $this;
    }
}
