<?php

declare(strict_types=1);

namespace CommissionCalculator\Logger;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    public function log($level, $message, array $context = []): void
    {
        $filePath = __DIR__ . '/../../var/debug.log';
        file_put_contents(
            $filePath,
            sprintf(
                '[%s] %s %s',
                $level,
                $message,
                json_encode($context)
            ) . PHP_EOL,
            FILE_APPEND,
        );
    }

    public function hasErrored(): bool
    {
        return true;
    }
}