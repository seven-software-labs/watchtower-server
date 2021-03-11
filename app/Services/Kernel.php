<?php

namespace App\Services;

class Kernel
{
    /**
     * Get the available modules for the channels.
     */
    public static function getModules(): array
    {
        return [
            // 'Email' => '\App\Services\Mailgun\Mailgun',
            // 'Facebook' => '\App\Services\Facebook\Facebook',
            'Logger' => '\App\Services\Logger\Logger',
            // 'Twitter' => '\App\Services\Twitter\Twitter',
        ];
    }
}
