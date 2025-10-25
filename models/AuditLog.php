<?php

class AuditLog
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $message;

    public function __construct($message = null) {
        $this->message = $message;
    }
}
