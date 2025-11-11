<?php

require_once __DIR__ . '/../../src/config.php';

class ModelDB {
    protected $db;
    public function __construct() {
        $this->db = new PDO(
    "mysql:host=".MYSQL_HOST.
         ";dbname=".MYSQL_DB.";charset=utf8", 
MYSQL_USER, MYSQL_PASS);
        $this->_deploy();
    }
    private function _deploy() {
        $query = $this->db->query('SHOW TABLES');
        $tables = $query->fetchAll();
        if(count($tables) == 0) {
            $sql =<<<'END'
                CREATE TABLE `gatos` (
                    `id_gato` int(11) NOT NULL,
                    `nombre` varchar(100) NOT NULL,
                    `edad_meses` int(11) DEFAULT NULL,
                    `raza` varchar(50) NOT NULL,
                    `color` varchar(20) NOT NULL,
                    `peso_kg` float DEFAULT NULL,
                    `observaciones` varchar(256) NOT NULL,
                    `id_peluquero` int(11) DEFAULT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
                    
                INSERT INTO `gatos` (`id_gato`, `nombre`, `edad_meses`, `raza`, `color`, `peso_kg`, `observaciones`, `id_peluquero`) VALUES
                    (1, 'Canelo', 15, 'Gato naranja', 'Naranja', 4, 'Ligera cautela: fanatico de saltar hacia las cortinas, conocido por ser muy lloron', 1),
                    (3, 'Luna', 15, 'Gato cafe', 'cafe', 5, 'Hermana calmada de Canelo', 1),
                    (4, 'Tito', 26, 'Siames', 'Crema', 5, 'Callejero..', NULL),
                    (15, 'Amarga', 2, 'Desconocida', 'Gris', 9, 'Chonker', 1),
                    (16, 'Abigail', 2, 'Egipcio', 'cafe', 7, 'Amenaza a todo lo que no sea un objeto muy solido', 2);

                CREATE TABLE `peluqueros` (
                    `id_peluquero` int(11) NOT NULL,
                    `nombre_apellido` varchar(100) NOT NULL,
                    `telefono` varchar(16) NOT NULL,
                    `edad` int(100) NOT NULL,
                    `turno` varchar(20) NOT NULL,
                    `especialidad` varchar(256) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

                INSERT INTO `peluqueros` (`id_peluquero`, `nombre_apellido`, `telefono`, `edad`, `turno`, `especialidad`) VALUES
                    (1, 'Rodrigo Membrilla', '2494329912', 23, 'Tarde', 'Especializado en paciencia y cuidado de gatos naranjas con dudosa cantidad de inteligencia'),
                    (2, 'Agustin F.', '24941', 22, 'Mañana', 'Pasante.');

                CREATE TABLE `usuarios` (
                    `id` int(11) NOT NULL,
                    `username` varchar(50) NOT NULL,
                    `password` varchar(255) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

                INSERT INTO `usuarios` (`id`, `username`, `password`) VALUES
                    (1, 'UserEjemplo', '$2y$10$2zKVZiStZpsiBVrzzu1NOusQtE5oSWOc3SNIxFHRWBnoQd0TtJism'),
                    (2, 'webadmin', '$2y$10$LAudmGyDa1DI0mLVYC4RdeoFZrQF2EtmoER1r8Z9gxeV5jU7s6AOa');
                ALTER TABLE `gatos`
                    ADD PRIMARY KEY (`id_gato`),
                    ADD KEY `fk_peluquero` (`id_peluquero`);

                ALTER TABLE `peluqueros`
                    ADD PRIMARY KEY (`id_peluquero`);

                ALTER TABLE `usuarios`
                    ADD PRIMARY KEY (`id`);

                ALTER TABLE `gatos`
                    MODIFY `id_gato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

                ALTER TABLE `peluqueros`
                    MODIFY `id_peluquero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;


                ALTER TABLE `usuarios`
                    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

                ALTER TABLE `gatos`
                    ADD CONSTRAINT `fk_peluquero` FOREIGN KEY (`id_peluquero`) REFERENCES `peluqueros` (`id_peluquero`);
                COMMIT;
            END;
        $this->db->query($sql);
        }
    }

    //Function para que redirija todos los $this->db de las clases hijas
    public function __call(string $name, array $arguments) {
        return call_user_func_array([$this->db, $name], $arguments);
    }
}

