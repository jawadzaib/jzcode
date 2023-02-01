<?php
namespace Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateControllerCommand extends Command
{
    protected $commandName = 'make:controller';
    protected $commandDescription = "Generate new Controller";

    protected $commandArgumentName = "name";
    protected $commandArgumentDescription = "Name of the Controller";

    protected $commandCRUDOptionName = "crud"; // should be specified like "make:controller --crud"
    protected $commandCRUDOptionDescription = 'this will generate CRUD actions for controller';

    protected $commandViewOptionName = "view"; // should be specified like "make:controller --view"
    protected $commandViewOptionDescription = 'this will generate view for each action of controller';

    protected $commandRoutingOptionName = "routing"; // should be specified like "make:controller --routing"
    protected $commandRoutingOptionDescription = 'this will set default routing for controller';   

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
               $this->commandCRUDOptionName,
               null,
               InputOption::VALUE_NONE,
               $this->commandCRUDOptionDescription
            )
            ->addOption(
               $this->commandViewOptionName,
               null,
               InputOption::VALUE_NONE,
               $this->commandViewOptionDescription
            )
            ->addOption(
               $this->commandRoutingOptionName,
               null,
               InputOption::VALUE_NONE,
               $this->commandRoutingOptionDescription
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
            $name =$io->ask('Controller Name:', null, function($response) {
                return $response;
            });
        }

        $module_name = null;
        $names = explode('/', $name);
        $controllers_dir = ROOT.DS.'app'.DS.'controllers';
        $views_dir = ROOT.DS.'app'.DS.'views';
        $template = file_get_contents('Commands/templates/controller/ControllerTemplate.txt');
        $namespace = 'App\Controllers\\';
        $routes_file_path = ROOT.DS.'config'.DS.'routes.php';
        if(isset($names[1])) {
            $module_name = $names[0];
            $module_class_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $module_name)));
            array_shift($names);
            $name = implode('/', $names);
            if(!is_dir(ROOT.DS.'modules'.DS.$module_name)) {
                $io->error("Module `".$module_name."` does not exists!");
                return false;
            }
            $controllers_dir = ROOT.DS.'modules'.DS.$module_name.DS.'controllers';
            if(!is_dir($controllers_dir)) {
                mkdir($controllers_dir);
            }
            $views_dir = ROOT.DS.'modules'.DS.$module_name.DS.'templates';
            if(!is_dir($views_dir)) {
                mkdir($views_dir);
            }
            $template = file_get_contents('Commands/templates/controller/ModuleControllerTemplate.txt');
            $template = str_replace('[MODULE_NAME]', $module_class_name, $template);
            $namespace = $module_class_name.'\\Controllers\\';
            $routes_file_path = ROOT.DS.'modules'.DS.$module_name.DS.'routes.php';
            if(!file_exists($routes_file_path)) {
                $routes_file_handle = fopen($routes_file_path);
                fwrite($routes_file_handle, "<?php\n");
                fwrite($routes_file_handle, "use Core\Router;\n\n");
                fclose($routes_file_handle);
            }
        }

        $controller_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        $routes_url = $controller_name;
        if($module_name) {
            $routes_url = $module_class_name.'/'.$controller_name;
        }
        $file_path = $controllers_dir.DS.$controller_name.'Controller.php';
        if(file_exists($file_path)) {
            $text = $namespace.$controller_name.'Controller already exists!';
            $io->error($text);
            return false;
        }
        $template = str_replace('[CONTROLLER_NAME]', $controller_name, $template);
        $view_dir_path = $views_dir.DS.$name;

        $layout_name = 'default';
        // Arguments
        $generateCRUD = $input->getOption($this->commandCRUDOptionName);
        $generateView = $input->getOption($this->commandViewOptionName);
        $generateRouting = $input->getOption($this->commandRoutingOptionName);
        if(!$is_internal) {
            if(!$generateCRUD) {
                $generateCRUD = $io->confirm('Generate CRUD actions?', false);
            }
            if(!$generateView) {
                $generateView = $io->confirm('Generate views for Controller actions?', false);
            } 
            if(!$generateRouting) {
                $generateRouting = $io->confirm('Set Default routes for Controller?', false);
            } 
            // layout handling
            $layout_name =$io->ask('Theme layout for Controller:', $layout_name, function($response) {
                return $response;
            });
        }

        if($layout_name == 'default') {
            $template = str_replace('[LOAD_LAYOUT]', "", $template);
        } else {
            $template = str_replace('[LOAD_LAYOUT]', "\$this->view->setLayout('".$layout_name."');", $template);
        }

        // Index View Handling
        $view_template = null;
        if($generateView) {
            if(!is_dir($view_dir_path)) {
                $view_dir = mkdir($view_dir_path);
            }
            $view_template = file_get_contents('Commands/templates/controller/ViewTemplate.txt');
            if(!file_exists($view_dir_path.DS.'index.php')) {
                $file = fopen($view_dir_path.DS.'index.php', 'w');
                $index_template = $view_template;
                $index_template = str_replace('[SITE_TITLE]', SITE_TITLE, $index_template);
                $index_template = str_replace('[CONTROLLER_NAME]', $namespace.$controller_name, $index_template);
                $index_template = str_replace('[VIEW_NAME]', 'Index', $index_template);
                fwrite($file, $index_template);
                fclose($file);
            }
            $template = str_replace('[INDEX_VIEW]', "\$this->view->render('".$name."/index');", $template);
        } else {
            $template = str_replace('[INDEX_VIEW]', "", $template);
        }
        if($generateRouting) {
            $routes_file = fopen($routes_file_path, 'a');
            fwrite($routes_file, "\n");
            fwrite($routes_file, "\n");
            fwrite($routes_file, '//Routes for '.$controller_name.'Controller');
            fwrite($routes_file, "\n");
            fwrite($routes_file, "Router::route('".$routes_url."', '".$controller_name."', 'index', '".$module_name."');\n");
            fwrite($routes_file, "Router::route('".$routes_url."/index', '".$controller_name."', 'index', '".$module_name."');\n");
        }


        if ($generateCRUD) {
            $crud_template = file_get_contents('Commands/templates/controller/CRUD_METHODS.txt');
            if($generateView) {
                if(!file_exists($view_dir_path.DS.'form.php')) {
                    $file = fopen($view_dir_path.DS.'form.php', 'w');
                    $form_template = $view_template;
                    $form_template = str_replace('[SITE_TITLE]', SITE_TITLE, $form_template);
                    $form_template = str_replace('[CONTROLLER_NAME]', $namespace.$controller_name, $form_template);
                    $form_template = str_replace('[VIEW_NAME]', 'Add/Edit', $form_template);
                    fwrite($file, $form_template);
                    fclose($file);
                }
                $crud_template = str_replace('[FORM_VIEW]', "\$this->view->render('".$name."/form');", $crud_template);
            } else {
                $crud_template = str_replace('[FORM_VIEW]', "", $crud_template);
            }
            $template = str_replace('[CRUD_METHODS]', $crud_template, $template);

            if($generateRouting) {
                fwrite($routes_file, "Router::route('".$routes_url."/new', '".$controller_name."', 'new', '".$module_name."');\n");
                fwrite($routes_file, "Router::route('".$routes_url."/edit/:id', '".$controller_name."', 'edit', '".$module_name."');\n");
                fwrite($routes_file, "Router::route('".$routes_url."/delete/:id', '".$controller_name."', 'delete', '".$module_name."');");
            }
        } else {
            $template = str_replace('[CRUD_METHODS]', '', $template);
        }
        fclose($routes_file);
        $text = $namespace.$controller_name.'Controller has been generated';
        $file = fopen($file_path, 'w');
        fwrite($file, $template);
        fclose($file);
        $io->success($text);
       


    }
}