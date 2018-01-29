<?php

namespace App\Services\Messenger;

use App\Services\Session\Session;

class Messenger
{
    private $messages = array();
    private $errors = array();

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * Saves the messages set in the session
     */
    public function __destruct()
    {
        if(!empty($this->messages)) {
            Session::set('messages', $this->messages, 'messages');
        }
        if(!empty($this->errors)) {
            Session::set('errors', $this->errors, 'messages');
        }
    }

    /**
     * Create normal message
     * 
     * @param string|int    $message    The message you want to show
     */
    public function createMessage($message)
    {
        $this->messages[] = $message;
    }

    /**
     * Create error message
     * 
     * @param string|int    $error      The message you want to show
     */
    public function createError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Get messages to display
     * 
     * @return array    Array with messages
     */
    public function getMessagesToDisplay()
    {
        $messages = array(
            'messages' => Session::get('messages', 'messages'),
            'errors' => Session::get('errors', 'messages')
        );

        Session::set('messages', array(), 'messages');
        Session::set('errors', array(), 'messages');

        return $messages;
    }    
}