<?php

namespace App\Channels;

class Kernel
{
    /**
     * Get the available modules for the channels.
     */
    public static function getModules(): array
    {
        return [
            'Email' => '\App\Channels\Email\EmailChannel',
        ];
    }
}
