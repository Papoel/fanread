<?php

namespace App\Exception;

class IsbnApiException extends \Exception
{
    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromHttpError(int $statusCode, string $response): self
    {
        return new self(
            sprintf('Erreur API ISBN (HTTP %d): %s', $statusCode, $response),
            $statusCode
        );
    }

    public static function fromException(\Throwable $e): self
    {
        return new self(
            sprintf('Erreur lors de la connexion à l\'API ISBN: %s', $e->getMessage()),
            0,
            $e
        );
    }
}
