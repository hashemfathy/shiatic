<?php

namespace App\Mail;

use App\Models\Employee;
use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SpecialistAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Employee $employee;
    public Visit $visit;
    public array $sessions;

    /**
     * Create a new message instance.
     */
    public function __construct(Employee $employee, Visit $visit, array $sessions = [])
    {
        $this->employee = $employee;
        $this->visit = $visit;
        $this->sessions = $sessions;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $clientName = $this->visit->client?->name ?? 'عميل جديد';
        $visitDate = $this->visit->date;
        return new Envelope(
            subject: "موعد جلسة جديد - {$clientName} ({$visitDate})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.specialist_assigned',
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
