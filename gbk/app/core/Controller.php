<?php

/**
 * Copyright (c) 2013,�Ϻ�������������Ƽ��ɷ����޹�˾
 * �ļ����ƣ�Controller.php
 * ժ    Ҫ��Controller����
 * ��    �ߣ���С��
 * �޸����ڣ�2013.10.12
 */
class Controller
{

    private static $instance;

    public function __construct()
    {
        self::$instance = $this;
    }

    public static function getInstance()
    {
        return self::$instance;
    }

}
