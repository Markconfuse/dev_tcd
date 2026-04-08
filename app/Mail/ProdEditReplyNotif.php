<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use App\Http\Requests;


class ProdEditReplyNotif extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $_subject;
    public $_content;
	public $_eLink;
    public $_to;
    public $_cc;

    /**
     * Create a new message instance.
     * 
     * @return void
     */
    public function __construct($_subject, $_content, $_eLink, $_to = null, $_cc = null)
    {
		
        $this->_subject = $_subject;
        $this->_content = $_content;
		$this->_eLink = $_eLink;
        $this->_to = $_to;
        $this->_cc = $_cc;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
        return $this->subject($this->_subject)->view('mail.edit_reply_email', ['content' => $this->_content, 'info' => $this->_eLink]);
	
    }

}
