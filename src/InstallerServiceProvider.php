<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
namespace Zs\Installer;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router;
use Zs\Foundation\Http\Abstracts\ServiceProvider;
use Zs\Installer\Commands\InstallCommand;
use Zs\Installer\Commands\IntegrationCommand;
use Zs\Installer\Commands\IntegrationConfigurationCommand;
use Zs\Installer\Contracts\Prerequisite;
use Zs\Installer\Controllers\Api\InstallController as InstallApiController;
use Zs\Installer\Controllers\InstallController;
use Zs\Installer\Listeners\CsrfTokenRegister;
use Zs\Installer\Prerequisite\PhpExtension;
use Zs\Installer\Prerequisite\PhpVersion;
use Zs\Installer\Prerequisite\WritablePath;

/**
 * Class InstallServiceProvider.
 */
class InstallerServiceProvider extends ServiceProvider
{
    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * InstallerServiceProvider constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->dispatcher = $app[Dispatcher::class];
        $this->router = $app[Router::class];
    }

    /**
     * Boot service provider.
     */
    public function boot()
    {
        if (!$this->app->isInstalled()) {
            $this->app->make(Dispatcher::class)->subscribe(CsrfTokenRegister::class);
            $this->app->make('router')->resource('/', InstallController::class, [
                'only' => 'index',
            ]);
            $this->app->make('router')->group(['middleware' => ['cross'], 'prefix' => 'api'], function () {
                $this->app->make('router')->post('check', InstallApiController::class . '@check');
                $this->app->make('router')->post('database', InstallApiController::class . '@database');
                $this->app->make('router')->post('information', InstallApiController::class . '@information');
                $this->app->make('router')->post('install', InstallApiController::class . '@install');
            });
        }
        $this->commands([
            InstallCommand::class,
            IntegrationCommand::class,
            IntegrationConfigurationCommand::class,
        ]);
        $this->loadViewsFrom(realpath(__DIR__ . '/../resources/views'), 'install');
        $this->loadTranslationsFrom(realpath(__DIR__ . '/../resources/translations'), 'install');
    }

    /**
     * Register for service provider.
     */
    public function register()
    {
        $this->app->singleton(Prerequisite::class, function () {
            return new Composite(new PhpVersion('5.6.28'), new PhpExtension([
                'dom',
                'fileinfo',
                'gd',
                'json',
                'mbstring',
                'openssl',
            ]), new WritablePath([
                static_path(),
                storage_path(),
            ]));
        });
    }
}
