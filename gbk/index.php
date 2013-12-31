<?php

/**
 * Copyright (c) 2013,上海瑞创网络科技股份有限公司
 * 文件名称：index.php
 * 摘    要：MVC框架入口文件
 * 作    者：张小虎
 * 修改日期：2013.11.07
 */
define('BASEPATH', __DIR__);
define('APPPATH', realpath(BASEPATH . '/app'));
include APPPATH . '/Bootstrap.php';