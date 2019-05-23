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

trait GetUid
{
    /**
     * @var UidBean
     */
    protected $uidBean;

    /**
     * @return UidBean
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function getUidBean(): UidBean
    {
        if ($this->uidBean == null) {
            $this->uidBean = Server::$instance->getContainer()->get(UidBean::class);
        }
        return $this->uidBean;
    }

    /**
     * @param $uid
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function kickUid($uid)
    {
        $this->getUidBean()->kickUid($uid);
    }

    /**
     * @param $fd
     * @param $uid
     * @param bool $autoKick
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function bindUid($fd, $uid, $autoKick = true)
    {
        $this->getUidBean()->bindUid($fd, $uid, $autoKick);
    }

    /**
     * @param $fd
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function unBindUid($fd)
    {
        $this->getUidBean()->unBindUid($fd);
    }

    /**
     * @param $uid
     * @return mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function getUidFd($uid)
    {
        return $this->getUidBean()->getUidFd($uid);
    }

    /**
     * @param $fd
     * @return mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function getFdUid($fd)
    {
        return $this->getUidBean()->getFdUid($fd);
    }

    /**
     * @param $uid
     * @return bool
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function isOnline($uid)
    {
        return $this->getUidBean()->isOnline($uid);
    }

    /**
     * @return int
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function getUidCount()
    {
        return $this->getUidBean()->getUidCount();
    }

    /**
     * @return array
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function getAllUid()
    {
        return $this->getUidBean()->getAllUid();
    }

    /**
     * @return Table
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function getUidFdTable(): Table
    {
        return $this->getUidBean()->getUidFdTable();
    }

    /**
     * @return Table
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function getFdUidTable(): Table
    {
        return $this->getUidBean()->getFdUidTable();
    }
}