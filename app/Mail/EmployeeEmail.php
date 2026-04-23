<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $body;
    public $sender;
    public $employee;

    public function __construct($subject, $body, $sender, $employee)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->sender = $sender;
        $this->employee = $employee;
    }

    public function build()
    {
        return $this->to($this->employee->email, $this->employee->name)
                    ->subject($this->subject)
                    ->view('emails.employee')
                    ->with([
                        'body' => $this->body,
                        'sender' => $this->sender,
                        'employee' => $this->employee,
                    ]);
    }
}
