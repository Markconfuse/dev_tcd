<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UnansweredRequestNotif extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $_subject;
    public $viewName;
    public $viewData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($_subject, $viewName, $viewData)
    {
        $this->_subject = $_subject;
        $this->viewName = $viewName;
        $this->viewData = $viewData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->_subject)->view($this->viewName, $this->viewData);
    }
}
