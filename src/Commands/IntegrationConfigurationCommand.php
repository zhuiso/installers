<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
class IntegrationConfigurationCommand extends Command
{
    public function configure()
    {
        $this->addOption('driver', null, InputOption::VALUE_REQUIRED, 'Database driver, such as mysql, pgsql, sqlite.');
        $this->addOption('host', null, InputOption::VALUE_REQUIRED, 'Database host.');
        $this->addOption('port', null, InputOption::VALUE_REQUIRED, 'Database port.');
        $this->addOption('database', null, InputOption::VALUE_REQUIRED, 'Database name.');
        $this->addOption('username', null, InputOption::VALUE_REQUIRED, 'Database username.');
        $this->addOption('password', null, InputOption::VALUE_OPTIONAL, 'Database password.');
        $this->addOption('prefix', null, InputOption::VALUE_REQUIRED, 'Database prefix.');
        $this->setName('integration:configuration');
    }

    public function handle()
    {
        $file = $this->container->environmentFilePath();
        $this->file->exists($file) || touch($file);
        $database = new Collection(Yaml::parse(file_get_contents($file)));
        $database->put('DB_CONNECTION', $this->input->getOption('driver'));
        $database->put('DB_HOST', $this->input->getOption('host'));
        $database->put('DB_PORT', $this->input->getOption('port'));
        $database->put('DB_DATABASE', $this->input->getOption('driver') == 'sqlite' ? $this->container->storagePath() . DIRECTORY_SEPARATOR . 'bootstraps' . DIRECTORY_SEPARATOR . 'database.sqlite' : $this->input->getOption('database'));
        $database->put('DB_USERNAME', $this->input->getOption('username'));
        $database->put('DB_PASSWORD', $this->input->getOption('password') ?: '');
        $database->put('DB_PREFIX', $this->input->getOption('prefix'));

        file_put_contents($file, Yaml::dump($database->toArray()));
    }
}
