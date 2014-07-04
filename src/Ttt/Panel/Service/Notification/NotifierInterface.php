<?php
namespace Ttt\Panel\Service\Notification;

interface NotifierInterface
{
    /**
    * Establece a quién notificamos
    *
    * @param array $to adress&&name
    * @return Ttt\Service\Notification\NotifierInterface
    */
    public function to($to);

    /**
    * Establece a quién ponemos en copia oculta
    *
    * @param array $bcc adress&&name
    * @return Ttt\Service\Notification\NotifierInterface
    */
    public function bcc($bcc);

    /**
    * Establece a quién ponemos en copia
    *
    * @param array $cc adress&&name
    * @return Ttt\Service\Notification\NotifierInterface
    */
    public function cc($cc);

    /**
    * Establece quién notifica
    *
    * @param array $from adress&&name
    * @return Ttt\Service\Notification\NotifierInterface
    */
    public function from($from);

    /**
    * Establece tpl para enviar el correo
    *
    * @param string $template
    * @return Ttt\Service\Notification\NotifierInterface
    */
    public function notifierTemplate($template);

    /**
    * Envía la notificación
    *
    * @param string $subject
    * @param array $data
    * @param string|FALSE $attachment
    * @return void
    */
    public function notify($subject, $data, $attachment = FALSE);
}
