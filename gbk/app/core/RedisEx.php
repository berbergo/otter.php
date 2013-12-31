<?php

/**
 * Copyright (c) 2013,上海二三四五网络科技股份有限公司
 * 文件名称：RedisEx.php
 * 摘    要：redis扩展（读写分离）
 * 作    者：张小虎
 * 修改日期：2013.09.04
 */
class RedisEx
{

    private static $instance;
    private $config, $writableMethods, $readableMethods, $redisW, $redisR;

    private function __construct()
    {
        $this->config = Config::get('redis');
        $this->writableMethods = array(
            'append' => 1,
            'bitcount' => 1,
            'bitop' => 1,
            'decr' => 1,
            'decrBy' => 1,
            'getSet' => 1,
            'incr' => 1,
            'incrBy' => 1,
            'incrByFloat' => 1,
            'mSet' => 1,
            'mSetNX' => 1,
            'set' => 1,
            'setBit' => 1,
            'setex' => 1,
            'psetex' => 1,
            'setnx' => 1,
            'setRange' => 1,
            'del' => 1,
            'delete' => 1,
            'expire' => 1,
            'setTimeout' => 1,
            'pexpire' => 1,
            'expireAt' => 1,
            'pexpireAt' => 1,
            'migrate' => 1,
            'move' => 1,
            'persist' => 1,
            'rename' => 1,
            'renameKey' => 1,
            'renameNx' => 1,
            'sort' => 1,
            'ttl' => 1,
            'pttl' => 1,
            'restore' => 1,
            'hDel' => 1,
            'hIncrBy' => 1,
            'hIncrByFloat' => 1,
            'hMSet' => 1,
            'hSet' => 1,
            'hSetNx' => 1,
            'blPop' => 1,
            'brPop' => 1,
            'brpoplpush' => 1,
            'lInsert' => 1,
            'lPop' => 1,
            'lPush' => 1,
            'lPushx' => 1,
            'lRem' => 1,
            'lRemove' => 1,
            'lSet' => 1,
            'lTrim' => 1,
            'listTrim' => 1,
            'rPop' => 1,
            'rpoplpush' => 1,
            'rPush' => 1,
            'rPushx' => 1,
            'sAdd' => 1,
            'sDiffStore' => 1,
            'sInterStore' => 1,
            'sMove' => 1,
            'sPop' => 1,
            'sRem' => 1,
            'sRemove' => 1,
            'sUnionStore' => 1,
            'zAdd' => 1,
            'zIncrBy' => 1,
            'zInter' => 1,
            'zRem' => 1,
            'zDelete' => 1,
            'zRemRangeByRank' => 1,
            'zDeleteRangeByRank' => 1,
            'zRemRangeByScore' => 1,
            'zDeleteRangeByScore' => 1,
            'zUnion' => 1,
        );
        $this->readableMethods = array(
            'get' => 1,
            'getBit' => 1,
            'getRange' => 1,
            'mGet' => 1,
            'getMultiple' => 1,
            'strlen' => 1,
            'dump' => 1,
            'exists' => 1,
            'keys' => 1,
            'getKeys' => 1,
            'object' => 1,
            'randomKey' => 1,
            'type' => 1,
            'hExists' => 1,
            'hGet' => 1,
            'hGetAll' => 1,
            'hKeys' => 1,
            'hLen' => 1,
            'hMGet' => 1,
            'hVals' => 1,
            'lIndex' => 1,
            'lGet' => 1,
            'lLen' => 1,
            'lSize' => 1,
            'lRange' => 1,
            'lGetRange' => 1,
            'sCard' => 1,
            'sSize' => 1,
            'sDiff' => 1,
            'sInter' => 1,
            'sIsMember' => 1,
            'sContains' => 1,
            'sMembers' => 1,
            'sGetMembers' => 1,
            'sRandMember' => 1,
            'sUnion' => 1,
            'zCard' => 1,
            'zSize' => 1,
            'zCount' => 1,
            'zRange' => 1,
            'zRangeByScore' => 1,
            'zRevRangeByScore' => 1,
            'zRank' => 1,
            'zRevRank' => 1,
            'zRevRange' => 1,
            'zScore' => 1,
        );
    }

    public static function getInstance()
    {
        if (!self::$instance)
        {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function getWritableRedis()
    {
        if (!$this->redisW)
        {
            $this->redisW = new Redis();
            $this->redisW->connect($this->config['default']['host'], $this->config['default']['port'], 3);
            $this->redisW->auth($this->config['default']['auth']);
        }
        return $this->redisW;
    }

    public function getReadableRedis()
    {
        if (!isset($this->config['slave']) || ($this->config['default']['host'] == $this->config['slave']['host']))
        {
            return $this->getWritableRedis();
        }
        else
        {
            if (!$this->redisR)
            {
                $this->redisR = new Redis();
                $this->redisR->connect($this->config['slave']['host'], $this->config['slave']['port'], 3);
                $this->redisR->auth($this->config['slave']['auth']);
            }
            return $this->redisR;
        }
    }

    public function __call($name, $arguments)
    {
        if (isset($this->writableMethods[$name]))
        {
            $redis = $this->getWritableRedis();
        }
        else if (isset($this->readableMethods[$name]))
        {
            $redis = $this->getReadableRedis();
        }
        else
        {
            return false;
        }
        return call_user_func_array(array(&$redis, $name), $arguments);
    }

    public function __destruct()
    {
        if ($this->redisR)
        {
            $this->redisR->close();
        }
        if ($this->redisW)
        {
            $this->redisW->close();
        }
    }

}