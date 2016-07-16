<?php
error_reporting(E_WARNING ^ E_ALL);
header('Content-type: application/json');
if ($_SERVER["REQUEST_METHOD"] != 'GET') {
    header('HTTP/1.0 405 Method Not Allowed', true, 405);
    die;
}


require_once "class.Mongo.php";

$mongo = new Businesses();

$query = (isset($_GET["name"]) && $_GET['name']) ? $_GET['name'] : null;
$city = (isset($_GET["city"]) && $_GET['city']) ? $_GET['city'] : null;
$case_sensitive = (isset($_GET["i"]) && $_GET["i"] == 1) ? true : false;

if ($query) {
    $query_name = array ( '$regex' => "^$query" );
    if (!$case_sensitive) {
        $query_name['$options'] = "i";
    }
    $query_cat = array ( '$regex' => $query, '$options' => "i" );
    $search_query = array ( '$or' => array ( array ( 'Category' => $query_cat ), array( "Name" => $query_name )) );
    if ($city) {
        $search_query = array ( '$and' => array ($search_query, array ( 'City' => $city ) ) );
    }
} else {
    $search_query = array();
}

if (isset($_GET['tag'])) {
    $tag = $_GET['tag'];
    $search_query = array( "Tag" => $tag );
}

$results = $mongo->find($search_query, 20);
echo json_encode($results);
?> 
