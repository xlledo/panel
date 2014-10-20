<?php
namespace Ttt\Panel\Service\Validation;

use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider{
    public function register(){}

    public function boot()
    {
        $this->app->validator->resolver(function($translator, $data, $rules, $messages)
            {
                return new \Ttt\Panel\Service\Form\Usuario\UserValidator($translator, $data, $rules, $messages);
            });
    }
}
