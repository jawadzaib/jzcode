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

class AppInstallCommand extends Command
{
    protected $commandName = 'app:install';
    protected $commandDescription = "App Installation";
    

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $request_scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
        $http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';

        $app_title = $io->ask("App Title", "JZ Code", function($response) {
            return $response;
        });
        $app_url = $io->ask("App Root", "/", function($response) {
            return $response;
        });
        $hostname = $io->ask("Database Hostname", "127.0.0.1", function($response) {
            return $response;
        });
        $database = $io->ask("Database", "jz_code", function($response) {
            return $response;
        });
        $db_user = $io->ask("Database User", "root", function($response) {
            return $response;
        });
        $db_password = $io->ask("Database Password", "", function($response) {
            return $response;
        });
        $db_table_prefix = $io->ask("Database Table Prefix", "jz_", function($response) {
            return $response;
        });

        $default_content = file_get_contents('config'.DS.'constants.php');
        $content = str_replace('#DB_HOST_NAME#', $hostname, $default_content);
        $content = str_replace('#DB_NAME#', $database, $content);
        $content = str_replace('#DB_USER_NAME#', $db_user, $content);
        $content = str_replace('#DB_PASSWORD#', $db_password, $content);
        $content = str_replace('#DB_TABLE_PREFIX#', $db_table_prefix, $content);
        $content = str_replace('#PROOT#', $app_url, $content);
        $content = str_replace('#SITE_TITLE#', $app_title, $content);

        $file = fopen('config'.DS.'constants.php', 'w');
        fwrite($file, $content);
        fclose($file);
        $process = new Process('php vendor/doctrine/orm/bin/doctrine.php orm:schema-tool:update --force');
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            $file = fopen('config'.DS.'constants.php', 'w');
            fwrite($file, $default_content);
            fclose($file);
            throw new ProcessFailedException($process);
        }

        $io->writeln($process->getOutput());
        $io->success("App has been installed successfully!");
        $io->note("URL: ".$request_scheme."://".$http_host.$app_url);
    }
}