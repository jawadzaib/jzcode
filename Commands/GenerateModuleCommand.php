<?php
namespace Commands;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Core\DB;
class GenerateModuleCommand extends Command
{
    protected $commandName = 'make:module';
    protected $commandDescription = "Generate new Module";

    protected $commandArgumentName = "name";
    protected $commandArgumentDescription = "Name of the Module";

   

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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument($this->commandArgumentName);

        $io = new SymfonyStyle($input, $output);

        


        while (!$name) {
            $name =$io->ask('Module Name:', null, function($response) {
                return $response;
            });
        }
        $name = strtolower(str_replace(' ', '_', $name));
        $module_dir_path = ROOT.DS.'modules'.DS.$name;
        if(is_dir($module_dir_path) || $name === 'module') {
            $text = 'Module `'.$name.'` already exists!';
            $io->error($text);
            return false;
        }
        $module_class_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));

        $module_dir = mkdir($module_dir_path);

        $module_class_template = file_get_contents('Commands/templates/module/ModuleClassTemplate.txt');
        $module_class_template = str_replace('[MODULE_CLASS_NAME]', $module_class_name, $module_class_template);
        $file = fopen($module_dir_path.DS.$module_class_name.'.php', 'w');
        fwrite($file, $module_class_template);
        fclose($file);

        $config_file = fopen($module_dir_path.DS.'config.php', 'w');
        fwrite($config_file, "<?php\n");
        fwrite($config_file, "// set configuration for your module\n");
        fwrite($config_file, "\$config = [];");
        fclose($config_file);

        $routes_file = fopen($module_dir_path.DS.'routes.php', 'w');
        fwrite($routes_file, "<?php\n");
        fwrite($routes_file, "use Core\Router;\n\n");
        fwrite($routes_file, "// set routes for your module\n");
        fclose($routes_file);

        $controllers_dir = mkdir($module_dir_path.DS.'controllers');
        $services_dir = mkdir($module_dir_path.DS.'services');
        $templates_dir = mkdir($module_dir_path.DS.'templates');

        // registering module
        $modules_config_content = file_get_contents(ROOT.DS.'config'.DS.'modules.php');
        $modules_config_content = trim($modules_config_content);
        $modules_config_content .= "\n";
        $modules_config_content .= "Module::register('".$name."', array('active' => true, 'version' => '1.0.0'));";
        $modules_config_file = fopen(ROOT.DS.'config'.DS.'modules.php', 'w');
        fwrite($modules_config_file, $modules_config_content);
        fclose($modules_config_file);

        $generateController = $io->confirm("Do you want to create controller for module?", false);
        if($generateController) {
            $controller = $io->ask("Controller Name: ", $name, function($response) {
                return $response;
            });
            $command = 'php jz make:controller '.$name.'/'.$controller.' --internal';
            if($io->confirm('Generate CRUD actions?', false)) {
            	$command .= ' --crud';
            }
            if($io->confirm('Generate views for Controller actions?', false)) {
            	$command .= ' --view';
            }
            if($io->confirm('Set Default routes for Controller?', false)) {
            	$command .= ' --routing';
            }
            $process = new Process($command);
            $process->run();

            // executes after the command finishes
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            $io->success($process->getOutput());
        }

        $io->success("Module `".$name."` has been created Successfully!");
    }
}