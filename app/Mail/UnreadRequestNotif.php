<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use App\Http\Requests;


class UnreadRequestNotif extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $_subject;
    public $_content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($_subject, $_content)
    {
        $this->_subject = $_subject;
        $this->_content = $_content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
        return $this->subject($this->_subject)->view('mail.unread_request', [
            'content' => $this->_content
        ]);
    }
}
