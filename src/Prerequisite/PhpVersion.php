<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
namespace Zs\Installer\Prerequisite;

use Zs\Installer\Abstracts\Prerequisite;

/**
 * Class PhpVersion.
 */
class PhpVersion extends Prerequisite
{
    /**
     * @var string
     */
    protected $minVersion;

    /**
     * PhpVersion constructor.
     *
     * @param $minVersion
     */
    public function __construct($minVersion)
    {
        $this->minVersion = $minVersion;
    }

    /**
     * Checking prerequisite's rules.
     */
    public function check()
    {
        if (version_compare(PHP_VERSION, $this->minVersion, '<')) {
            $this->messages[] = [
                'type' => 'error',
                'detail' => '',
                'help' => '',
                'message' => "PHP 版本必须至少为 {$this->minVersion} ，当前运行版本为 " . PHP_VERSION . " ！",
            ];
        } else {
            $this->messages[] = [
                'type' => 'message',
                'message' => "PHP 版本检测通过，当前运行版本为 " . PHP_VERSION . " ！",
            ];
        }
    }
}
