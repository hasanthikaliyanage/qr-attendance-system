<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $userType;

    public function __construct(User $user, $password, $userType = null)
    {
        $this->user = $user;
        $this->password = $password;
        $this->userType = $userType;
    }

    public function envelope(): Envelope
    {
        $subject = $this->userType 
            ? "Your {$this->userType} Account Credentials - University QR Attendance System"
            : 'Your University QR Attendance System Account Credentials';
            
        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        // User type එක ගන්න (නැත්නම් role එකෙන්)
        $userType = $this->userType;
        if (!$userType && $this->user->role) {
            $userType = $this->user->role->name;
        }
        
        return new Content(
            view: 'emails.credentials',
            with: [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $this->password,
                'loginUrl' => route('login'),
                'userType' => $userType,
                'role' => $this->user->role->name ?? 'User',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}