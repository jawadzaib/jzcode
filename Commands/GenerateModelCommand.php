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
class GenerateModelCommand extends Command
{
    protected $commandName = 'make:model';
    protected $commandDescription = "Generate new Model";

    protected $commandArgumentName = "name";
    protected $commandArgumentDescription = "Name of the Model";

    protected $commandRepoOptionName = "repository"; // should be specified like "make:controller --crud"
    protected $commandRepoOptionDescription = 'this will generate Repository';

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
               $this->commandRepoOptionName,
               null,
               InputOption::VALUE_NONE,
               $this->commandRepoOptionDescription
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument($this->commandArgumentName);

        $io = new SymfonyStyle($input, $output);


        while (!$name) {
            $name =$io->ask('Model Name:', null, function($response) {
                return $response;
            });
        }
        $model_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        $file_path = ROOT.DS.'app'.DS.'models'.DS.$model_name.'.php';
        if(file_exists($file_path)) {
            $text = $model_name.' Model already exists!';
            $io->error($text);
            return false;
        }

        $generateMapping = $io->confirm("Do you want to map with Database Table?", true);
        $table_name = false;
        if($generateMapping) {
            while(!$table_name) {
                $table_name = $io->ask('DB Table:', $name, function($response) {
                    return $response;
                });
            }
            $table_name = DB_TABLE_PREFIX.$table_name;
        }

        $fields = [
            ['name' => 'id', 'type' => 'integer', 'length' => 11, 'is_primary' => true, 'is_unique' => true, 'is_null' => false, 'default' => 'none']
        ];
        $createFields = $io->confirm("Do you want to create extra properties?", false);
        if($createFields) {
            do {
                $field = [];
                $property = null;
                while(!$property) {
                    $property = $io->ask("Property Name", null, function($response) {
                        return $response;
                    });
                }
                $property = explode(' ', trim($property));
                $field['name'] = $property[0];
                array_shift($property);
                if($generateMapping) {
                    if(isset($property[0])) {
                        $field['type'] = $property[0];
                        array_shift($property);
                    } else {
                        $field['type'] = $io->ask('Choose Type(integer, string, text, date, datetime):', 'string', function($response) {
                            return $response;
                        });
                    }
                    // if(class_exists('App\Models\\'.$field['type'])) {
                    //     $io->listing([
                    //         "1: OneToMany",
                    //         "2: OneToOne",
                    //         "3: ManyToMany"
                    //     ]);
                    //     $relation_type = $io->ask('Choose Relation Type?', 1, function($response) {
                    //         return $response;
                    //     });
                    // }
                    $field['length'] = null;
                    if($field['type'] == 'string') {
                        if(isset($property[0])) {
                            $field['length'] = (int)$property[0];
                            array_shift($property);
                        } else {
                            $field['length'] = $io->ask('Length', 100, function($response) {
                                return (int)$response;
                            });
                        }
                    }
                    $field['is_primary'] = false;
                    if(isset($property[0])) {
                        if(strtolower($property[0]) === 'unique') {
                            $field['is_unique'] = true;
                            array_shift($property);
                        }
                    }
                    if(isset($property[0])) {
                        if(strtolower($property[0]) === 'null') {
                            $field['is_null'] = true;
                        }
                    }
                    if(!isset($field['is_null'])) {
                        $field['is_null'] = $io->confirm("Can be NULL?", true);
                    }
                    if(!isset($field['is_unique'])) {
                        $field['is_unique'] = $io->confirm("is this Unique?", false);
                    }
                    if(!$field['is_null']) {
                        if(isset($property[0]) && trim($property[0]) !== '') {
                            $field['default'] = $property[0];
                        } else {
                            $field['default'] = $io->ask('Default Value', '', function($response) {
                                return $response;
                            });
                        }
                    } else {
                        $field['default'] = null;
                    }
                }

                array_push($fields, $field);
                $create_more = $io->confirm("Do you want more?", true);
            } while($create_more);
            // if($generateMapping) {
            //     $io->title("Table Schema: ".$table_name);
            //     $io->table(
            //         array_keys($fields[0]),
            //         $fields
            //     );
            // }
        }


        $model_properties_html = "";
        $model_methods_html = "";
        if(!empty($fields)) {
            foreach ($fields as $field) {
                $name = $field['name'];
                if($generateMapping) {
                    $type = $field['type'];
                    $model_properties_html .= "\t/**\n";
                    if(isset($field['is_primary']) && $field['is_primary']) {
                        $model_properties_html .= "\t* @Id\n";
                        $model_properties_html .= "\t* @GeneratedValue\n";
                    }
                    $model_properties_html .= "\t* @Column(type=\"{$type}\"";
                    if(isset($field['length']) && $field['length']) {
                        $model_properties_html .= ", length=".(int)$field['length'];
                    }
                    if(isset($field['is_null'])) {
                        $field['is_null'] = ($field['is_null']) ? 'true' : 'false';
                        $model_properties_html .= ", nullable=".$field['is_null'];
                    }
                    if(isset($field['is_unique']) && $field['is_unique']) {
                        $model_properties_html .= ", unique=true";
                    }
                    if(isset($field['default']) && $field['default'] != '') {
                        $model_properties_html .= ", options={\"default\":\"".$field['default']."\"}";
                    }
                    $model_properties_html .= ")\n";
                    $model_properties_html .= "\t*/\n";
                }
                $model_properties_html .= "\tprotected \${$name};\n";

                $method_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));

                $getter_setter_template = file_get_contents('Commands/templates/model/Getter_Setter.txt');
                $getter_setter_template = str_replace('[GETTER_METHOD]', "get".$method_name, $getter_setter_template);
                $getter_setter_template = str_replace('[SETTER_METHOD]', "set".$method_name, $getter_setter_template);
                $getter_setter_template = str_replace('[PROPERTY]', $name, $getter_setter_template);
                $model_methods_html .= $getter_setter_template."\n";
            }
        }

        $generateRepository = $input->getOption($this->commandRepoOptionName);
        if(!$generateRepository) {
            $generateRepository = $io->confirm("Do you want to create Repository?", false);
        }
        $repository_annotation = '';
        if($generateRepository) {
            $repository = $io->ask('Repository Name:', $model_name.'s', function($response) {
                return $response;
            });
            $repository = str_replace(' ', '', ucwords(str_replace('_', ' ', $repository)));

            $process = new Process('php jz make:repository '.$repository);
            $process->run();

            // executes after the command finishes
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $io->writeln($process->getOutput());
            $repository_annotation = '(repositoryClass="App\Repositories\\'.$repository.'Repository")';
        }

        $template = file_get_contents('Commands/templates/model/ModelTemplate.txt');
        $template = str_replace('[MODEL_NAME]', $model_name, $template);
        $entity_annotation_html = '';
        if($generateMapping) {
            $entity_annotation_html .= "/**\n";
            $entity_annotation_html .= "* @Entity".$repository_annotation."\n";
            $entity_annotation_html .= "* @Table(name=\"{$table_name}\")\n";
            $entity_annotation_html .= "*/\n";
        }
        $template = str_replace('[ENTITY_ANNOTATION]', $entity_annotation_html, $template);
        $template = str_replace('[MODEL_PROPERTIES]', $model_properties_html, $template);
        $template = str_replace('[MODEL_GETTERS_SETTERS]', $model_methods_html, $template);
        $file = fopen(ROOT.DS.'app'.DS.'models'.DS.$model_name.'.php', 'w');
        fwrite($file, $template);
        fclose($file);
        $text = 'App\Models\\'.$model_name.' has been generated';
        $io->success($text);

        if($generateMapping) {
            $process = new Process('php vendor/doctrine/orm/bin/doctrine.php orm:schema-tool:update --force');
            $process->run();

            // executes after the command finishes
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $io->writeln($process->getOutput());
        }
        


    }
}