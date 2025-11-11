<?php
    //Controlador de peluqueros / Entidades 1 de la relacion

    require_once __DIR__ . '/../Models/CategoryModel.php';
    //Me traigo el Model de Items para que no me asignen cualquier cosa en los edit o add
    require_once __DIR__ . '/../Models/ItemModel.php';

    class CategoryController {
        private $model;
        private $FKmodel;
        private $view;

        public function __construct() {
            $this->model= new CategoryModel();
            $this->FKmodel= new ItemModel();
        }

        public function GetCategorias($req, $res) {
            $param='id_peluquero';
            $order='ASC';

            if (isset($_GET['param'])) {
                $param= $_GET['param'];
                $param= strtolower($param);

                //Si no recibo un parametro valido hago que ordene simplemente por los ID de los peluqueros
                //el query &param de Category acepta solamente EDAD
                //No es case sensitive
                if ($param != 'edad') {
                        $param= 'id_peluquero';
                }
            }

            if (isset($_GET['order'])) {
                $order= $_GET['order'];
                $order= strtoupper($order);

                //En caso de que el user no mande un orden valido por predeterminado lo mando por ASC
                if (!($order == 'ASC') && !($order == 'DESC')) {
                    $order= 'ASC';
                }
            }

            $categorias= $this->model->GetElements($param, $order);
            return $res->json($categorias);
        }

        //Traer un peluquero / categoria a la vez
        public function GetCategory($req, $res) {
            $id= $req->params->id;
            $categoria= $this->model->GetCategoryByID($id);

            if (!$categoria) {
                return $res->json("No existe un peluquero con la ID {$id}", 404);
            }

            return $res->json($categoria);
        }

        //Agregar un peluquero / categoria
        public function AddCategory($req, $res) {
            //Muy idem al AddItem de ItemController, nomas cambiamos de lugar algunos campos!
            $campos= ['nombre_apellido', 'telefono', 'edad', 'especialidad'];
            foreach ($campos as $campo) {
                if (empty($req->body->$campo)) {
                    return $res->json("Falta completar el campo '{$campo}'", 400);
                }
            }

            $nombre= $req->body->nombre_apellido;
            $telefono= $req->body->telefono;
            $edad= $req->body->edad;
            $especialidad= $req->body->especialidad;

            //Por cuestion de visual agrego esto en caso de que el user no mande el campo opcional
            if (empty($req->body->turno)) {
                $turno= 'No definido';
            } else {
                $turno= $req->body->turno;
            }

            //Como es el lado 1 de la relacion no tengo que preocuparme por chequear FK de los Items (Gatos)
            $CategoryID= $this->model->AddCategory($nombre, $telefono, $edad, $turno, $especialidad);

            if (!$CategoryID) {
                return $res->json("Error del servidor al insertar datos", 500);
            }

            $Category= $this->model->GetCategoryByID($CategoryID);
            return $res->json($Category, 201); 
        }

        public function DeleteCategoria($req, $res) {
            $id= $req->params->id;
            $categoria= $this->model->GetCategoryByID($id);

           if (!$categoria) {
                return $res->json("No hay un peluquero registrado con el ID {$id}", 404);
            }

            $check= $this->FKmodel->GetItemsByCategory($id);
            if ($check) {
                return $res->json("No se puede eliminar un peluquero que aun tiene gatos asignados!", 400);
            }

            $this->model->RemoveCategory($id);

            return $res->json("Eliminado con exito!", 204);
        }

        public function EditCategoria($req, $res) {
            $id= $req->params->id;
            $categoria= $this->model->GetCategoryByID($id);

            if (!$categoria) {
                return $res->json("No hay un peluquero registrado con el ID {$id}", 404);
            }


            //En el ItemController (EditItem) deje una explicacion sobre esta parte
            $body = json_decode(file_get_contents('php://input'), true);
            if (!is_array($body)) {
                return $res->json("JSON invalido", 400);
            }

            $body = array_intersect_key($body, (array)$categoria);
            unset($body['id_peluquero']);
            $diff= array_diff_assoc($body, (array)$categoria);

            if (empty($diff)) {
                return $res->json("Ningun campo valido fue actualizado", 400);
            }

            $this->model->EditCategory($diff, $id);

            $CategoriaActualizada= $this->model->GetCategoryByID($id);
            return $res->json($CategoriaActualizada);
        }
    }