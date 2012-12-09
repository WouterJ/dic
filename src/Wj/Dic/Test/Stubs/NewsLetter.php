<?php

class NewsLetter
{
    private $mailer;

    public function __construct(\Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function getMailer()
    {
        return $this->mailer;
    }
}
