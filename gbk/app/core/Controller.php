<?php

/**
 * Copyright (c) 2013,上海二三四五网络科技股份有限公司
 * 文件名称：Controller.php
 * 摘    要：Controller基类
 * 作    者：张小虎
 * 修改日期：2013.10.12
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
