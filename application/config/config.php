<?php
    
    define('app_path', dirname(dirname(__FILE__)));     //Ruta de la app
    define('BASE_PATH', realpath(dirname(__FILE__) . '/../..').'/'); //BASE_PATH del proyecto
    define('base_url', 'http://localhost/atlas/'); //Ruta de la url

    define('site_name', 'Atlas Delictivo');       //Nombre del sitio

    //Configuración de acceso a la base de datos
    define ('DB_HOST', 'localhost');
    define ('DB_USER', 'root');
    define ('DB_PASSWORD', '');
    define ('DB_NAME', 'atlas');

    //key de encryptación de información
    define ('CRYPTO_KEY', 'planeacion_xdlol123');
    //valores globales del número máximo de registros por Pagination
    define ('NUM_MAX_REG_PAGE', 7);
    define ('GLOBAL_LINKS_EXTREMOS', 4);
     //globals GESTOS DE CASOS
     define('MIN_FILTRO_GC', 1);
     define('MAX_FILTRO_GC', 15);
    //globales USUARIOS
    define('MIN_FILTRO_USER', 1);
    define('MAX_FILTRO_USER', 3);
    //globales HISTORIAL
    define('MIN_FILTRO_HIS',1);
    define('MAX_FILTRO_HIS',11);
    //Zona horaria
    date_default_timezone_set ('America/Mexico_City');

    session_start();//configuracion de sesiones
?>
