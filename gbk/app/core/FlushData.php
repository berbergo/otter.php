<?php

/**
 * Copyright (c) 2013,�Ϻ�������������Ƽ��ɷ����޹�˾
 * �ļ����ƣ�FlushData.php
 * ժ    Ҫ��������Ϣ��
 * ��    �ߣ���С��
 * �޸����ڣ�2013.10.12
 */
class FlushData
{

    private static $data = array();

    public static function load()
    {
        foreach ($_COOKIE as $key => $value)
        {
            if (strpos($key, 'flush:') === 0)
            {
                self::$data[str_replace('flush:', '', $key)] = $value;
                unset($_COOKIE[$key]);
                setcookie($key, "", time() - 3600, "/");
            }
        }
    }

    public static function get($key)
    {
        return self::$data[$key];
    }

    public static function set($key, $value)
    {
        setcookie("flush:$key", $value, 0, "/");
    }

}
