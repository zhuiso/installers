<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
namespace Zs\Installer\Handlers;

use Illuminate\Contracts\Console\Kernel;
use Zs\Foundation\Routing\Abstracts\Handler;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class InstallHandler.
 */
class InstallHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function execute()
    {
        set_time_limit(0);
        if ($this->container->isInstalled()) {
            $this->withCode(500)->withError('Zs 已经安装，无需重复安装！');
        } else {
            try {
                if ($this->request->input('database_engine') === 'sqlite') {
                    $this->validate($this->request, [
                        'account_mail'     => 'required',
                        'account_password' => 'required',
                        'account_username' => 'required',
                        'database_engine'  => 'required',
                        'sitename'         => 'required',
                    ], [
                        'account_mail.required'     => '必须填写管理员邮箱',
                        'account_password.required' => '必须填写管理员账户',
                        'account_username.required' => '必须填写管理员密码',
                        'database_engine.required'  => '必须选择数据库引擎',
                        'sitename.required'         => '必须填写网站名称',
                    ]);
                } else {
                    $this->validate($this->request, [
                        'account_mail'      => 'required',
                        'account_password'  => 'required',
                        'account_username'  => 'required',
                        'database_engine'   => 'required',
                        'database_host'     => 'required',
                        'database_name'     => 'required',
                        'database_password' => 'required',
                        'database_username' => 'required',
                        'sitename'          => 'required',
                    ], [
                        'account_mail.required'      => '必须填写管理员邮箱',
                        'account_password.required'  => '必须填写管理员账户',
                        'account_username.required'  => '必须填写管理员密码',
                        'database_engine.required'   => '必须选择数据库引擎',
                        'database_host.required'     => '必须填写数据库地址',
                        'database_name.required'     => '必须填写数据库名称',
                        'database_password.required' => '必须填写数据库密码',
                        'database_username.required' => '必须填写数据库用户名',
                        'sitename.required'          => '必须填写网站名称',
                    ]);
                }
                $command = $this->getCommand('install');
                $command->setDataFromController($this->request->all());
                $input = new ArrayInput([]);
                $output = new BufferedOutput();
                $command->run($input, $output);
                $this->withCode(200)->withData([
                    'all'            => $this->request->all(),
                    'administration' => url('admin'),
                    'frontend'       => url(''),
                ])->withMessage('install::install.success');
            } catch (\Exception $exception) {
                $this->withCode($exception->getCode())->withError($exception->getMessage())->withExtra([
                    'trace'   => $exception->getTrace(),
                ]);
            }
        }
    }

    /**
     * Get a command from console instance.
     *
     * @param string $name
     *
     * @return \Zs\Foundation\Console\Abstracts\Command|\Symfony\Component\Console\Command\Command|\Zs\Installer\Commands\InstallCommand
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getCommand($name)
    {
        return $this->getConsole()->get($name);
    }

    /**
     * Get console instance.
     *
     * @return \Illuminate\Contracts\Console\Kernel|\Zs\Foundation\Console\Application
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getConsole()
    {
        $kernel = $this->container->make(Kernel::class);
        $kernel->bootstrap();

        return $kernel->getArtisan();
    }
}
