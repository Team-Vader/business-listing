<?php
error_reporting(E_WARNING ^ E_ALL);
header('Content-type: application/json');
if ($_SERVER["REQUEST_METHOD"] != 'GET') {
    header('HTTP/1.0 405 Method Not Allowed', true, 405);
    die;
}


require_once "class.Mongo.php";

$mongo = new Businesses();

$name = (isset($_GET["name"]) && $_GET['name']) ? $_GET['name'] : null;
$city = (isset($_GET["city"]) && $_GET['city']) ? $_GET['city'] : null;
$case_sensitive = (isset($_GET["i"]) && $_GET["i"] == 1) ? true : false;

if ($name) {
    $query_name = array ( '$regex' => "^$name" );
    if (!$case_sensitive) {
        $query_name['$options'] = "i";
    }
    $search_query = array( "Name" => $query_name, "City" => $city);
} else {
    $search_query = array();
}

if (isset($_GET['tag'])) {
    $tag = $_GET['tag'];
    $search_query = array( "Tag" => $tag );
}

$results = $mongo->find($search_query, 10);
echo json_encode($results);
?> 
