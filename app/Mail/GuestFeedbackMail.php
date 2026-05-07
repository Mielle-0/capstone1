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
            htmlString: "
                <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                    <h2>Hello,</h2>
                    <p>You requested a link to view your submitted feedback history.</p>
                    <p>Click the button below to securely access your portal. <strong>This link will expire in 24 hours.</strong></p>
                    <div style='margin: 30px 0;'>
                        <a href='{$this->temporaryUrl}' style='background-color: #0d6efd; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                            View My Feedback
                        </a>
                    </div>
                    <p>If you did not request this, you can safely ignore this email.</p>
                </div>
            "
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
