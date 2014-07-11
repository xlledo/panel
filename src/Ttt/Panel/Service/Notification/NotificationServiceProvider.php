<?php
namespace Ttt\Panel\Service\Notification;

use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider{

    /**
    * Register the bindingt
    * @return void
    */
    public function register()
    {
        $app = $this->app;

        //bind nuestro notificador
        $app['ttt.notifier'] = $app->share(function($app)
        {
            $notifier = new EmailNotifier($app['mailer']);

            $config = $app['config'];

            /*$notifier->from($config->get('mail.from'))
                        ->to($config->get('mail.notifyTo'));*/
            $notifier->from($config->get('panel::mail.from'))
                        ->to($config->get('panel::mail.notifyTo'));

            return $notifier;
        });
    }
}
