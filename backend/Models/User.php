<?php

class User
{
    public $nombre;
    public $apellidos;
    public $edad;

    public function __construct($nombre, $apellidos, $edad)
    {
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->edad = $edad;
    }
}
