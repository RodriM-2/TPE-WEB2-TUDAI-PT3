<?php

    require_once 'Model.php';
    class ItemModel extends Model{

       protected $table='gatos';

        public function GetItem($id) {
            $sentence= $this->db->prepare("SELECT * FROM {$this->table} WHERE id_gato=?");
            $sentence->execute([$id]);
            $gato= $sentence->fetch(PDO::FETCH_OBJ);

            return $gato;
        }

        public function GetItemsByCategory($category) {
            $sentence= $this->db->prepare("SELECT * FROM {$this->table} WHERE id_peluquero=?");
            $sentence->execute([$category]);
            $gatos= $sentence->fetchAll(PDO::FETCH_OBJ);

            return $gatos;
        }

        public function AddItem($nombre,$edad,$raza,$color,$peso,$observaciones,$peluquero) {
            $sentence=$this->db->prepare("INSERT INTO {$this->table}(nombre,edad_meses,raza,color,peso_kg,observaciones,id_peluquero) VALUES (?,?,?,?,?,?,?)");
            $sentence->execute([$nombre,$edad,$raza,$color,$peso,$observaciones,$peluquero]);

            return $this->db->lastInsertId();
        }

        public function EditItem($valores, $id) {
            //Genero un array y lo voy llenando dinamicamente con los campos que pasaron el filtro y fueron
            //modificados
            $datos= [];
            foreach($valores as $valor => $data) {
                $datos[]= "$valor = :$valor";
            }

            //Con la misma base luego genero la consulta SQL de forma dinamica con el implode
            //Y asigno el ID a $valores antes de ejecutar la consulta
            $sentence= $this->db->prepare("UPDATE {$this->table} SET " . implode(', ', $datos) . " WHERE id_gato= :id_gato");
            $valores['id_gato']= $id;
            $sentence->execute($valores);
        }

        public function RemoveItem($id) {
            $sentence= $this->db->prepare("DELETE FROM {$this->table} WHERE id_gato=?");
            $sentence->execute([$id]);
        }
    }