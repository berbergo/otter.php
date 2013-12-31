<?php

/**
 * Copyright (c) 2013,�Ϻ�������������Ƽ��ɷ����޹�˾
 * �ļ����ƣ�Bootstrap.php
 * ժ    Ҫ��MVC��������ļ�
 * ��    �ߣ���С��
 * �޸����ڣ�2013.10.12
 */
include APPPATH . '/core/FlushData.php';
include APPPATH . '/core/Config.php';
include APPPATH . '/core/Common.php';
include APPPATH . '/core/Router.php';
include APPPATH . '/core/Controller.php';
include APPPATH . '/core/Action.php';
include APPPATH . '/core/Model.php';
include APPPATH . '/core/PdoEx.php';
include APPPATH . '/core/RedisEx.php';
Config::load();
$autoloadIncludes = Config::get('autoloadIncludes');
if ($autoloadIncludes)
{
    foreach ($autoloadIncludes as $includeFile)
    {
        includeFile($includeFile);
    }
}
FlushData::load();
Router::parseUrl();
$directory = Router::fetchDirectory();
$className = Router::fetchClass();
$methodName = Router::fetchMethod();
$params = Router::fetchParams();
$classFile = APPPATH . "/controllers/{$directory}{$className}.php";
if (!file_exists($classFile))
{
    show404();
}
else
{
    include $classFile;
    if (!in_array($methodName, array_map('strtolower', get_class_methods($className))) || in_array($methodName, array_map('strtolower', get_class_methods('Controller'))))
    {
        show404();
    }
    else
    {
        $reflectionMethod = new ReflectionMethod($className, $methodName);
        if (!$reflectionMethod->isPublic() || $reflectionMethod->isStatic())
        {
            show404();
        }
        else
        {
            $class = new $className();
            call_user_func_array(array(&$class, $methodName), $params);
        }
    }
}