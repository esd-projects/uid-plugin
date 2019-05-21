<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/21
 * Time: 15:46
 */

namespace ESD\Plugins\Uid\Aspect;

use ESD\BaseServer\Memory\CrossProcess\Table;
use ESD\Plugins\Uid\UidConfig;
use Go\Aop\Aspect;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation\After;

class UidAspect implements Aspect
{
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
     * @param $fd
     * @param $uid
     */
    public function bindUid($fd, $uid)
    {
        $this->fdUidTable->set($fd, ["uid" => $uid]);
        $this->uidFdTable->set($uid, ["fd" => $fd]);
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
}