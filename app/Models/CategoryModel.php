<?php 

    require_once 'Model.php';

    class CategoryModel extends Model{
        protected $table='peluqueros';

        public function AddCategory($nombre_apellido,$telefono,$edad,$turno,$especialidad) {
            $sentence= $this->db->prepare("INSERT INTO {$this->table}(nombre_apellido,telefono,edad,turno,especialidad) VALUES (?,?,?,?,?)");
            $sentence->execute([$nombre_apellido,$telefono,$edad,$turno,$especialidad]);
        
            return $this->db->lastInsertId();
        }

        public function EditCategory($valores,$id) {
            $datos= [];
            foreach ($valores as $valor => $data) {
                $datos[]= "$valor = :$valor";
            }

            $sentence= $this->db->prepare("UPDATE {$this->table} SET " . implode(', ', $datos) . " WHERE id_peluquero= :id_peluquero");
            $valores['id_peluquero']= $id;
            $sentence->execute($valores);
        }

        public function RemoveCategory($id) {
            $sentence= $this->db->prepare("DELETE FROM {$this->table} WHERE id_peluquero=?");
            $sentence->execute([$id]);
        }

        public function GetCategoryByID($id) {
            $sentence= $this->db->prepare("SELECT * FROM {$this->table} WHERE id_peluquero=?");
            $sentence->execute([$id]);
            $peluquero= $sentence->fetch(PDO::FETCH_OBJ);

            return $peluquero;
        }
    }