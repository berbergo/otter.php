<?php

/**
 * Copyright (c) 2013,上海二三四五网络科技股份有限公司
 * 文件名称：Common.php
 * 摘    要：公用函数库
 * 作    者：张小虎
 * 修改日期：2013.10.12
 */
function showError($code, $statusCode = 500, $desc = '')
{
    header("HTTP/1.1 $statusCode");
    loadView('error.tpl.html', array('error' => $code, 'desc' => $desc));
    exit;
}

function show404()
{
    header("HTTP/1.1 404");
    loadView('error.tpl.html', array('error' => 404));
    exit;
}

function includeFile($includeFile)
{
    include APPPATH . '/includes/' . $includeFile;
}

function includeBase($baseClass)
{
    include APPPATH . '/base/' . $baseClass . '.php';
}

function loadClass($classFile, $className)
{
    static $classes = array();
    if (!isset($classes[$className]))
    {
        if (file_exists($classFile))
        {
            include $classFile;
            $classes[$className] = new $className();
        }
        else
        {
            showError(500);
        }
    }
    return $classes[$className];
}

function loadAction($actionName)
{
    $actionName = ucfirst($actionName) . 'Action';
    $actionFile = APPPATH . "/actions/$actionName.php";
    return loadClass($actionFile, $actionName);
}

function loadModel($modelName)
{
    $modelName = ucfirst($modelName) . 'Model';
    $modelFile = APPPATH . "/models/$modelName.php";
    return loadClass($modelFile, $modelName);
}

function loadVendor($className)
{
    $className = ucfirst($className);
    $classFile = APPPATH . "/vendors/$className.php";
    return loadClass($classFile, $className);
}

function loadView($tpl, $pageArray = array(), $return = false)
{
    $ob_level = ob_get_level();
    ob_start();
    include(APPPATH . "/views/$tpl.php");
    if ($return)
    {
        $buffer = ob_get_contents();
        @ob_end_clean();
        return $buffer;
    }
    if (ob_get_level() > $ob_level + 1)
    {
        ob_end_flush();
    }
    else
    {
        $buffer = ob_get_contents();
        @ob_end_clean();
    }
    echo $buffer;
}

function removeInvisibleCharacters($str, $urlEncoded = TRUE)
{
    $nonDisplayables = array();
    // every control character except newline (dec 10)
    // carriage return (dec 13), and horizontal tab (dec 09)
    if ($urlEncoded)
    {
        $nonDisplayables[] = '/%0[0-8bcef]/'; // url encoded 00-08, 11, 12, 14, 15
        $nonDisplayables[] = '/%1[0-9a-f]/'; // url encoded 16-31
    }
    $nonDisplayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127
    do
    {
        $str = preg_replace($nonDisplayables, '', $str, -1, $count);
    }
    while ($count);
    return $str;
}

function redirect($uri = '', $method = 'location', $http_response_code = 302)
{
    switch ($method)
    {
        case 'refresh' : header("Refresh:0;url=" . $uri);
            break;
        default : header("Location: " . $uri, TRUE, $http_response_code);
            break;
    }
    exit;
}

function closeWindow()
{
    die('<script type="text/javascript">window.opener=null;window.open("", "_self", "");window.close();</script>');
}