<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
namespace Zs\Installer\Handlers;

use Illuminate\Container\Container;
use Zs\Foundation\Routing\Abstracts\Handler;
use Zs\Installer\Contracts\Prerequisite;

/**
 * Class CheckingHandler.
 */
class CheckHandler extends Handler
{
    /**
     * @var \Zs\Installer\Contracts\Prerequisite
     */
    protected $prerequisite;

    /**
     * CheckingHandler constructor.
     *
     * @param \Illuminate\Container\Container          $container
     * @param \Zs\Installer\Contracts\Prerequisite $prerequisite
     */
    public function __construct(Container $container, Prerequisite $prerequisite)
    {
        parent::__construct($container);
        $this->prerequisite = $prerequisite;
    }

    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        if ($this->container->isInstalled()) {
            $this->withCode(500)->withError('Zs 已经安装，无需重复安装！');
        } else {
            $this->prerequisite->check();
            $this->withCode(200)->withData($this->prerequisite->getMessages())->withMessage('获取预安装检测信息成功！');
        }
    }
}
