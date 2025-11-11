<?php
    //Controlador de gatos / Entidades N de la relacion

    require_once __DIR__ . '/../Models/ItemModel.php';  
    //Me traigo el Model de Categorias (Peluqueros) porque necesito ver las claves foraneas
    require_once __DIR__ . '/../Models/CategoryModel.php';

    class ItemController {

        private $model;
        private $FKmodel;

        public function __construct() {
            $this->model= new ItemModel();
            $this->FKmodel= new CategoryModel();
        }

        public function GetItems($req, $res) {
            $param='id_gato';
            $order='ASC';

            if (isset($_GET['param'])) {
                $param= $_GET['param'];
                $param= strtolower($param);

                //Si no recibo un parametro valido hago que ordene simplemente por los ID de los peluqueros
                //el query &param acepta PESO_KG , EDAD_MESES , ID_PELUQUERO
                //No es case sensitive
                $ValidParams= ['peso_kg', 'edad_meses', 'id_peluquero'];
                if (!in_array($param, $ValidParams)) {
                        $param= 'id_gato';
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

            $items= $this->model->GetElements($param,$order);
            
            //Ligera correccion para no mostrar 'null' cuando un gato no tenga un peluquero asignado
            //(No es tan practico pero no me gustaba dejarlo asi feo a la vista)
            foreach ($items as $item) {
                if (!isset($item->id_peluquero)) {
                    $item->id_peluquero= 'No tiene';
                }
            }
            return $res->json($items);
        }

        public function GetItem($req, $res) {
            $id= $req->params->id;

            $item= $this->model->GetItem($id);
            
            if (!$item) {
                return $res->json("No existe un gato con el ID {$id}", 404);
            }

            return $res->json($item);
        }

        public function AddItem($req, $res) {
            //Pido casi los mismos campos obligatorios que en la parte 2 del trabajo por consistencia
            //(Pero le sumo obligatorio una observacion para hacerlo mas dinamico)
            $campos= ['nombre', 'edad_meses', 'raza', 'peso_kg', 'observaciones'];
            foreach ($campos as $campo) {
                if (empty($req->body->$campo)) {
                    return $res->json("Falta completar el campo {$campo}", 400);
                }
            }

            $nombre= $req->body->nombre;
            $edad= $req->body->edad_meses;
            $raza= $req->body->raza;
            $peso= $req->body->peso_kg;
            $observaciones= $req->body->observaciones;

            if (empty($req->body->color)) {
                $color= 'No definido';
            } else {
                $color= $req->body->color;
            }
            //Chequeo simple para que no se pueda agregar un gato con un ID de un peluquero que no existe
            if (!empty($req->body->id_peluquero)) {
                $id_peluquero= $req->body->id_peluquero;
                $FK= $this->FKmodel->GetCategoryByID($id_peluquero);

                if (!$FK) {
                    return $res->json("No se puede agregar un peluquero inexistente a un gato, no hay peluquero con ID {$id_peluquero}", 400);
                }
            } else {
                $id_peluquero=null;
            }

            $GatoID= $this->model->AddItem($nombre, $edad, $raza, $color, $peso, $observaciones, $id_peluquero);

            if (!$GatoID) {
                return $res->json("Error del servidor al insertar datos", 500);
            }

            $Gato= $this->model->GetItem($GatoID);
            return $res->json($Gato, 201); 
        }

        public function DeleteItem($req, $res) {
            $id= $req->params->id;
            $item= $this->model->GetItem($id);

            if (!$item) {
                return $res->json("No hay un gato registrado con el ID {$id}", 404);
            }

            $this->model->RemoveItem($id);

            return $res->json("Eliminado con exito!", 204);
        }

        public function EditItem($req, $res) {
            $id= $req->params->id;
            $item= $this->model->GetItem($id);

            if (!$item) {
                return $res->json("No hay un gato registrado con el ID {$id}", 404);
            }

            //Hago un array asociativo con las diferencias entre el body y el item traido del model
            //y atajo cualquier campo bizarro que me pueda mandar el user para que no me rompan la PDO
            $body = json_decode(file_get_contents('php://input'), true);
            if (!is_array($body)) {
                return $res->json("JSON invalido", 400);
            }

            $body = array_intersect_key($body, (array)$item);
            //Si el user se hizo el gracioso y me envio un id_gato lo saco urgente del body antes de hacer diff
            unset($body['id_gato']);
            $diff= array_diff_assoc($body, (array)$item);

            //Chequeo que me enviaron al menos un campo valido para actualizar
            if (empty($diff)) {
                return $res->json("Ningun campo valido fue actualizado", 400);
            }

            //En caso de que el user quiera cambiar la clave foranea hago un chequeo de que no meta
            //cualquier numero (Solo toma peluqueros existentes)
            if (isset($diff['id_peluquero'])) {
                $categoria= $this->FKmodel->GetCategoryByID($diff['id_peluquero']);
                if (!$categoria) {
                    return $res->json("No existe un peluquero con ese ID, no se puede asignar", 400);
                }
            }

            $this->model->EditItem($diff, $id);

            $itemActualizado= $this->model->GetItem($id);
            return $res->json($itemActualizado);
        }
    }