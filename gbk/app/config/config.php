<?php

/**
 * Copyright (c) 2013,上海二三四五网络科技股份有限公司
 * 文件名称：Config.inc.php
 * 摘    要：MVC框架配置
 * 作    者：张小虎
 * 修改日期：2013.10.12
 */
include APPPATH . '/config/database.php';
include APPPATH . '/config/redis.php';
//自动加载include文件列表
$config['autoloadIncludes'] = array();
//返回配置
return $config;