<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
namespace Zs\Installer\Commands;

use Illuminate\Support\Collection;
use Zs\Administration\ModuleServiceProvider;
use Zs\Foundation\Console\Abstracts\Command;
use Zs\Foundation\Member\Member;
use Zs\Foundation\Setting\Contracts\SettingsRepository;
use PDO;
use Symfony\Component\Yaml\Yaml;

/**
 * Class InstallCommand.
 */
class InstallCommand extends Command
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $data;

    /**
     * @var bool
     */
    protected $isDataSetted = false;

    /**
     * InstallCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data       = new Collection();
    }

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this->setDescription('Run zs\'s installation migration and seeds');
        $this->setName('install');
    }

    /**
     * Create administration user.
     */
    protected function createAdministrationUser()
    {
        $user = Member::query()->create([
            'name'     => $this->data->get('admin_account'),
            'email'    => $this->data->get('admin_email'),
            'password' => bcrypt($this->data->get('admin_password')),
        ]);
        if ($this->container->bound('request')) {
            $this->container->make('auth')->login($user);
        }
    }

    /**
     * Command handler.
     */
    public function handle()
    {
        if (! $this->isDataSetted) {
            $this->setDataFromConsoling();
        }
        $this->config->set('database', [
            'fetch'       => PDO::FETCH_OBJ,
            'default'     => $this->data->get('driver'),
            'connections' => [],
            'redis'       => [],
        ]);
        switch ($this->data->get('driver')) {
            case 'mysql':
                $this->config->set('database.connections.mysql', [
                    'driver'    => 'mysql',
                    'host'      => $this->data->get('database_host'),
                    'database'  => $this->data->get('database'),
                    'username'  => $this->data->get('database_username'),
                    'password'  => $this->data->get('database_password'),
                    'charset'   => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix'    => $this->data->get('database_prefix'),
                    'port'   => $this->data->get('database_port') ?: 3306,
                    'strict'    => true,
                    'engine'    => null,
                ]);
                break;
            case 'pgsql':
                $this->config->set('database.connections.pgsql', [
                    'driver'   => 'pgsql',
                    'host'     => $this->data->get('database_host'),
                    'database' => $this->data->get('database'),
                    'username' => $this->data->get('database_username'),
                    'password' => $this->data->get('database_password'),
                    'charset'  => 'utf8',
                    'prefix'   => $this->data->get('database_prefix'),
                    'port'   => $this->data->get('database_port') ?: 5432,
                    'schema'   => 'public',
                    'sslmode'  => 'prefer',
                ]);
                break;
            case 'sqlite':
                $this->config->set('database.connections.sqlite', [
                    'driver'   => 'sqlite',
                    'database' => $this->container->storagePath() . DIRECTORY_SEPARATOR . 'bootstraps' . DIRECTORY_SEPARATOR . 'database.sqlite',
                    'prefix'   => $this->data->get('database_prefix'),
                ]);
                touch($this->container->storagePath() . DIRECTORY_SEPARATOR . 'bootstraps' . DIRECTORY_SEPARATOR . 'database.sqlite');
                break;
        }

        $this->config->set('database.redis.default', [
            'host'     => $this->data->get('redis_host', 'localhost'),
            'password' => $this->data->get('redis_password') ?: null,
            'port'     => $this->data->get('redis_port', 6379),
            'database' => 0,
        ]);

        if (class_exists(ModuleServiceProvider::class)) {
            $this->container->getProvider(ModuleServiceProvider::class) || $this->container->register(ModuleServiceProvider::class);
        }

        $this->call('migrate', [
            '--force' => true,
        ]);

        $this->call('jwt:generate');

        $this->call('vendor:publish', [
            '--force' => true,
        ]);

        $setting = $this->container->make(SettingsRepository::class);
        $setting->set('application.version', $this->container->version());
        $setting->set('site.enabled', true);
        $setting->set('site.name', $this->data->get('website'));
        $setting->set('setting.image.engine', 'normal');
        $setting->set('module.zs/administration.enabled', true);
        $this->createAdministrationUser();
        $this->writingConfiguration();
        $this->call('key:generate');
        $this->redis->flushall();
        $this->info('Zs Installed!');
    }

    /**
     * Get data from console.
     */
    public function setDataFromConsoling()
    {
        $this->data->put('driver', $this->ask('数据库引擎(mysql/pgsql/sqlite)：'));
        if (in_array($this->data->get('driver'), [
            'mysql',
            'pgsql',
        ])) {
            $this->data->put('database_host', $this->ask('数据库服务器：'));
            $this->data->put('database', $this->ask('数据库名：'));
            $this->data->put('database_username', $this->ask('数据库用户名：'));
            $this->data->put('database_password', $this->ask('数据库密码：'));
        }
        $this->data->put('database_prefix', $this->ask('数据库表前缀：'));
        $this->data->put('admin_account', $this->ask('管理员帐号：'));
        $this->data->put('admin_password', $this->ask('管理员密码：'));
        $this->data->put('admin_password_confirmation', $this->ask('重复密码：'));
        $this->data->put('admin_email', $this->ask('电子邮箱：'));
        $this->data->put('website', $this->ask('网站标题：'));
        $this->info('所填写的信息是：');
        $this->info('数据库引擎：' . $this->data->get('driver'));
        if (in_array($this->data->get('driver'), [
            'mysql',
            'pgsql',
        ])) {
            $this->info('数据库服务器：' . $this->data->get('database_host'));
            $this->info('数据库名：' . $this->data->get('database'));
            $this->info('数据库用户名：' . $this->data->get('database_username'));
            $this->info('数据库密码：' . $this->data->get('database_password'));
        }
        $this->info('数据库表前缀：' . $this->data->get('database_prefix'));
        $this->info('管理员帐号：' . $this->data->get('admin_account'));
        $this->info('管理员密码：' . $this->data->get('admin_password'));
        $this->info('重复密码：' . $this->data->get('admin_password_confirmation'));
        $this->info('电子邮箱：' . $this->data->get('admin_email'));
        $this->info('网站标题：' . $this->data->get('website'));
        $this->isDataSetted = true;
    }

    /**
     * Get data from controller.
     *
     * @param array $data
     */
    public function setDataFromController(array $data)
    {
        $this->data->put('driver', $data['database_engine']);
        $this->data->put('database_host', $data['database_host']);
        $this->data->put('database', $data['database_name']);
        $this->data->put('database_username', $data['database_username']);
        $this->data->put('database_password', $data['database_password']);
        $this->data->put('database_port', $data['database_port']);
        $this->data->put('admin_account', $data['account_username']);
        $this->data->put('admin_password', $data['account_password']);
        $this->data->put('admin_email', $data['account_mail']);
        $this->data->put('redis_host', $data['redis_host']);
        $this->data->put('redis_password', $data['redis_password'] ?: null);
        $this->data->put('redis_port', $data['redis_port']);
        $this->data->put('website', $data['sitename']);
        $this->isDataSetted = true;
    }

    /**
     * Write configuration to file.
     */
    protected function writingConfiguration()
    {
        $file = $this->container->environmentFilePath();
        $this->file->exists($file) || touch($file);

        $database = new Collection(Yaml::parse(file_get_contents($file)));
        $database->put('BROADCAST_DRIVER', 'redis');
        $database->put('CACHE_DRIVER', 'redis');
        $database->put('DB_CONNECTION', $this->data->get('driver'));
        $database->put('DB_HOST', $this->data->get('database_host'));
        $database->put('DB_PORT', $this->data->get('database_port'));
        $database->put('DB_DATABASE', $this->data->get('driver') == 'sqlite' ? $this->container->storagePath() . DIRECTORY_SEPARATOR . 'bootstraps' . DIRECTORY_SEPARATOR . 'database.sqlite' : $this->data->get('database'));
        $database->put('DB_USERNAME', $this->data->get('database_username'));
        $database->put('DB_PASSWORD', $this->data->get('database_password'));
        $database->put('DB_PREFIX', $this->data->get('database_prefix'));
        $database->put('REDIS_HOST', $this->data->get('redis_host'));
        $this->data->get('redis_password') && $database->put('REDIS_PASSWORD', $this->data->get('redis_password'));
        $database->put('REDIS_PORT', $this->data->get('redis_port'));
        $database->put('QUEUE_DRIVER', 'sync');

        file_put_contents($file, Yaml::dump($database->toArray()));
        touch($this->container->storagePath() . DIRECTORY_SEPARATOR . 'installed');
    }
}
