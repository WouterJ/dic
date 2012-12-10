<?php

class Registration implements MailerAwareInterface
{
    private $mailer;

    public function setMailer(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function getMailer()
    {
        return $this->mailer;
    }
}
