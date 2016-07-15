<?php

require_once 'class.Mongo.php';

$mongo = new Mongo();
$mongo->setCollection("floating");

$name = $_GET['name'];
$city = $_GET['city'];
$case_sensitive = (isset($_GET['i']) && $_GET['i'] == 1) ? true : false;

$query_name = array ( '$regex' => "^$name" );
if (!$case_sensitive) {
    $query_name['$options'] = 'i';
}
$search_query = array( 'Business Name' => $query_name, 'City' => $city);
$results = $mongo->find($search_query, 10);

echo json_encode($results);
?> 
