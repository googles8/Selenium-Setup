<?php
namespace SeleniumSetup\Controller;

use SeleniumSetup\Config\Config;
use SeleniumSetup\Config\ConfigFactory;
use SeleniumSetup\Environment;
use SeleniumSetup\SeleniumSetup;
use SeleniumSetup\Service\StartServerService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartServer extends Command
{
    const CLI_COMMAND = 'start';
    
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $defaultName = basename(Config::DEFAULT_CONFIGURATION_FILENAME, '.json');

        $this
            ->setName(self::CLI_COMMAND)
            ->setDescription('Start Selenium Server setup with all supported drivers attached to it.')
            ->addArgument('name', InputArgument::OPTIONAL, 'The instance name.', $defaultName)
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The config path.');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFilePath = null;

        if ($input->getArgument('name')) {
            $configFilePath = SeleniumSetup::$APP_CONF_PATH . DIRECTORY_SEPARATOR . $input->getArgument('name') . '.json';
        }

        if ($input->getOption('config')) { 
            $configFilePath = realpath($input->getOption('config'));
        }

        // Prepare.
        $config = ConfigFactory::createFromConfigFile($configFilePath);
        $env = new Environment($config, $input, $output);

        $handler = new StartServerService($config, $env, $input, $output);

        if ($handler->test()) {
            if (!$env->isAdmin()) {
                $output->writeln('<comment>Running without elevated rights.</comment>');
            }
            $output->writeln('<comment>Let\'s go ...</comment>');
            $handler->handle();
        } else {
            $output->writeln('<error>Missing required components. Please review your setup.</error>');
        }
        
    }

}