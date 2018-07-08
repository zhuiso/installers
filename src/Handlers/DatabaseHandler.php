<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
namespace Zs\Installer\Handlers;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;
use Zs\Foundation\Routing\Abstracts\Handler;
use PDO;
use Predis\Connection\ConnectionException;

/**
 * Class DatabaseHandler.
 */
class DatabaseHandler extends Handler
{
    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $repository;

    /**
     * DatabaseHandler constructor.
     *
     * @param \Illuminate\Container\Container         $container
     * @param \Illuminate\Contracts\Config\Repository $repository
     */
    public function __construct(Container $container, Repository $repository)
    {
        parent::__construct($container);
        $this->repository = $repository;
    }

    /**
     * Execute Handler.
     */
    public function execute()
    {
        if ($this->container->isInstalled()) {
            $this->withCode(500)->withError('Zs 已经安装，无需重复安装！');
        } else {
            if ($this->request->input('database_engine') == 'sqlite') {
                $this->repository->set('database.redis.default', [
                    'host'     => $this->request->input('redis_host', 'localhost'),
                    'password' => $this->request->input('redis_password') ?: null,
                    'port'     => $this->request->input('redis_port', 6379),
                    'database' => 0,
                ]);
                try {
                    $this->container->make('redis')->connect();
                    $this->withCode(200)->withMessage('数据库验证正确！');
                } catch (Exception $exception) {
                    switch ($exception->getCode()) {
                        case 0:
                            $error = 'Redis 密码未设置或密码错误！';
                            break;
                        case 99:
                            $error = 'Redis 地址不可访问！';
                            break;
                        case 111:
                            $error = 'Redis 配置错误！';
                            break;
                        default:
                            $error = '未知错误！';
                    }
                    $this->withCode(500)->withData($exception->getTrace())->withError($error);
                }
            } else {
                $this->repository->set('database', [
                    'fetch'       => PDO::FETCH_CLASS,
                    'default'     => $this->request->input('database_engine'),
                    'connections' => [],
                    'redis'       => [],
                ]);
                $sql = '';
                switch ($this->request->input('database_engine')) {
                    case 'mysql':
                        $this->repository->set('database.connections.mysql', [
                            'driver'    => 'mysql',
                            'host'      => $this->request->input('database_host'),
                            'database'  => $this->request->input('database_name'),
                            'username'  => $this->request->input('database_username'),
                            'password'  => $this->request->input('database_password'),
                            'charset'   => 'utf8',
                            'collation' => 'utf8_unicode_ci',
                            'prefix'    => $this->request->input('database_prefix'),
                            'port'      => $this->request->input('database_port') ?: 3306,
                            'strict'    => true,
                            'engine'    => null,
                        ]);
                        $sql = 'show tables';
                        break;
                    case 'pgsql':
                        $this->repository->set('database.connections.pgsql', [
                            'driver'   => 'pgsql',
                            'host'     => $this->request->input('database_host'),
                            'database' => $this->request->input('database_name'),
                            'username' => $this->request->input('database_username'),
                            'password' => $this->request->input('database_password'),
                            'charset'  => 'utf8',
                            'prefix'   => $this->request->input('database_prefix'),
                            'port'     => $this->request->input('database_port') ?: 5432,
                            'schema'   => 'public',
                        ]);
                        $sql = "select * from pg_tables where schemaname='public'";
                        break;
                }
                $this->repository->set('database.redis.default', [
                    'host'     => $this->request->input('redis_host', 'localhost'),
                    'password' => $this->request->input('redis_password') ?: null,
                    'port'     => $this->request->input('redis_port', 6379),
                    'database' => 0,
                ]);
                try {
                    $this->container->make('redis')->connect();
                    $results = collect($this->container->make('db')->select($sql));
                    if ($results->count()) {
                        $this->withCode(500)->withError('数据库[' . $this->request->input('database_name') . ']已经存在数据表，请先清空数据库！');
                    } else {
                        $this->withCode(200)->withMessage('数据库验证正确！');
                    }
                } catch (Exception $exception) {
                    if ($exception instanceof ConnectionException) {
                        $message = $exception->getMessage();
                        if (Str::contains($message, 'Name or service not known')) {
                            $error = 'Redis 服务未安装或服务地址错误！';
                        } else if (Str::contains($message, 'no password is set')) {
                            $error = 'Redis 服务未设置密码，不必填写密码！';
                        } else if (Str::contains($message, 'Connection refused')) {
                            $error = 'Redis 服务连接请求被拒绝，请检查配置输入是否正确！';
                        }
                    } else {
                        switch ($exception->getCode()) {
                            case 7:
                                $error = '数据库账号或密码错误，或数据库不存在！';
                                break;
                            case 1045:
                                $error = '数据库账号或密码错误！';
                                break;
                            case 1049:
                                $error = '数据库[' . $this->request->input('database_name') . ']不存在，请先创建数据库！';
                                break;
                            default:
                                $error = array_merge((array)$exception->getCode(), (array)$exception->getMessage());
                                break;
                        }
                    }
                    $this->withCode(500)->withData([
                        'code'   => $exception->getCode(),
                        'traces' => $exception->getTrace(),
                    ])->withError($error);
                }
            }
        }
    }
}
