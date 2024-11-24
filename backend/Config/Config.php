<?php

class Config
{
    public $host = 'mysql:host=sql;port=3306;dbname=docker';
    public $user = 'root';
    public $pass = 'root';
    public $options = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"];
}
