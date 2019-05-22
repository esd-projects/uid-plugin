<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/21
 * Time: 16:03
 */

namespace ESD\Plugins\Uid;


use ESD\BaseServer\Server\Server;

trait GetUid
{
    public function kickUid($uid)
    {
        $uidPlugin = Server::$instance->getPlugManager()->getPlug(UidPlugin::class);
        if ($uidPlugin instanceof UidPlugin) {
            $uidPlugin->getUidAspect()->kickUid($uid);
        }
    }

    public function bindUid($fd, $uid, $autoKick = true)
    {
        $uidPlugin = Server::$instance->getPlugManager()->getPlug(UidPlugin::class);
        if ($uidPlugin instanceof UidPlugin) {
            $uidPlugin->getUidAspect()->bindUid($fd, $uid, $autoKick);
        }
    }

    public function unBindUid($fd)
    {
        $uidPlugin = Server::$instance->getPlugManager()->getPlug(UidPlugin::class);
        if ($uidPlugin instanceof UidPlugin) {
            $uidPlugin->getUidAspect()->unBindUid($fd);
        }
    }

    public function getUidFd($uid)
    {
        $uidPlugin = Server::$instance->getPlugManager()->getPlug(UidPlugin::class);
        if ($uidPlugin instanceof UidPlugin) {
            return $uidPlugin->getUidAspect()->getUidFd($uid);
        }
        return null;
    }

    public function getFdUid($fd)
    {
        $uidPlugin = Server::$instance->getPlugManager()->getPlug(UidPlugin::class);
        if ($uidPlugin instanceof UidPlugin) {
            return $uidPlugin->getUidAspect()->getFdUid($fd);
        }
        return null;
    }
}