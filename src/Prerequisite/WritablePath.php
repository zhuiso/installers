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
 * Class WritablePath.
 */
class WritablePath extends Prerequisite
{
    /**
     * @var array
     */
    protected $paths;

    /**
     * WritablePath constructor.
     *
     * @param array $paths
     */
    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * Checking prerequisite's rules.
     */
    public function check()
    {
        $isWritable = collect();
        $notWritable = collect();
        foreach ($this->paths as $path) {
            if (!is_writable($path)) {
                $notWritable->push(realpath($path));

            } else {
                $isWritable->push(realpath($path));

            }
        }
        $isWritable->count() && $this->messages[] = [
            'type' => 'message',
            'message' => "目录权限检测通过，路径 '" . $isWritable->implode("', '") . "' 可写。",
        ];
        $notWritable->count() && $this->messages[] = [
            'type' => 'error',
            'detail' => '',
            'help' => '',
            'message' => "目录 '" . $notWritable->implode("', '") . "' 不可写！",
        ];
    }
}
