<?php

namespace AppBundle\Service;

interface MailManagerInterface
{
    public function sendEmail($subject,
                              $to,
                              $template, array
                              $data = [],
                              $attach = null,
                              $fromName = null,
                              $fromMail = null
    );
}