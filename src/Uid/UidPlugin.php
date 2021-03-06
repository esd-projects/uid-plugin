<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/21
 * Time: 15:39
 */

namespace ESD\Plugins\Uid;

use ESD\Core\Context\Context;
use ESD\Core\PlugIn\AbstractPlugin;
use ESD\Core\PlugIn\PluginInterfaceManager;
use ESD\Core\Server\Server;
use ESD\Plugins\Aop\AopConfig;
use ESD\Plugins\Aop\AopPlugin;
use ESD\Plugins\Uid\Aspect\UidAspect;

class UidPlugin extends AbstractPlugin
{
    /**
     * @var UidAspect
     */
    private $uidAspect;
    /**
     * @var UidConfig|null
     */
    private $uidConfig;
    /**
     * @var UidBean
     */
    private $uid;

    /**
     * 获取插件名字
     * @return string
     */
    public function getName(): string
    {
        return "UidBean";
    }

    /**
     * UidPlugin constructor.
     * @param UidConfig|null $uidConfig
     * @throws \DI\DependencyException
     * @throws \ReflectionException
     * @throws \DI\NotFoundException
     */
    public function __construct(?UidConfig $uidConfig = null)
    {
        parent::__construct();
        //需要aop的支持，所以放在aop后加载
        $this->atAfter(AopPlugin::class);
        if ($uidConfig == null) {
            $uidConfig = new UidConfig();
        }
        $this->uidConfig = $uidConfig;
    }

    /**
     * @param PluginInterfaceManager $pluginInterfaceManager
     * @return mixed|void
     * @throws \ESD\Core\Exception
     * @throws \ReflectionException
     */
    public function onAdded(PluginInterfaceManager $pluginInterfaceManager)
    {
        parent::onAdded($pluginInterfaceManager);
        $pluginInterfaceManager->addPlug(new AopPlugin());
    }

    /**
     * @param Context $context
     * @return mixed|void
 * @throws \ESD\Core\Exception
     */
    public function init(Context $context)
    {
        parent::init($context);
        $serverConfig = Server::$instance->getServerConfig();
        $aopConfig = DIGet(AopConfig::class);
        $aopConfig->addIncludePath($serverConfig->getVendorDir() . "/esd/base-server");
        $this->uidAspect = new UidAspect();
        $aopConfig->addAspect($this->uidAspect);
    }

    /**
     * 初始化
     * @param Context $context
     * @return mixed
 * @throws \ESD\Core\Plugins\Config\ConfigException
     * @throws \Exception
     */
    public function beforeServerStart(Context $context)
    {
        $this->uidConfig->merge();
        $serverConfig = Server::$instance->getServerConfig();
        $this->uid = new UidBean($serverConfig->getMaxCoroutine(), $this->uidConfig);
        $this->setToDIContainer(UidBean::class, $this->uid);
    }

    /**
     * 在进程启动前
     * @param Context $context
     * @return mixed
     */
    public function beforeProcessStart(Context $context)
    {
        $this->ready();
    }

    /**
     * @return UidAspect
     */
    public function getUidAspect(): UidAspect
    {
        return $this->uidAspect;
    }

}