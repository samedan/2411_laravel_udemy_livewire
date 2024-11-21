<?php

namespace App\Livewire;

use Livewire\Component;

class Chat extends Component
{

    public $textvalue="";
    public $chatLog = array();

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
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
