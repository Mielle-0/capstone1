<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationCode;
    public $firstName;

    /**
     * Create a new message instance.
     */
    public function __construct($verificationCode, $firstName)
    {
        $this->verificationCode = $verificationCode;
        $this->firstName = $firstName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Verification Code',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: "
                <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                    <h2>Hello {$this->firstName},</h2>
                    <p>Thank you for submitting your feedback. Your verification code is:</p>
                    <div style='margin: 20px 0;'>
                        <span style='font-size: 28px; font-weight: bold; letter-spacing: 5px; color: #0d6efd; background: #f8f9fa; padding: 10px 20px; border: 1px dashed #ccc; border-radius: 5px;'>
                            {$this->verificationCode}
                        </span>
                    </div>
                    <p>Please enter this code back on the form to proceed.</p>
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
