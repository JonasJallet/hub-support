<?php

namespace App\Domain\Exception;

use LogicException;

class DomainException extends LogicException
{
    public int $internalCode;
    public string $translationDomain;
    public array $translationParameters;

    public function __construct(
        string $message,
        int    $code = 0,
        array  $translationParameters = [],
        string $translationDomain = 'exceptions',
        int    $internalCode = 999,

    )
    {
        $this->internalCode = $internalCode;
        $this->translationDomain = $translationDomain;
        $this->setTranslationParameters($translationParameters);
        parent::__construct($message, $code);
    }

    public function getInternalCode(): int
    {
        return $this->internalCode;
    }

    public function setTranslationDomain(string $translationDomain): self
    {
        $this->translationDomain = $translationDomain;

        return $this;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function addTranslationParameter(int|string $key, mixed $value): self
    {
        $this->translationParameters[$key] = $value;

        return $this;
    }

    public function setTranslationParameters(array $parameters): self
    {
        $this->translationParameters = $parameters;

        return $this;
    }

    public function removeTranslationParameter(int|string $key): self
    {
        unset($this->translationParameters[$key]);

        return $this;
    }

    public function getTranslationParameters(): array
    {
        return $this->translationParameters;
    }
}
