<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use MinVWS\DUSi\Shared\Application\DTO\DispositionMailData;
use MinVWS\DUSi\Shared\Application\Mail\DispositionMail;

class SendDispositionNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly DispositionMailData $dispositionData,
    ) {
    }

    public function handle(): void
    {
        Mail::to([new Address($this->dispositionData->toAddress, $this->dispositionData->toName)])
            ->send(new DispositionMail($this->dispositionData))
        ;
    }
}
