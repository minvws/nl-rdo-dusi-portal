<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use MinVWS\DUSi\Shared\Application\DTO\DispositionMailData;

class DispositionMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly DispositionMailData $data)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Er staat een nieuw bericht voor u klaar',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'dusi::emails.disposition-html',
            text: 'dusi::emails.disposition-text',
            with: [
                'name' => $this->data->toName,
            ]
        );
    }
}
