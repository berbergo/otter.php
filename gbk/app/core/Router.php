<?php

/**
 * Copyright (c) 2013,上海二三四五网络科技股份有限公司
 * 文件名称：Router.php
 * 摘    要：路由操作类
 * 作    者：张小虎
 * 修改日期：2013.10.12
 */
class Router
{

    private static $directory = '', $class = 'default', $method = 'index', $params = array();

    public static function parseUrl()
    {
        if (isset($_SERVER['REQUEST_URI']) && isset($_SERVER['SCRIPT_NAME']))
        {
            $uri = $_SERVER['REQUEST_URI'];
            if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
            {
                $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
            }
            elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
            {
                $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
            }
            $parts = preg_split('#\?#i', $uri, 2);
            $uri = $parts[0];
            if ($uri && $uri != '/')
            {
                $uri = parse_url($uri, PHP_URL_PATH);
                $uri = str_replace(array('//', '../'), '/', trim($uri, '/'));
                $uri = removeInvisibleCharacters($uri);
                //兼容.php后缀
                $uri = preg_replace("|.php$|", "", $uri);
                //兼容.php后缀
                $moves = Config::get('moves');
                if ($moves)
                {
                    if (isset($moves[$uri]))
                    {
                        redirect($moves[$uri], 'location', '301');
                    }
                }
                $routes = Config::get('routes');
                if ($routes)
                {
                    if (isset($routes[$uri]))
                    {
                        $uri = $routes[$uri];
                    }
                    else
                    {
                        foreach ($routes as $key => $val)
                        {
                            if (preg_match('#^' . $key . '$#', $uri))
                            {
                                if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
                                {
                                    $uri = preg_replace('#^' . $key . '$#', $val, $uri);
                                }
                                break;
                            }
                        }
                    }
                }
                if ($uri)
                {
                    if (!preg_match("|^[" . str_replace(array('\\-', '\-'), '-', preg_quote('a-z 0-9~%.:_\-/', '-')) . "]+$|i", $uri))
                    {
                        showError(400, 400);
                    }
                    $bad = array('$', '(', ')', '%28', '%29');
                    $good = array('&#36;', '&#40;', '&#41;', '&#40;', '&#41;');
                    $uri = str_replace($bad, $good, $uri);
                    $segments = explode('/', preg_replace("|/*(.+?)/*$|", "\\1", $uri));
                    if (is_dir(APPPATH . '/controllers/' . $segments[0]))
                    {
                        self::$directory = $segments[0] . '/';
                        unset($segments[0]);
                        if (isset($segments[1]))
                        {
                            self::$class = preg_replace_callback(
                                    '#(\_(.))#', create_function(
                                            '$matches', 'return strtoupper($matches[2]);'
                                    ), $segments[1]
                            );
                            unset($segments[1]);
                            if (isset($segments[2]))
                            {
                                self::$method = $segments[2];
                                unset($segments[2]);
                            }
                        }
                    }
                    else
                    {
                        self::$class = preg_replace_callback(
                                '#(\_(.))#', create_function(
                                        '$matches', 'return strtoupper($matches[2]);'
                                ), $segments[0]
                        );
                        unset($segments[0]);
                        if (isset($segments[1]))
                        {
                            self::$method = $segments[1];
                            unset($segments[1]);
                        }
                    }
                    self::$params = array_values($segments);
                }
            }
        }
    }

    public static function fetchDirectory()
    {
        return self::$directory;
    }

    public static function fetchClass()
    {
        return ucfirst(self::$class) . 'Controller';
    }

    public static function fetchMethod()
    {
        return strtolower(self::$method);
    }

    public static function fetchParams()
    {
        return self::$params;
    }

}