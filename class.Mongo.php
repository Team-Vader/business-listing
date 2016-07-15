<?php

include 'vendor/autoload.php';
use MongoDB\Driver\Manager;

class Businesses {

    private $mongo;
    private $db;
    private $collection;

    function __construct() {
        $mongo_conf = parse_ini_file("conf/mongo.ini");

        $host = $mongo_conf['host'];
        $port = $mongo_conf['port'];
        $this->db = $mongo_conf['db'];
        $this->mongo = new MongoDB\Client("mongodb://$host:27017");
        $this->floating = $this->mongo->floatDB->$mongo_conf['fps']; //test_main->floating;
        $this->rnr = $this->mongo->floatDB->$mongo_conf['rnr'];
        $this->places_url = $mongo_conf['places-url'];
    }

    function search($query, $limit = 10, $case_sensitive = false) {
        $q = array();
        foreach ($query as $k => $v) {
            $regex = $case_sensitive ? "/^$v/" : "/^$v/i";
            $q[$k] = array( '$regex' => $regex );
        }
        return $this->find( $q, $limit );
    }

    function find($query = array(), $limit = 10) {
        $result = iterator_to_array( $this->floating->find($query, ['limit' => $limit]) );
        foreach ($result as $i => $r) {
            //$rnr = $this->getRnR($r->_id, $r->{'Business Name'}, $r->{'Address'});
            #$result_list[] = array(
            #    'id' => $r->_id->__toString(),
            #    'name' => $r->{'Business Name'},
            #    'category' => $r->Category,
            #    'city' => $r->City
            #);
            #$result[$i]->{'id'} = $r->_id;
            #$result[$i]->{'name'} = $r->{'Name'};
            #$result[$i]->{'category'} = $r->{'Category'};
            #$result[$i]->{'city'} = $r->City;
            if ( !isset($r->Name) || !isset($r->Address) ) {
                unset($result[$i]);
                continue;
            }
            $rnr = $this->getRnr($r->_id, $r->{'Name'}, $r->Address, $r->lat, $r->lng);
            if ($rnr == null) continue;
            $result[$i]->{'rating'} = isset($rnr->rating) ? $rnr->rating : null;
            $result[$i]->{'reviews'} = isset($rnr->reviews) ? $rnr->reviews : null;
        }
        return $result;
    }

    function getRnr($id, $name, $address, $lat, $lng) {
        $rnr = iterator_to_array( $this->rnr->find( array( 'bid' => $id ) ) );
        if (count($rnr[0]) > 0) {
            if ( !isset($rnr[0]->rating)) return null;
            return $rnr[0];
        }

        $rnr = json_decode(self::google_places($name, $address, $lat, $lng));
        $rnr->{'bid'} = $id;
        $this->rnr->insertOne( $rnr );

        if ( isset($rnr->rating) && $rnr->rating == null) return null;
        return $rnr;
    }    

    function google_places($name, $address, $lat, $lng) {
        $address_parts = explode(",", $address);
        array_shift($address_parts);
        $address = implode(",", $address_parts);
        $name = urlencode($name);
        $address = urlencode($address);
        $loc = "$lat,$lng";
        $url = "{$this->places_url}?place=$address&q=$name&location=$loc";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}
