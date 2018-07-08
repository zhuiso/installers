<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
namespace Zs\Installer\Commands;

use Zs\Foundation\Console\Abstracts\Command;
use Zs\Foundation\Member\Member;

/**
 * Class IntegrationCommand.
 */
class IntegrationCommand extends Command
{
    /**
     * Configure command.
     */
    protected function configure()
    {
        $this->setDescription('Run zs\'s integration testing');
        $this->setName('integration');
    }

    /**
     * Command handler.
     */
    public function handle()
    {
        $this->call('migrate', [
            '--force' => true,
        ]);

        $this->call('jwt:generate');

        $this->call('vendor:publish', [
            '--force' => true,
        ]);

        $this->setting->set('application.version', $this->container->version());
        $this->setting->set('site.enabled', true);
        $this->setting->set('site.name', 'Zs');
        $this->setting->set('setting.image.engine', 'normal');
        $this->setting->set('module.zs/administration.enabled', true);

        Member::query()->create([
            'name'     => 'admin',
            'email'    => 'admin@zs.com',
            'password' => bcrypt('123qwe'),
        ]);

        $this->call('key:generate');
        touch($this->container->storagePath() . DIRECTORY_SEPARATOR . 'installed');
        $this->info('Zs Installed!');
    }
}
