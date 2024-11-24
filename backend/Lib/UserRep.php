<?php
$dr = $_SERVER['DOCUMENT_ROOT'];
require_once $dr . '/Lib/Connection.php';
require_once $dr . '/Models/User.php';

class UserRep
{
    /**
     * getAll
     *
     * @return array
     */
    public static function getAll()
    {
        $con = Connection::getConection();
        $sql = "SELECT * FROM usuarios";
        $result = $con->query($sql);
        $users = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($row['nombre'], $row['apellidos'], $row['edad']);
        }
        return $users;
    }
}
