<?php
namespace Ttt\Panel\Service\Notification;

class EmailNotifier implements NotifierInterface
{
    protected $to = array();

    protected $cc = array();

    protected $bcc = array();

    protected $notifyTemplate = 'panel::emails.notifier.notify';

    protected $from;

    protected $mailer;

    public function __construct(\Illuminate\Mail\Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
    * Establece a quién notificamos
    *
    * @param array $to adress&&name
    * @return Ttt\Service\Notification\NotifierInterface
    */
    public function to($to)
    {
        $this->to[] = $to;

        return $this;
    }

    /**
    * Establece a quién ponemos en copia oculta
    *
    * @param array $bcc adress&&name
    * @return Ttt\Service\Notification\NotifierInterface
    */
    public function bcc($bcc)
    {
        $this->bcc[] = $bcc;

        return $this;
    }

    /**
    * Establece a quién ponemos en copia
    *
    * @param array $cc adress&&name
    * @return Ttt\Service\Notification\NotifierInterface
    */
    public function cc($cc)
    {
        $this->cc[] = $cc;

        return $this;
    }

    /**
    * Establece quién notifica
    *
    * @param array $from adress&&name
    * @return Ttt\Service\Notification\NotifierInterface
    */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
    * Establece tpl para enviar el correo
    *
    * @param string $template
    * @return Ttt\Service\Notification\NotifierInterface
    */
    public function notifierTemplate($template)
    {
        $this->notifyTemplate = $template;

        return $this;
    }

    /**
    * Envía la notificación
    *
    * @param string $subject
    * @param array $data
    * @param string|FALSE $attachment
    * @return void
    */
    public function notify($subject, $data, $attachment = FALSE)
    {

        $to    = $this->to;
        $cc    = $this->cc;
        $bcc   = $this->bcc;

        $from = $this->from;
//        $this->mailer->pretend(TRUE);
		$this->mailer->send($this->notifyTemplate, $data, function($message) use ($subject, $data, $to, $from, $bcc, $cc, $attachment)
		{
			$message->subject($subject);

            $message->from($from['address'], $from['name']);

            foreach($to as $aTo)
            {
                $message->to($aTo['address'], $aTo['name']);
            }

            foreach($cc as $aCc)
            {
                $message->cc($aCc['address'], $aCc['name']);
            }

            foreach($bcc as $aBcc)
            {
                $message->bcc($aBcc['address'], $aBcc['name']);
            }

            //@TODO: FALTA VER EL TEMA DEL ATTACHMENT

		});
    }
}
