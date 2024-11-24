<?php

$dr = $_SERVER['DOCUMENT_ROOT'];
require_once $dr . '/Lib/UserRep.php';

header('Access-Control-Allow-Origin: http://localhost:8000');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$requesmethod = $_SERVER['REQUEST_METHOD'];

switch ($requesmethod) {
    case 'GET':
        $id = $_GET['id'];
        if ($id == 'All') {
            $user = UserRep::getAll();
            echo json_encode($user);
        }
        break;
    default:
        echo json_encode(['error' => 'Error']);
        break;
}
