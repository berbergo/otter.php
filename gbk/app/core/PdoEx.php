<?php

/**
 * Copyright (c) 2013,上海二三四五网络科技股份有限公司
 * 文件名称：PdoEx.php
 * 摘    要：Pdo读写分离扩展类
 * 作    者：张小虎
 * 修改日期：2013.10.12
 */
class PdoEx
{

    private static $instances = array();
    private $config, $dbW, $dbR;

    private function __construct($dbName)
    {
        $dbConfig = Config::get('db');
        $this->config = $dbConfig[$dbName];
    }

    public static function getInstance($dbName)
    {
        if (!isset(self::$instances[$dbName]))
        {
            self::$instances[$dbName] = new static($dbName);
        }
        return self::$instances[$dbName];
    }

    public function getWritableDB()
    {
        if (!$this->dbW)
        {
            $dsn = 'mysql:host=' . $this->config['default']['hostname'] . ';dbname=' . $this->config['default']['database'] . ';charset=' . $this->config['default']['char_set'];
            $username = $this->config['default']['username'];
            $password = $this->config['default']['password'];
            $options = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $this->config['default']['char_set'],
            );
            $this->dbW = new PDO($dsn, $username, $password, $options);
        }
        return $this->dbW;
    }

    public function getReadableDB()
    {
        if (!isset($this->config['slave']) || ($this->config['default']['hostname'] == $this->config['slave']['hostname']))
        {
            return $this->getWritableDB();
        }
        else
        {
            if (!$this->dbR)
            {
                $dsn = 'mysql:host=' . $this->config['slave']['hostname'] . ';dbname=' . $this->config['slave']['database'] . ';charset=' . $this->config['slave']['char_set'];
                $username = $this->config['slave']['username'];
                $password = $this->config['slave']['password'];
                $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $this->config['slave']['char_set'],
                );
                $this->dbR = new PDO($dsn, $username, $password, $options);
            }
            return $this->dbR;
        }
    }

    public function insert($table, $data)
    {
        $db = $this->getWritableDB();
        $columns = array_keys($data);
        $stmt = $db->prepare("INSERT INTO $table (`" . implode("`, `", $columns) . "`) VALUES (:" . implode(", :", $columns) . ")");
        foreach ($data as $column => $param)
        {
            $stmt->bindValue(":$column", $param);
        }
        return $stmt->execute();
    }

    public function update($table, $data, $condition)
    {
        $db = $this->getWritableDB();
        $columns = array_keys($data);
        foreach ($columns as $key => $column)
        {
            $columns[$key] = "`$column` = :$column";
        }
        $stmt = $db->prepare("UPDATE $table SET " . implode(',', $columns) . " WHERE {$condition['where']}");
        foreach ($data as $column => $param)
        {
            $stmt->bindValue(":$column", $param);
        }
        foreach ($condition['params'] as $column => $param)
        {
            $stmt->bindValue($column, $param);
        }
        return $stmt->execute();
    }

    public function delete($table, $condition)
    {
        $db = $this->getWritableDB();
        $stmt = $db->prepare("DELETE FROM $table WHERE {$condition['where']}");
        foreach ($condition['params'] as $column => $param)
        {
            $stmt->bindValue($column, $param);
        }
        return $stmt->execute();
    }

    public function find($sql, $params = array(), $useWritableDB = false)
    {
        if ($useWritableDB)
        {
            $db = $this->getWritableDB();
        }
        else
        {
            $db = $this->getReadableDB();
        }
        $stmt = $db->prepare($sql);
        foreach ($params as $column => $param)
        {
            $stmt->bindValue($column, $param);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll($sql, $params = array(), $useWritableDB = false)
    {
        if ($useWritableDB)
        {
            $db = $this->getWritableDB();
        }
        else
        {
            $db = $this->getReadableDB();
        }
        $stmt = $db->prepare($sql);
        foreach ($params as $column => $param)
        {
            $stmt->bindValue($column, $param);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lastInsertId()
    {
        $db = $this->getWritableDB();
        return $db->lastInsertId();
    }

    public function beginTransaction()
    {
        $db = $this->getWritableDB();
        $db->beginTransaction();
    }

    public function rollBack()
    {
        $db = $this->getWritableDB();
        $db->rollBack();
    }

    public function commit()
    {
        $db = $this->getWritableDB();
        $db->commit();
    }

}