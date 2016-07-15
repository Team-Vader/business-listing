<?php

if ($_SERVER["REQUEST_METHOD"] != 'GET') {
    header('HTTP/1.0 405 Method Not Allowed', true, 405);
    die;
}
require_once "class.Mongo.php";

$mongo = new Mongo();
$mongo->setCollection("floating");

$name = (isset($_GET["name"]) && $_GET['name']) ? $_GET['name'] : null;
$city = (isset($_GET["city"]) && $_GET['city']) ? $_GET['city'] : null;
$case_sensitive = (isset($_GET["i"]) && $_GET["i"] == 1) ? true : false;

if ($name) {
    $query_name = array ( '$regex' => "^$name" );
    if (!$case_sensitive) {
        $query_name['$options'] = "i";
    }
    $search_query = array( "Business Name" => $query_name, "City" => $city);
} else {
    $search_query = array();
}
$results = $mongo->find($search_query, 10);

echo json_encode($results);
?> 
