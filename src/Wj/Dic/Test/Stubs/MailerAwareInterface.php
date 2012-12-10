<?php

interface MailerAwareInterface
{
    /**
     * @param \Mailer $mailer
     */
    public function setMailer(Mailer $mailer);
}
