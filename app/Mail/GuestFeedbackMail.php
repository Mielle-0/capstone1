<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuestFeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    public $temporaryUrl;

    public function __construct($temporaryUrl)
    {
        $this->temporaryUrl = $temporaryUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'View Your Feedback History',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.guest-feedback',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
