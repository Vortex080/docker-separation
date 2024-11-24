<?php

$dr = $_SERVER['DOCUMENT_ROOT'];
require_once $dr . '/Config/Config.php';

class Connection
{
    // Objeto connection para conectarse a base de datos
    public static $con = null;
    public static $config = null;


    /**
     * Crea la conexión con la base de datos
     * introduciendo los campos solicitados por el PDO
     */
    public static function getConection()
    {
        if (self::$con == null) {
            self::$config = new Config();
            try {
                self::$con = new PDO(self::$config->host, self::$config->user, self::$config->pass, self::$config->options);
            } catch (PDOException $e) {
                error_log($e->getMessage());
                exit('Esta mal :' . $e->getMessage());
            }
        }

        return self::$con;
    }
}
