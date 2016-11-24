<?php

namespace AppBundle\Service;

class MailManager implements MailManagerInterface
{
    protected $mailer;
    protected $twig;
    protected $fromMail;
    protected $mailName;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, $fromMail, $mailName)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->fromMail = $fromMail;
        $this->mailName = $mailName;
    }

    /**
     * @param string $subject
     * @param string $to
     * @param        $template
     * @param array  $data
     * @param null   $attach
     * @param null   $fromName
     * @param null   $fromMail
     *
     * @return bool
     */
    public function sendEmail($subject, $to, $template, array $data = [], $attach = null, $fromName = null, $fromMail = null)
    {
        /** @var \Swift_Message $message */
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromMail ?: $this->fromMail, $fromName ?: $this->mailName)
            ->setTo($to)
            ->setBody($this->renderView($template, $data), 'text/html');

        if ($attach) {
            $message->attach(\Swift_Attachment::fromPath($attach));
        }

        return $this->mailer->send($message);
    }

    protected function renderView($view, array $parameters = [])
    {
        return $this->twig->render($view, $parameters);
    }
}
