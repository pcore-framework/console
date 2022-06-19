<?php

declare(strict_types=1);

namespace PCore\Console;

use PCore\Aop\Collectors\AbstractCollector;
use PCore\Console\Annotations\Command;

/**
 * Class CommandCollector
 * @package PCore\Console
 * @github https://github.com/pcore-framework/console
 */
class CommandCollector extends AbstractCollector
{

    protected static array $container = [];

    public static function collectClass(string $class, object $attribute): void
    {
        if ($attribute instanceof Command) {
            self::add($class);
        }
    }

    public static function add(string $class): void
    {
        if (!in_array($class, self::$container)) {
            self::$container[] = $class;
        }
    }

    public static function all(): array
    {
        return self::$container;
    }

}