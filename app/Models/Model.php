<?php

require_once 'DBDeploy.php';

//Tanto Items como Category heredan de la clase Model ya que comparten el elemento en comun de traer todos
//A su vez, Model utiliza DBDeploy.php y por ende la clase ModelDB para construir el PDO y el deploy
abstract class Model {

    protected $db;
    protected $table;

    public function __construct() {
        $this->db= new ModelDB();
    }

    
    public function GetElements($param, $order) {
        
        $sentence= $this->db->prepare("SELECT * FROM {$this->table} ORDER BY {$param} {$order}");
        $sentence->execute([]);
        $items= $sentence->fetchAll(PDO::FETCH_OBJ);

        return $items;
    }
}