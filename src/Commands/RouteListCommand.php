<?php

declare (strict_types=1);

namespace PCore\Console\Commands;

use App\Kernel;
use Closure;
use PCore\Di\Exceptions\NotFoundException;
use PCore\Routing\{Route, RouteCollector};
use PCore\Utils\Collection;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\{ConsoleOutput, OutputInterface};

/**
 * Class RouteListCommand
 * @package PCore\Console\Commands
 * @github https://github.com/pcore-framework/console
 */
class RouteListCommand extends Command
{

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('route:list')
            ->setDescription('Список маршрутов');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table(new ConsoleOutput());
        $table->setHeaders(['Methods', 'URI', 'Action', 'Middlewares', 'Domain']);
        foreach ($this->getRoutes() as $route) {
            /** @var Route $route */
            $action = $route->getAction();
            if (is_array($action)) {
                $action = implode('@', $action);
            } else if ($action instanceof Closure) {
                $action = 'Closure';
            }
            $table->addRow([
                implode('|', $route->getMethods()),
                $route->getPath(),
                $action,
                implode(PHP_EOL, $route->getMiddlewares()),
                $route->getDomain() ?: '*'
            ]);
        }
        $table->render();
        return 0;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundException
     * @throws ReflectionException
     */
    protected function getRoutes(): Collection
    {
        make(Kernel::class);
        $routeCollector = make(RouteCollector::class);
        $routes = [];
        foreach ($routeCollector->all() as $registeredRoute) {
            foreach ($registeredRoute as $route) {
                foreach ($route as $item) {
                    if (!in_array($item, $routes)) {
                        $routes[] = $item;
                    }
                }
            }
        }
        return Collection::make($routes)->unique()->sortBy(function ($item) {
            /** @var Route $item */
            return $item->getPath();
        });
    }

}