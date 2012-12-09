<?php

class Mailer
{
    private $transport;

    public function __construct($transport)
    {
        $this->transport = $transport;
    }
}
