<?php

namespace App\Livewire;

use App\Events\ChatMessage;
use Livewire\Component;

class Chat extends Component
{

    public $textvalue="";
    // Array of messages, own and others
    public $chatLog = array();

    public function getListeners() {
        return [
            "echo-private:chatchannel,ChatMessage" => 'notifyNewMessage'
        ];
    }

    public function notifyNewMessage($x) {
        array_push($this->chatLog, $x['chat']);
    }

    public function send() {
        // user not logged in
        if(!auth()->check()) {
            abort(402, 'Unauthorized' );
        }
        // empty message
        if(trim(strip_tags($this->textvalue)) == "") {
            return;
        }
        array_push($this->chatLog, [
            'selfmessage' => true, 
            'username' => auth()->user()->username,            
            'textvalue' => strip_tags(($this->textvalue)),            
            'avatar' => auth()->user()->avatar,            
        ]);
        // Broadcast own message
        broadcast(new ChatMessage([
            'selfmessage' => false, 
            'username' => auth()->user()->username,            
            'textvalue' => strip_tags(($this->textvalue)),            
            'avatar' => auth()->user()->avatar,            
        ]))->toOthers();

        // clear the message form
        $this->textvalue ="";
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
