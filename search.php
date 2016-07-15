<?php

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    header('HTTP/1.0 405 Method Not Allowed', true, 405);
    die;
}



?>
