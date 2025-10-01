<?php

namespace App\Application\Query\SupportCase;

use App\Domain\Entity\SupportCase;

class SupportCaseResponse
{
    public int $id;
    public string $subject;
    public string $message;
    public ?string $file;
    public ?string $downloadUrl;

    public function fromEntity(SupportCase $form): self
    {
        $this->id = $form->getId();
        $this->subject = $form->getSubject();
        $this->message = $form->getMessage();

        if ($form->getFile() !== null) {
        $fileContent = $form->getFile();
        if (is_resource($fileContent)) {
            $fileContent = stream_get_contents($fileContent);
        }

        $this->file = base64_encode($fileContent);
        $this->downloadUrl = "data:application/octet-stream;base64," . $this->file;
        }

        return $this;
    }
}
