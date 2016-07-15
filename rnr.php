<?php
error_reporting(E_WARNING ^ E_ALL);
header('Content-type: application/json');
if ($_SERVER["REQUEST_METHOD"] != 'GET') {
    header('HTTP/1.0 405 Method Not Allowed', true, 405);
    die;
}
require_once "class.Mongo.php";

$mongo = new Businesses();
$tag = $_GET['tag'];
$results = $mongo->getRnr($tag);
echo json_encode($results);
?> 
