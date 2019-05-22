<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/21
 * Time: 15:46
 */

namespace ESD\Plugins\Uid\Aspect;

use ESD\BaseServer\Memory\CrossProcess\Table;
use ESD\BaseServer\Plugins\Logger\GetLogger;
use ESD\BaseServer\Server\Server;
use ESD\Plugins\Aop\OrderAspect;
use ESD\Plugins\Uid\UidConfig;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation\After;

class UidAspect extends OrderAspect
{
    use GetLogger;
    /**
     * @var Table
     */
    protected $uidFdTable;
    /**
     * @var Table
     */
    protected $fdUidTable;
    /**
     * @var UidConfig
     */
    protected $uidConfig;

    /**
     * @param int $getMaxCoroutine
     * @param UidConfig $uidConfig
     */
    public function createTable(int $getMaxCoroutine, UidConfig $uidConfig)
    {
        $this->uidConfig = $uidConfig;
        $this->uidFdTable = new Table($getMaxCoroutine);
        $this->uidFdTable->column("fd", Table::TYPE_INT);
        $this->uidFdTable->create();
        $this->fdUidTable = new Table($getMaxCoroutine);
        $this->fdUidTable->column("uid", Table::TYPE_STRING, $uidConfig->getUidMaxLength());
        $this->fdUidTable->create();
    }

    /**
     * @return Table
     */
    public function getUidFdTable(): Table
    {
        return $this->uidFdTable;
    }

    /**
     * @return Table
     */
    public function getFdUidTable(): Table
    {
        return $this->fdUidTable;
    }

    /**
     * @param $uid
     */
    public function kickUid($uid)
    {
        $fd = $this->getUidFd($uid);
        if ($fd != null) {
            $this->unBindUid($fd);
            Server::$instance->closeFd($fd);
        }
        $this->debug("Kick uid: $uid");
    }

    /**
     * @param $fd
     * @param $uid
     * @param bool $autoKick
     */
    public function bindUid($fd, $uid, $autoKick = true)
    {
        if ($autoKick) {
            $this->kickUid($uid);
        }
        $this->fdUidTable->set($fd, ["uid" => $uid]);
        $this->uidFdTable->set($uid, ["fd" => $fd]);
        $this->debug("$fd Bind uid: $uid");
    }

    /**
     * @param $fd
     */
    public function unBindUid($fd)
    {
        $uid = $this->fdUidTable->get($fd, "uid");
        $this->fdUidTable->del($fd);
        if ($uid != null) {
            $this->uidFdTable->del($uid);
            $this->debug("$fd UnBind uid: $uid");
        }
    }

    /**
     * @param $uid
     * @return mixed
     */
    public function getUidFd($uid)
    {
        return $this->uidFdTable->get($uid, "fd");
    }

    /**
     * @param $fd
     * @return mixed
     */
    public function getFdUid($fd)
    {
        return $this->fdUidTable->get($fd, "uid");
    }

    public function isOnline($uid)
    {
        $fd = $this->getUidFd($uid);
        if ($fd != null) return true;
        return false;
    }

    public function getUidCount()
    {
        return $this->fdUidTable->count();
    }

    public function getAllUid()
    {
        $result = [];
        foreach ($this->uidFdTable as $key => $value) {
            $result[] = $key;
        }
        return $result;
    }

    /**
     * around onTcpReceive
     *
     * @param MethodInvocation $invocation Invocation
     * @throws \Throwable
     * @After("within(ESD\BaseServer\Server\IServerPort+) && execution(public **->onTcpClose(*))")
     */
    protected function afterTcpClose(MethodInvocation $invocation)
    {
        list($fd, $reactorId) = $invocation->getArguments();
        $this->unBindUid($fd);
    }

    /**
     * around onTcpReceive
     *
     * @param MethodInvocation $invocation Invocation
     * @throws \Throwable
     * @After("within(ESD\BaseServer\Server\IServerPort+) && execution(public **->onWsClose(*))")
     */
    protected function afterWsClose(MethodInvocation $invocation)
    {
        list($fd, $reactorId) = $invocation->getArguments();
        $this->unBindUid($fd);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return "UidAspect";
    }
}