<?php

declare(strict_types=1);

namespace Modules\Notifications\Infrastructure\Drivers;

use Illuminate\Support\Facades\Log;

class DummyDriver implements DriverInterface
{
    public function send(
        string $toEmail,
        string $subject,
        string $message,
        string $reference,
    ): bool {
        Log::channel('sending_email_log')
            ->info(
                sprintf(
                    'Message was sended: to: %s , subject: %s , message: %s, resource_id : %s',
                    $toEmail,
                    $subject,
                    $message,
                    $reference)
            );

        return true;
    }
}
