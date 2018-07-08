<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
namespace Zs\Installer\Contracts;

/**
 * Interface PrerequisiteContract.
 */
interface Prerequisite
{
    /**
     * Checking prerequisite's rules.
     *
     * @return mixed
     */
    public function check();

    /**
     * Get prerequisite's error message.
     *
     * @return mixed
     */
    public function getMessages();
}
