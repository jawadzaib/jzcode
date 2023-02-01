<?php
namespace Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateRepositoryCommand extends Command
{
    protected $commandName = 'make:repository';
    protected $commandDescription = "Generate new Repository";

    protected $commandArgumentName = "name";
    protected $commandArgumentDescription = "Name of the Repository";

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
            $name =$io->ask('Repository Name', null, function($response) {
                return $response;
            });
        }

        $names = explode('/', $name);
        $repositories_dir = ROOT.DS.'app'.DS.'repositories';
        $template = file_get_contents('Commands/templates/repository/RepositoryTemplate.txt');
        $namespace = 'App\Repositories';

        $repository_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));

        $file_path = $repositories_dir.DS.$repository_name.'Repository.php';
        if(file_exists($file_path)) {
            $text = $namespace.'\\'.$repository_name.'Repository already exists!';
            $io->error($text);
            return false;
        }
        $template = str_replace('[NAMESPACE]', $namespace, $template);
        $template = str_replace('[REPOSITORY_NAME]', $repository_name, $template);

        $text = $namespace.'\\'.$repository_name.'Repository has been generated';
        $file = fopen($file_path, 'w');
        fwrite($file, $template);
        fclose($file);
        $io->success($text);
       


    }
}