<?php

require_once './Lib/UserRep.php';

$users = UserRep::getAll();

echo json_encode($users);