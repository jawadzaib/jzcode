<?php
namespace Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateServiceCommand extends Command
{
    protected $commandName = 'make:service';
    protected $commandDescription = "Generate new Service";

    protected $commandArgumentName = "name";
    protected $commandArgumentDescription = "Name of the Service";

    protected $commandInternalOptionName = "internal";
    protected $commandInternalOptionDescription = 'this option will allow to execute command internally';

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentName,
                InputArgument::OPTIONAL,
                $this->commandArgumentDescription
            )
            ->addOption(
                $this->commandInternalOptionName,
                null,
                InputOption::VALUE_NONE,
                $this->commandInternalOptionDescription
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument($this->commandArgumentName);

        $io = new SymfonyStyle($input, $output);

        $is_internal = $input->getOption($this->commandInternalOptionName);

        while (!$name) {
            $name =$io->ask('Service Name:', null, function($response) {
                return $response;
            });
        }

        $module_name = null;
        $names = explode('/', $name);
        $services_dir = ROOT.DS.'app'.DS.'services';
        $template = file_get_contents('Commands/templates/service/ServiceTemplate.txt');
        $namespace = 'App\Services';
        if(isset($names[1])) {
            $module_name = $names[0];
            $module_class_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $module_name)));
            array_shift($names);
            $name = implode('/', $names);
            if(!is_dir(ROOT.DS.'modules'.DS.$module_name)) {
                $io->error("Module `".$module_name."` does not exists!");
                return false;
            }
            $services_dir = ROOT.DS.'modules'.DS.$module_name.DS.'services';
            if(!is_dir($services_dir)) {
                mkdir($services_dir);
            }

            $namespace = $module_class_name.'\\Services';
        }

        $service_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));

        $file_path = $services_dir.DS.$service_name.'Service.php';
        if(file_exists($file_path)) {
            $text = $namespace.'\\'.$service_name.'Service already exists!';
            $io->error($text);
            return false;
        }
        $template = str_replace('[NAMESPACE]', $namespace, $template);
        $template = str_replace('[SERVICE_NAME]', $service_name, $template);

        $text = $namespace.'\\'.$service_name.'Service has been generated';
        $file = fopen($file_path, 'w');
        fwrite($file, $template);
        fclose($file);
        $io->success($text);
       


    }
}