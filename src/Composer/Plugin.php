<?php
// +----------------------------------------------------------------------
// | Dscription:  The file is part of Zs
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
// | CopyRight: (c) 2018 zhuiso.com
// +----------------------------------------------------------------------
namespace Zs\Installer\Composer;

use Composer\Composer;
//use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
//use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Zs\Installer\Composer\Installers\ExtensionInstaller;
use Zs\Installer\Composer\Installers\AddonInstaller;
use Zs\Installer\Composer\Installers\ModuleInstaller;

/**
 * Class Plugin.
 */
class Plugin implements /*EventSubscriberInterface, */PluginInterface
{
    /**
     * @var \Composer\Composer
     */
    protected $composer;

    /**
     * @var \Composer\IO\IOInterface
     */
    protected $io;

    /**
     * Add installer to Composer Installation Manager.
     *
     * @param \Composer\Composer       $composer
     * @param \Composer\IO\IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        $composer->getInstallationManager()->addInstaller(new AddonInstaller($io, $composer));
        $composer->getInstallationManager()->addInstaller(new ExtensionInstaller($io, $composer));
        $composer->getInstallationManager()->addInstaller(new ModuleInstaller($io, $composer));
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     * The array keys are event names and the value can be:
     * * The method name to call (priority defaults to 0)
     * * An array composed of the method name to call and the priority
     * * An array of arrays composed of the method names to call and respective
     *   priorities, or 0 if unset
     * For instance:
     * * array('eventName' => 'methodName')
     * * array('eventName' => array('methodName', $priority))
     * * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
//    public static function getSubscribedEvents()
//    {
//        return [
//            PluginEvents::INIT => 'onInit',
//        ];
//    }
}
