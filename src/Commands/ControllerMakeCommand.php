<?php

namespace PCore\Console\Commands;

use PCore\Utils\Exceptions\FileNotFoundException;
use PCore\Utils\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ControllerMakeCommand
 * @package PCore\Console\Commands
 * @github https://github.com/pcore-framework/console
 */
class ControllerMakeCommand extends Command
{

    protected string $stubsPath = __DIR__ . '/stubs/';

    protected function configure()
    {
        $this->setName('make:controller')
            ->setDescription('Making controllers.')
            ->setDefinition([
                new InputArgument('controller', InputArgument::REQUIRED, 'Имя контроллера, например "user".'),
                new InputOption('rest', 'r', InputOption::VALUE_OPTIONAL, 'Создать спокойный контроллер.')
            ]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws FileNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $controller = $input->getArgument('controller');
        $stubFile = $this->stubsPath . ($input->hasOption('rest') ? 'controller_rest.stub' : 'controller.stub');
        [$namespace, $controller] = $this->parse($controller);
        $controllerPath = base_path('src/Controllers/' . str_replace('\\', '/', $namespace) . '/');
        $controllerFile = $controllerPath . $controller . 'Controller.php';
        if (Filesystem::exists($controllerFile)) {
            $output->writeln('<comment>[WARN]</comment> контроллер уже существует!');
            return 1;
        }
        Filesystem::exists($controllerPath) || Filesystem::makeDirectory($controllerPath, 0777, true);
        Filesystem::put($controllerFile, str_replace(
            ['{{namespace}}', '{{class}}', '{{path}}'],
            ['App\\Controllers' . $namespace, $controller . 'Controller', strtolower($controller)],
            Filesystem::get($stubFile)
        ));
        $output->writeln("<info>[INFO]</info>Контроллер App\\Controllers{$namespace}\\{$controller}Controller был создан успешно!");
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