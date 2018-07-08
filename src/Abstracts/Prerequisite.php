<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
namespace Zs\Installer\Abstracts;

use Zs\Installer\Contracts\Prerequisite as PrerequisiteContract;

/**
 * Class Prerequisite.
 */
abstract class Prerequisite implements PrerequisiteContract
{
    /**
     * @var array
     */
    protected $messages = [];

    /**
     * Checking prerequisite's rules.
     *
     * @return mixed
     */
    abstract public function check();

    /**
     * Get prerequisite's error message.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
