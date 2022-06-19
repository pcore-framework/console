<?php

declare(strict_types=1);

namespace PCore\Console;

/**
 * Class ConfigProvider
 * @package PCore\Console
 * @github https://github.com/pcore-framework/console
 */
class ConfigProvider
{

    public function __invoke(): array
    {
        return [
            'commands' => [
                'PCore\\Console\Commands\ControllerMakeCommand'
            ]
        ];
    }

}