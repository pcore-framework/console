<?php

namespace PCore\Console\Commands;

use InvalidArgumentException;
use PCore\Utils\Exceptions\FileNotFoundException;
use PCore\Utils\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MiddlewareMakeCommand
 * @package PCore\Console\Commands
 * @github https://github.com/pcore-framework/console
 */
class MiddlewareMakeCommand extends Command
{

    protected string $stubsPath = __DIR__ . '/stubs/';

    protected function configure()
    {
        $this->setName('make:middleware')
            ->setDescription('Making middleware.')
            ->setDefinition([
                new InputArgument('middleware', InputArgument::REQUIRED, 'Имя промежуточного программного обеспечения, например `user`.'),
                new InputOption('suffix', 's', InputOption::VALUE_OPTIONAL, 'Файл имеет суффикс, если эта опция доступна.')
            ]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws FileNotFoundException|\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stubFile = $this->stubsPath . 'middleware.stub';
        [$namespace, $middleware] = $this->parse($input->getArgument('middleware'));
        $middlewarePath = base_path('src/Middlewares/' . str_replace('\\', '/', $namespace) . '/');
        Filesystem::exists($middlewarePath) || Filesystem::makeDirectory($middlewarePath, 0755, true);
        $suffix = $input->getOption('suffix') ? 'Middleware' : '';
        $middlewareFile = $middlewarePath . $middleware . $suffix . '.php';
        Filesystem::exists($middlewareFile) && throw new InvalidArgumentException('Промежуточное программное обеспечение уже существует!');
        Filesystem::put($middlewareFile, str_replace(
            ['{{namespace}}', '{{class}}'],
            ['App\\Middlewares' . $namespace, $middleware . $suffix],
            file_get_contents($stubFile)
        ));
        $output->writeln("<info>[DEBU]</info>Промежуточное программное обеспечение App\\Middlewares{$namespace}\\{$middleware} было создано успешно!");
        return 1;
    }

    /**
     * @param $input
     * @return array
     */
    protected function parse($input): array
    {
        $array = explode('/', $input);
        $class = ucfirst(array_pop($array));
        $namespace = implode('\\', array_map(fn($value) => ucfirst($value), $array));
        if (!empty($namespace)) {
            $namespace = '\\' . $namespace;
        }
        return [$namespace, $class];
    }

}