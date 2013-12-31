<?php

/**
 * Copyright (c) 2013,上海二三四五网络科技股份有限公司
 * 文件名称：Config.php
 * 摘    要：配置文件操作类
 * 作    者：张小虎
 * 修改日期：2013.10.12
 */
class Config
{

    private static $config;

    public static function load()
    {
        self::$config = include APPPATH . '/config/config.php';
    }

    public static function get($key)
    {
        if (isset(self::$config[$key]))
        {
            return self::$config[$key];
        }
        else
        {
            return false;
        }
    }

}