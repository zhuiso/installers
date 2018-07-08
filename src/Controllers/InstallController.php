<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
namespace Zs\Installer\Controllers;

use Zs\Foundation\Routing\Abstracts\Controller;

/**
 * Class InstallController.
 */
class InstallController extends Controller
{
    /**
     * Index handler.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return $this->view('install::install');
    }
}
