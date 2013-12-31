<?php

class DefaultController extends Controller
{

    public function index()
    {
        loadView('index', array("message" => "hello world!"));
    }

}