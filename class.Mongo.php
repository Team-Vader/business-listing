<?php

include 'vendor/autoload.php';
use MongoDB\Driver\Manager;

class Mongo {

    private $mongo;
    private $db;
    private $collection;

    function __construct() {
        $mongo_conf = parse_ini_file("conf/mongo.ini");

        $host = $mongo_conf['host'];
        $port = $mongo_conf['port'];
        $this->db = $mongo_conf['db'];
        $this->mongo = new MongoDB\Client("mongodb://$host:27017");
    }

    function setCollection($collection) {
        $db = $this->db;
        $this->collection = $this->mongo->test_main->$collection; //test_main->floating;
    }

    function search($query, $limit = 10, $case_sensitive = false) {
        $q = array();
        foreach ($query as $k => $v) {
            $regex = $case_sensitive ? "/^$v/" : "/^$v/i";
            $q[$k] = array( '$regex' => $regex );
        }
var_rump($q);
        return $this->find( $q, $limit );
    }

    function find($query = array(), $limit = 10) {
        $result = $this->collection->find($query, ['limit' => $limit]);
        $result_list = array();
        foreach ($result as $r) {
            $result_list[] = array(
                'id' => $r->_id->__toString(),
                'name' => $r->{'Business Name'},
                'category' => $r->Category,
                'city' => $r->City
            );
        }
        return $result_list;
    }
}
