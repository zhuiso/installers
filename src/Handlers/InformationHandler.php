<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
namespace Zs\Installer\Handlers;

use Zs\Foundation\Routing\Abstracts\Handler;

/**
 * Class InformationHandler.
 */
class InformationHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->withCode(200)->withData([
            'version' => $this->container->version(),
        ])->withMessage('获取信息成功！');
    }
}
