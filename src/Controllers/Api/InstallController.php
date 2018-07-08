<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
namespace Zs\Installer\Controllers\Api;

use Zs\Foundation\Routing\Abstracts\Controller;
use Zs\Installer\Handlers\CheckHandler;
use Zs\Installer\Handlers\DatabaseHandler;
use Zs\Installer\Handlers\InformationHandler;
use Zs\Installer\Handlers\InstallHandler;

/**
 * Class InstallController.
 */
class InstallController extends Controller
{
    /**
     * Checking handler.
     *
     * @param \Zs\Installer\Handlers\CheckHandler $handler
     *
     * @return \Zs\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function check(CheckHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * Database handler.
     *
     * @param \Zs\Installer\Handlers\DatabaseHandler $handler
     *
     * @return \Zs\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     */
    public function database(DatabaseHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * Information handler.
     *
     * @param \Zs\Installer\Handlers\InformationHandler $handler
     *
     * @return \Zs\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     */
    public function information(InformationHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * Install handler.
     *
     * @param \Zs\Installer\Handlers\InstallHandler $handler
     *
     * @return \Zs\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function install(InstallHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
