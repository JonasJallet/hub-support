<?php

namespace App\Application\Command\SupportCase\AddSupportCase;

use App\Application\Bus\Command\Command;
use App\Domain\Entity\SupportCase;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

class AddSupportCase implements Command
{
    #[Ignore]
    public ?int $id = null;

    #[Assert\NotBlank(
        message: "Exceptions.Assert.NotBlank",
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: "Exceptions.Assert.Length.Subject",
    )]
    #[Assert\Type('string')]
    public string $subject;

    #[Assert\NotBlank(
        message: "Exceptions.Assert.NotBlank",
    )]
    #[Assert\Type('string')]
    public string $message;

    #[Assert\File(
        maxSize: "2M",
        mimeTypes: [
            "application/pdf",
            "image/*",
            "text/plain",
        ],
        mimeTypesMessage: "Please upload a valid file."
    )]
    #[OA\Property(type: 'string', format: 'binary')]
    public ?UploadedFile $file = null;

    public function toEntity(): SupportCase
    {
        $form = new SupportCase();
        $form->setSubject($this->subject);
        $form->setMessage($this->message);
        if ($this->file !== null) {
            $form->setFile($this->file);
        }

        return $form;
    }
}
