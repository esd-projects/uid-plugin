<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/21
 * Time: 16:03
 */

namespace ESD\Plugins\Uid;


use ESD\BaseServer\Memory\CrossProcess\Table;
use ESD\BaseServer\Server\Server;
use ESD\Plugins\Uid\Aspect\UidAspect;

trait GetUid
{
    protected $uidAspect;

    /**
     * @return UidAspect
     */
    protected function getUidAspect(): UidAspect
    {
        if ($this->uidAspect == null) {
            $uidPlugin = Server::$instance->getPlugManager()->getPlug(UidPlugin::class);
            if ($uidPlugin instanceof UidPlugin) {
                $this->uidAspect = $uidPlugin->getUidAspect();
            }
        }
        return $this->uidAspect;
    }

    public function kickUid($uid)
    {
        return $this->getUidAspect()->kickUid($uid);
    }

    public function bindUid($fd, $uid, $autoKick = true)
    {
        return $this->getUidAspect()->bindUid($fd, $uid, $autoKick);
    }

    public function unBindUid($fd)
    {
        return $this->getUidAspect()->unBindUid($fd);
    }

    public function getUidFd($uid)
    {
        return $this->getUidAspect()->getUidFd($uid);
    }

    public function getFdUid($fd)
    {
        return $this->getUidAspect()->getFdUid($fd);
    }

    public function isOnline($uid)
    {
        return $this->getUidAspect()->isOnline($uid);
    }

    public function getUidCount()
    {
        return $this->getUidAspect()->getUidCount();
    }

    public function getAllUid()
    {
        return $this->getUidAspect()->getAllUid();
    }

    /**
     * @return Table
     */
    public function getUidFdTable(): Table
    {
        return $this->getUidAspect()->getUidFdTable();
    }

    /**
     * @return Table
     */
    public function getFdUidTable(): Table
    {
        return $this->getUidAspect()->getFdUidTable();
    }
}