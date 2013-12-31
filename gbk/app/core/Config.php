<?php

/**
 * Copyright (c) 2013,�Ϻ�������������Ƽ��ɷ����޹�˾
 * �ļ����ƣ�Config.php
 * ժ    Ҫ�������ļ�������
 * ��    �ߣ���С��
 * �޸����ڣ�2013.10.12
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