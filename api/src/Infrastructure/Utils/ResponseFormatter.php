<?php

namespace App\Infrastructure\Utils;

use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

readonly class ResponseFormatter
{
    public const int EXCEPTION_CODE_DEFAULT_VALUE = 0;
    public const int MAX_HTTP_REQUEST_STATUS_CODE = 599;
    public const int HTTP_BAD_REQUEST = 400;
    public const int FRONT_CUSTOM_ERROR = 999;

    public function __construct(
        private TranslatorInterface $translator,
    )
    {
    }

    public function formatResponse(
        string       $message = '',
        array        $translationParameters = [],
        ?string      $translationDomain = null,
        array|object $data = [],
    ): array
    {
        if (array_key_exists("%entity%", $translationParameters)) {
            $translationParameters['%entity%'] = $this->translator->trans($translationParameters['%entity%'], [], 'entities', $this->translator->getLocale());
        }

        return [
            'message' => $this->translator->trans($message, $translationParameters, $translationDomain),
            'data' => $data,
        ];
    }

    public function formatException(
        Throwable $exception, array $data = [],
    ): array
    {
        $statusCode = $exception->getCode();
        [$translationParameters, $translationDomain] = $this->prepareTranslationParams($exception);
        $code = ($statusCode === self::EXCEPTION_CODE_DEFAULT_VALUE)
        || ($statusCode > self::MAX_HTTP_REQUEST_STATUS_CODE) ? self::HTTP_BAD_REQUEST : $statusCode;
        $response = $this->formatResponse(
            $exception->getMessage(), $translationParameters, $translationDomain, $data
        );
        $response['is_internal'] = is_callable([$exception, 'getInternalCode'])
            && $exception->getInternalCode() == self::FRONT_CUSTOM_ERROR;

        return [$response, $code];
    }

    public static function prepareTranslationParams(Throwable $exception): array
    {
        $translationParameters = is_callable([$exception, 'getTranslationParameters'])
            ? $exception->getTranslationParameters() : [];

        $translationDomain = is_callable([$exception, 'getTranslationDomain'])
            ? $exception->getTranslationDomain() : null;

        return [$translationParameters, $translationDomain];
    }
}
