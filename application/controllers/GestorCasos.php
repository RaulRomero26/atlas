<?php

class GestorCasos extends Controller
{

    
    public $GestorCaso;
    public $numColumnsGC; //número de columnas por cada filtro
    public $FV;

    public function __construct(){
       
        $this->GestorCaso = $this->model('GestorCaso');
        $this->numColumnsGC = [14, 7, 6];  //se inicializa el número de columns por cada filtro
        $this->FV = new FormValidator();
    }

    public function index(){
        // $_SESSION['userdata']->columns_GC;
        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Seguimientos[2] != '1')) {
            header("Location: " . base_url . "Inicio");
            exit();
        }

        $data = [
            'titulo'    => 'Atlas Delictivo',
            'extra_css' => '<link rel="stylesheet" href="' . base_url . 'public/css/system/gestorCasos/index.css">',
            'extra_js'  => '<script src="' . base_url . 'public/js/system/gestorCasos/index2.js"></script>'
        ];

         //PROCESO DE FILTRADO DE EVENTOS DELICTIVOS
         if (isset($_GET['filtro']) && is_numeric($_GET['filtro']) && $_GET['filtro'] >= MIN_FILTRO_GC && $_GET['filtro'] <= MAX_FILTRO_GC) { //numero de filtro
            if ($_GET['filtro'] >= 13 && $_GET['filtro'] <= 14) { //si son filtros de validación
                if ($_SESSION['userdata']->Modo_Admin == '1' || $_SESSION['userdata']->Nivel_User == '1') { //si cuenta con los permisos necesarios
                    $filtro = $_GET['filtro'];
                } else { //si no cuenta con los permisos lo dirige a la vista general
                    $filtro = 1;
                }
            } else {
                $filtro = $_GET['filtro'];
            }
        } else {
            $filtro = 1;
        }

        //PROCESAMIENTO DE LAS COLUMNAS 
        $this->setColumnsSession($filtro);
        $data['columns_GC'] = $_SESSION['userdata']->columns_GC;

        //PROCESAMIENTO DE RANGO DE FOLIOS
        if (isset($_POST['rango_inicio']) && isset($_POST['rango_fin'])) {
            $_SESSION['userdata']->rango_inicio_gc = $_POST['rango_inicio'];
            $_SESSION['userdata']->rango_fin_gc = $_POST['rango_fin'];
        }

        //PROCESO DE PAGINATION
        if (isset($_GET['numPage'])) { //numero de pagination
            $numPage = $_GET['numPage'];
            if (!(is_numeric($numPage))) //seguridad si se ingresa parámetro inválido
                $numPage = 1;
        } else {
            $numPage = 1;
        }
        //cadena auxiliar por si se trata de una paginacion conforme a una busqueda dada anteriormente
        $cadena = "";
        if (isset($_GET['cadena'])) { //numero de pagination
            $cadena = $_GET['cadena'];
            $data['cadena'] = $cadena;
        }

        $where_sentence = $this->GestorCaso->generateFromWhereSentence($cadena, $filtro);
        $extra_cad = ($cadena != "") ? ("&cadena=" . $cadena) : ""; //para links conforme a búsqueda

        $no_of_records_per_page = NUM_MAX_REG_PAGE; //total de registros por pagination
        $offset = ($numPage - 1) * $no_of_records_per_page; // desplazamiento conforme a la pagina

        $results_rows_pages = $this->GestorCaso->getTotalPages($no_of_records_per_page, $where_sentence);   //total de páginas de acuerdo a la info de la DB
        $total_pages = $results_rows_pages['total_pages'];

        if ($numPage > $total_pages) {
            $numPage = 1;
            $offset = ($numPage - 1) * $no_of_records_per_page;
        } //seguridad si ocurre un error por url     

        $rows_GestorCasos = $this->GestorCaso->getDataCurrentPage($offset, $no_of_records_per_page, $where_sentence);    //se obtiene la información de la página actual

        //guardamos la tabulacion de la información para la vista
        $data['infoTable'] = $this->generarInfoTable($rows_GestorCasos, $filtro);
        //guardamos los links en data para la vista
        $data['links'] = $this->generarLinks($numPage, $total_pages, $extra_cad, $filtro);
        //número total de registros encontrados
        $data['total_rows'] = $results_rows_pages['total_rows'];
        //filtro actual para Fetch javascript
        $data['filtroActual'] = $filtro;
        $data['dropdownColumns'] = $this->generateDropdownColumns($filtro);

        switch ($filtro) {
            case '1':
                $data['filtroNombre'] = "Todos / Integrantes de Grupos";
                break;
            case '2':
                $data['filtroNombre'] = "Grupos Delictivos";
                break;
            case '3':
                $data['filtroNombre'] = "Elementos Participantes";
                break;
            case '4':
                $data['filtroNombre'] = "Objetos Asegurados";
                break;
            case '5':
                $data['filtroNombre'] = "Armas Aseguradas";
                break;
        }

        $this->view('templates/header', $data);
        $this->view('system/gestorCasos/gestorCasosView', $data);
        $this->view('templates/footer', $data);
    }

    public function nuevo(){
        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Seguimientos[3] != '1')) {
            header("Location: " . base_url . "Inicio");
            exit();
        }

        $data = [
            'titulo'    => 'Sistema de Atlas | Atlas',
            'extra_css' => '<link rel="stylesheet" href="' . base_url . 'public/css/system/Remisiones/index.css">',
            'extra_js'  => '<script src="' . base_url . 'public/js/system/GestorCasos/grupos_d.js"></script>'.
                            '<script src="' . base_url . 'public/js/system/GestorCasos/fotoP.js"></script>'.
                            '<script src="' . base_url . 'public/js/system/GestorCasos/grupos_add.js"></script>',
        ];
        $this->view('templates/header', $data);
        $this->view('system/GestorCasos/gruposView', $data);
        $this->view('templates/footer', $data);
    }


    public function editarGrupo(){
        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1)) {
            header("Location: " . base_url . "Inicio");
            exit();
        }

        $data = [
            'titulo'    => 'Sistema de Atlas | Editar',
            'extra_css' => '<link rel="stylesheet" href="' . base_url . 'public/css/system/Remisiones/index.css">',
            'extra_js'  => '<script src="' . base_url . 'public/js/system/GestorCasos/grupos_d.js"></script>'.
                            '<script src="' . base_url . 'public/js/system/GestorCasos/grupos_add.js"></script>'.
                            '<script src="' . base_url . 'public/js/system/GestorCasos/grupos_getInformacion.js"></script>'.
                            '<script src="' . base_url . 'public/js/system/GestorCasos/fotoP.js"></script>',
        ];
        $this->view('templates/header', $data);
        $this->view('system/GestorCasos/editargruposView', $data);
        $this->view('templates/footer', $data);
    }
    /* ----------------------------------------INSERSION DE EVENTOS ------------------------------------- */

    public function insertGrupoFetch(){

        

        $integrantes = json_decode($_POST['integrantes_table']);

        $success = true;
        
        if ($success) {
           
            $success_2 = $this->GestorCaso->insertNuevoGrupo($_POST);
            $caso = $success_2['grupo'];
            if ($success_2['status']) {//$success_2['status']
                $path_carpeta = BASE_PATH . "public/files/GestorCasos/" . $caso . "/Grupo/";
                foreach (glob($path_carpeta . "/*") as $archivos_carpeta) {
                    if (is_dir($archivos_carpeta)) {
                        rmDir_rf($archivos_carpeta);
                    } else {
                        unlink($archivos_carpeta);
                    }
                }
                foreach ($integrantes as $integrante) {
                    if ($integrante->row->typeImage == 'File') {
                        $type = $_FILES[$integrante->row->nameImage]['type'];
                        $extension = explode("/", $type);
                        $result = $this->uploadImageFileRemisiones($integrante->row->nameImage, $_FILES, $caso, $path_carpeta, $integrante->row->nameImage . "." . $extension[1]);
                    }
                    if ($integrante->row->typeImage == 'Photo') {
                        $result = $this->uploadImagePhotoRemisiones($integrante->row->image, $caso, $path_carpeta, $path_carpeta . $integrante->row->nameImage . ".png");
                    }
                }

                    $data_p['status'] =  true;
            
                } else{
                    $data_p['status'] =  false;
                }
            }
            echo json_encode($data_p);
    }

    public function editGrupoFetch(){
       
        $integrantes = json_decode($_POST['integrantes_table']);

        $success = true;
        
        if ($success) {
           
            $success_2 = $this->GestorCaso->editarGrupo($_POST);
            $caso = $success_2['grupo'];
            if ($success_2['status']) {//$success_2['status']
                $path_carpeta = BASE_PATH . "public/files/GestorCasos/" . $caso . "/Grupo/";
                foreach (glob($path_carpeta . "/*") as $archivos_carpeta) {
                    if (is_dir($archivos_carpeta)) {
                        rmDir_rf($archivos_carpeta);
                    } else {
                        unlink($archivos_carpeta);
                    }
                }
                foreach ($integrantes as $integrante) {
                    if ($integrante->row->typeImage == 'File') {
                        $type = $_FILES[$integrante->row->nameImage]['type'];
                        $extension = explode("/", $type);
                        $result = $this->uploadImageFileRemisiones($integrante->row->nameImage, $_FILES, $caso, $path_carpeta, $integrante->row->nameImage . "." . $extension[1]);
                    }
                    if ($integrante->row->typeImage == 'Photo') {
                        $result = $this->uploadImagePhotoRemisiones($integrante->row->image, $caso, $path_carpeta, $path_carpeta . $integrante->row->nameImage . ".png");
                    }
                }

                    $data_p['status'] =  true;
            
                } else{
                    $data_p['status'] =  false;
                }
            }
            echo json_encode($data_p);
            
    }

    public function getGrupo(){
        if (isset($_POST['no_grupo'])) {
            $no_grupo = $_POST['no_grupo'];
            $data = $this->GestorCaso->getGrupo($no_grupo);
            echo json_encode($data);
        }
        else {
            header("Location: " . base_url . "Inicio");
            exit();
        }
    }

    public function insertEventoFetch(){

          //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Seguimientos[2] != '1')) {
        header("Location: " . base_url . "Inicio");
        exit();
        }

        if (isset($_POST)) {
            $success = true;
            if ($success) {
                //se trata de insertar la nueva inspección
                $success2 = $this->GestorCaso->insertNuevoEvento($_POST);
                //print_r($success2);
            }else{
                echo json_encode("Error, Error al insertar en la DB");
            }
        }else{
           echo "post vacio";
        }
    }

    /* ----------------------------------------FUNCIONES DE FILTROS ------------------------------------- */
    //función para checar los cambios de filtro y poder asignar los valores correspondientes de las columnas a la session
    public function setColumnsSession($filtroActual = 1){
        //si el filtro existe y esta dentro de los parámetros continua
        if (isset($_SESSION['userdata']->filtro_GC) && $_SESSION['userdata']->filtro_GC >= MIN_FILTRO_GC && $_SESSION['userdata']->filtro_GC <= MAX_FILTRO_GC) {
            //si cambia el filtro se procde a cambiar los valores de las columnas que contiene el filtro seleccionado
            if ($_SESSION['userdata']->filtro_GC != $filtroActual) {
                $_SESSION['userdata']->filtro_GC = $filtroActual;
                unset($_SESSION['userdata']->columns_GC); //se borra las columnas del anterior filtro
                //se asignan las nuevas columnas y por default se muestran todas (atributo show)
                for ($i = 0; $i < $this->numColumnsGC[$_SESSION['userdata']->filtro_GC - 1]; $i++)
                    $_SESSION['userdata']->columns_GC['column' . ($i + 1)] = 'show';
            }
        } else { //si no existe el filtro entonces se inicializa con el primero por default
            $_SESSION['userdata']->filtro_GC = $filtroActual;
            unset($_SESSION['userdata']->columns_GC);
            for ($i = 0; $i < $this->numColumnsGC[$_SESSION['userdata']->filtro_GC - 1]; $i++)
                $_SESSION['userdata']->columns_GC['column' . ($i + 1)] = 'show';
        }
        //echo "filtro: ".var_dump($_SESSION['userdata']->filtro_REM)."<br>br>";
        //echo "columns: ".var_dump($_SESSION['userdata']->columns_GC)."<br>br>";
    }

    //función fetch que actualiza los valores de las columnas para la session
    public function setColumnFetch(){
        if (isset($_POST['columName']) && isset($_POST['valueColumn'])) {
            $_SESSION['userdata']->columns_GC[$_POST['columName']] = $_POST['valueColumn'];
            echo json_encode("ok");
        } else {
            header("Location: " . base_url . "Inicio");
            exit();
        }
    }
        
    
    public function generarLinks($numPage, $total_pages, $extra_cad = "", $filtro = 1){
        //$extra_cad sirve para determinar la paginacion conforme a si se realizó una busqueda
        //Creación de links para el pagination
        $links = "";

        //FLECHA IZQ (PREV PAGINATION)
        if ($numPage > 1) {
            $links .= '<li class="page-item">
                            <a class="page-link" href=" ' . base_url . 'GestorCasos/index/?numPage=1' . $extra_cad . '&filtro=' . $filtro . ' " data-toggle="tooltip" data-placement="top" title="Primera página">
                                <i class="material-icons">first_page</i>
                            </a>
                        </li>';
            $links .= '<li class="page-item">
                            <a class="page-link" href=" ' . base_url . 'GestorCasos/index/?numPage=' . ($numPage - 1) . $extra_cad . '&filtro=' . $filtro . ' " data-toggle="tooltip" data-placement="top" title="Página anterior">
                                <i class="material-icons">navigate_before</i>
                            </a>
                        </li>';
        }

        //DESPLIEGUE DE PAGES NUMBER
        $LINKS_EXTREMOS = GLOBAL_LINKS_EXTREMOS; //numero máximo de links a la izquierda y a la derecha
        for ($ind = ($numPage - $LINKS_EXTREMOS); $ind <= ($numPage + $LINKS_EXTREMOS); $ind++) {
            if (($ind >= 1) && ($ind <= $total_pages)) {

                $activeLink = ($ind == $numPage) ? 'active' : '';

                $links .= '<li class="page-item ' . $activeLink . ' ">
                                <a class="page-link" href=" ' . base_url . 'GestorCasos/index/?numPage=' . ($ind) . $extra_cad . '&filtro=' . $filtro . ' ">
                                    ' . ($ind) . '
                                </a>
                            </li>';
            }
        }

        //FLECHA DERECHA (NEXT PAGINATION)
        if ($numPage < $total_pages) {

            $links .= '<li class="page-item">
                            <a class="page-link" href=" ' . base_url . 'GestorCasos/index/?numPage=' . ($numPage + 1) . $extra_cad . '&filtro=' . $filtro . ' " data-toggle="tooltip" data-placement="top" title="Siguiente página">
                            <i class="material-icons">navigate_next</i>
                            </a>
                        </li>';
            $links .= '<li class="page-item">
                            <a class="page-link" href=" ' . base_url . 'GestorCasos/index/?numPage=' . ($total_pages) . $extra_cad . '&filtro=' . $filtro . ' " data-toggle="tooltip" data-placement="top" title="Última página">
                            <i class="material-icons">last_page</i>
                            </a>
                        </li>';
        }

        return $links;
    }

            //función para generar la información de la tabla de forma dinámica
    public function generarInfoTable($rows, $filtro = 1){
        $permisos_Editar = ($_SESSION['userdata']->Seguimientos[1] == '1') ? 'd-flex justify-content-center' : 'mi_hide';
        $permisos_Ver = ($_SESSION['userdata']->Seguimientos[2] == '1') ? 'd-flex justify-content-center' : 'mi_hide';
        $permisos_FormatoFicha = ($_SESSION['userdata']->Seguimientos[2] == '1') ? 'd-flex justify-content-center' : 'mi_hide';
        //se genera la tabulacion de la informacion por backend
        $infoTable['header'] = "";
        $infoTable['body'] = "";


        switch ($filtro) {
            case '1': //general
                $infoTable['header'] .= '
                        <th class="column1">ID PERSONA</th>
                        <th class="column2">NOMBRE</th>
                        <th class="column3">SEXO</th>
                        <th class="column4">CURP</th>
                        <th class="column5">UDC</th>
                        <th class="column6">UTC</th>
                        <th class="column7">ALIAS</th>
                        <th class="column8">PERFIL FACEBOOK</th>
                        <th class="column9">OTROS DOMICILIOS</th>
                        <th class="column10">REGISTRO VEHICULOS</th>
                        <th class="column11">ASOCIACION VEHICULOS</th>
                        <th class="column12">ANTECEDENTES PERSONA</th>
                        <th class="column13">ESTATUS</th>
                        <th class="column14">NOMBRE BANDA</th>
                    ';
                foreach ($rows as $row) {
                    $infoTable['body'] .= '<tr id="tr' . $row->ID_PERSONA . '">';
                    $infoTable['body'] .= '  <td class="column1">' . $row->ID_PERSONA . '</td>
                                            <td class="column2">' . mb_strtoupper($row->NOMBRE_COMPLETO) . '</td>
                                            <td class="column3">' . mb_strtoupper($row->SEXO) . '</td>
                                            <td class="column4">' . mb_strtoupper($row->CURP) . '</td>
                                            <td class="column5">' . mb_strtoupper($row->UDC) . '</td>
                                            <td class="column6">' . mb_strtoupper($row->UTC) . '</td>
                                            <td class="column7">' . mb_strtoupper($row->ALIAS) . '</td>
                                            <td class="column8">' . mb_strtoupper($row->PERFIL_FACEBOOK) . '</td>
                                            <td class="column9">' . mb_strtoupper($row->OTROS_DOMICILIOS) . '</td>
                                            <td class="column10">' . mb_strtoupper($row->REGISTRO_VEHICULOS) . '</td>
                                            <td class="column11">' . mb_strtoupper($row->ASOCIACION_VEHICULOS) . '</td>
                                            <td class="column12">' . mb_strtoupper($row->ANTECEDENTES_PERSONA) . '</td>
                                            <td class="column13">' . mb_strtoupper($row->ESTATUS) . '</td>
                                            <td class="column14">' . mb_strtoupper($row->NOMBRE_BANDA) . '</td>

                        ';
                    //se comprueba si el registro ya tiene un dictamen previamente llenado o si no existe genera un link para nuevo
                    
                        if ($_SESSION['userdata']->Modo_Admin == '1' || $_SESSION['userdata']->Nivel_User == '1') { //validacion de tabs validados completaente y/o permisos de validacion o modo admin
                            $infoTable['body'] .= '<td class="d-flex">
                                                    <a class="myLinks mb-3' . $permisos_Editar . '" data-toggle="tooltip" data-placement="right" title="Editar registro" href="' . base_url . 'GestorCasos/editarGrupo/?no_grupo=' . $row->ID_BANDA . '">
                                                        <i class="material-icons">edit</i>
                                                    </a>';
                        } else {
                            $infoTable['body'] .= '<td class="d-flex">';
                        }
                        $infoTable['body'] .= '
                                                <a class="myLinks mt-3' . $permisos_Ver . '" data-toggle="tooltip" data-placement="right" title="Ver registro" href="' . base_url . 'GestorCasos/verCaso/?no_grupo=' . $row->ID_BANDA . '">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                                <a class="myLinks mt-3' . $permisos_FormatoFicha . '" data-toggle="tooltip" data-placement="right" title="Generar ficha" href="' . base_url . 'GestorCasos/generarFicha/?no_grupo=' . $row->ID_BANDA . '" target="_blank">
                                                    <i class="material-icons">file_present</i>
                                                </a>
                                            </td>';
                    


                    $infoTable['body'] .= '</tr>';
                }
                break;
           
            case '2': //ubicación de los hechos
                $infoTable['header'] .= '
                        <th class="column1">ID BANDA</th>
                        <th class="column2">NOMBRE BANDA</th>
                        <th class="column3">DELITO BANDA GENERAL</th>
                        <th class="column4">DELITOS ASOCIADOS</th>
                        <th class="column5">MODUS OPERANDI</th>
                        <th class="column6">PELIGROSIDAD</th>
                        <th class="column7">ZONAS</th>
                        <th class="column8">COLONIAS</th>
                        <th class="column9">EVENTOS ASOCIADOS</th>
                        <th class="column10">EVENTOS CONFIRMADOS</th>
                        <th class="column11">EVENTOS CDI</th>
                        <th class="column12">ANTECEDENTES BANDA</th>
                        <th class="column13">ORIGEN</th>
                    ';
                foreach ($rows as $row) {
                    $infoTable['body'] .= '<tr id="tr' . $row->ID_BANDA . '">';
                    $infoTable['body'] .= '  <td class="column1">' . $row->ID_BANDA . '</td>
                                            <td class="column2">' . mb_strtoupper($row->NOMBRE_BANDA) . '</td>
                                            <td class="column3">' . mb_strtoupper($row->DELITO_BANDA_GENERAL) . '</td>
                                            <td class="column4">' . mb_strtoupper($row->DELITOS_ASOCIADOS) . '</td>
                                            <td class="column5">' . mb_strtoupper($row->MODUS_OPERANDI) . '</td>
                                            <td class="column6">' . mb_strtoupper($row->PELIGROSIDAD) . '</td>
                                            <td class="column7">' . mb_strtoupper($row->ZONAS) . '</td>
                                            <td class="column8">' . mb_strtoupper($row->COLONIAS) . '</td>
                                            <td class="column9">' . mb_strtoupper($row->EVENTOS_ASOCIADOS) . '</td>
                                            <td class="column10">' . mb_strtoupper($row->EVENTOS_CONFIRMADOS) . '</td>
                                            <td class="column11">' . mb_strtoupper($row->EVENTOS_CDI) . '</td>
                                            <td class="column12">' . mb_strtoupper($row->ANTECEDENTES_BANDA) . '</td>
                                            <td class="column13">' . mb_strtoupper($row->ORIGEN) . '</td>

                        ';
                    //se comprueba si el registro ya tiene un dictamen previamente llenado o si no existe genera un link para nuevo
                    
                        if ($_SESSION['userdata']->Modo_Admin == '1' || $_SESSION['userdata']->Nivel_User == '1') { //validacion de tabs validados completaente y/o permisos de validacion o modo admin
                            $infoTable['body'] .= '<td class="d-flex">
                                                    <a class="myLinks mb-3' . $permisos_Editar . '" data-toggle="tooltip" data-placement="right" title="Editar registro" href="' . base_url . 'GestorCasos/editarGrupo/?no_grupo=' . $row->ID_BANDA . '">
                                                        <i class="material-icons">edit</i>
                                                    </a>';
                        } else {
                            $infoTable['body'] .= '<td class="d-flex">';
                        }
                        $infoTable['body'] .= '
                                                <a class="myLinks mt-3' . $permisos_Ver . '" data-toggle="tooltip" data-placement="right" title="Ver registro" href="' . base_url . 'GestorCasos/verRemision/?no_grupo=' . $row->ID_BANDA .  '">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                                <a class="myLinks mt-3' . $permisos_FormatoFicha . '" data-toggle="tooltip" data-placement="right" title="Generar ficha" href="' . base_url . 'GestorCasos/generarFicha/?no_grupo=' . $row->ID_BANDA . '" target="_blank">
                                                    <i class="material-icons">file_present</i>
                                                </a>
                                            </td>';
                    


                    $infoTable['body'] .= '</tr>';
                }
                break;
            case '3': //elementos participantes
                $infoTable['header'] .= '
                        <th class="column1">Ficha</th>
                        <th class="column2">Núm. Remisión</th>
                        <th class="column3">Fecha y Hora</th>
                        <th class="column4">Detenido</th>
                        <th class="column5">Elemento</th>
                        <th class="column6">Cargo</th>
                        <th class="column7">Placa</th>
                        <th class="column8">Unidad</th>
                        <th class="column9">Llamado</th>
                    ';
                foreach ($rows as $row) {
                    $auxllamado = ($row->Tipo_Llamado == '0') ? "En apoyo" : "Primer respondiente";
                    $infoTable['body'] .= '<tr id="tr' . $row->No_Remision . '">';
                    $infoTable['body'] .= '  <td class="column1">' . $row->Ficha . '</td>
                                            <td class="column2">' . $row->No_Remision . '</td>
                                            <td class="column3">' . $row->Fecha_Hora . '</td>
                                            <td class="column4">' . mb_strtoupper($row->Nombre_Detenido) . '</td>
                                            <td class="column5">' . mb_strtoupper($row->Nombre_Elemento) . '</td>
                                            <td class="column6">' . mb_strtoupper($row->Cargo) . '</td>
                                            <td class="column7">' . mb_strtoupper($row->Placa) . '</td>
                                            <td class="column8">' . mb_strtoupper($row->No_Unidad) . '</td>
                                            <td class="column9">' . $auxllamado . '</td>

                        ';
                    //se comprueba si el registro ya tiene un dictamen previamente llenado o si no existe genera un link para nuevo
                    if ($row->Fecha_Hora != '') {
                        if ($row->Validacion_Tab_Bit != '11111111111' || $_SESSION['userdata']->Modo_Admin == '1' || $_SESSION['userdata']->Nivel_User == '1') { //validacion de tabs validados completaente y/o permisos de validacion o modo admin
                            $infoTable['body'] .= '<td class="d-flex">
                                                    <a class="myLinks mb-3' . $permisos_Editar . '" data-toggle="tooltip" data-placement="right" title="Editar registro" href="' . base_url . 'GestorCasos/editarRemision/?no_remision=' . $row->No_Remision . '&no_ficha=' . $row->Ficha . '">
                                                        <i class="material-icons">edit</i>
                                                    </a>';
                        } else {
                            $infoTable['body'] .= '<td class="d-flex">';
                        }
                        $infoTable['body'] .= '
                                                <a class="myLinks mt-3' . $permisos_Ver . '" data-toggle="tooltip" data-placement="right" title="Ver registro" href="' . base_url . 'GestorCasos/verRemision/?no_ficha=' . $row->Ficha . '&no_remision=' . $row->No_Remision . '">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                                <a class="myLinks mt-3' . $permisos_FormatoFicha . '" data-toggle="tooltip" data-placement="right" title="Generar formato remisión" href="' . base_url . 'GestorCasos/generarIPH1/?no_ficha=' . $row->Ficha . '&no_remision=' . $row->No_Remision . '" target="_blank">
                                                    <i class="material-icons">list_alt</i>
                                                </a>
                                                <a class="myLinks mt-3' . $permisos_FormatoFicha . '" data-toggle="tooltip" data-placement="right" title="Generar ficha" href="' . base_url . 'GestorCasos/generarFicha/?no_ficha=' . $row->Ficha . '&no_remision=' . $row->No_Remision . '" target="_blank">
                                                    <i class="material-icons">file_present</i>
                                                </a>
                                            </td>';
                    }


                    $infoTable['body'] .= '</tr>';
                }
                break;
          
        }

        $infoTable['header'] .= '<th >Operaciones</th>';
        //$infoTable['header'].='<th >Ver</th>';

        return $infoTable;
    }

    //función que filtra las columnas deseadas por el usuario
    public function generateDropdownColumns($filtro = 1){
        //parte de permisos

        $dropDownColumn = '';
        //generación de dropdown dependiendo del filtro
        switch ($filtro) {
            case '1':
                $campos = ['ID PERSONA', 'NOMBRE', 'SEXO', 'CURP', 'UDC', 'UTC', 'ALIAS', 'PERFIL FACEBOOK', 'OTROS DOMICILIOS', 'REGISTRO VEHICULOS', 'ASOCIACION VEHICULOS', 'ANTECEDENTES PERSONA', 'ESTATUS', 'NOMBRE BANDA'];
                break;
            case '2':
                $campos = ['ID BANDA', 'NOMBRE BANDA', 'DELITO BANDA GENERAL', 'DELITOS ASOCIADOS', 'Instancia', 'Folio 911'];
                break;
            case '3':
                $campos = ['Ficha', 'Núm. Remisión', 'Fecha y Hora', 'Detenido', 'Elemento', 'Cargo', 'Placa', 'Unidad', 'Llamado'];
                break;
        }
        //gestión de cada columna
        $ind = 1;
        foreach ($campos as $campo) {
            $checked = ($_SESSION['userdata']->columns_GC['column' . $ind] == 'show') ? 'checked' : '';
            $dropDownColumn .= ' <div class="form-check">
                                    <input class="form-check-input checkColumns" type="checkbox" value="' . $_SESSION['userdata']->columns_GC['column' . $ind] . '" onchange="hideShowColumn(this.id);" id="column' . $ind . '" ' . $checked . '>
                                    <label class="form-check-label" for="column' . $ind . '">
                                        ' . $campo . '
                                    </label>
                                </div>';
            $ind++;
        }
        $dropDownColumn .= '     <div class="dropdown-divider">
                                </div>
                                <div class="form-check">
                                    <input id="checkAll" class="form-check-input" type="checkbox" value="hide" onchange="hideShowAll(this.id);" id="column' . $ind . '" checked>
                                    <label class="form-check-label" for="column' . $ind . '">
                                        Todo
                                    </label>
                                </div>';
        return $dropDownColumn;
    }

    //funcion para borrar variable sesión para filtro de rangos de fechas
    public function removeRangosFechasSesion(){

        if (isset($_REQUEST['filtroActual'])) {
            unset($_SESSION['userdata']->rango_inicio_gc);
            unset($_SESSION['userdata']->rango_fin_gc);

            header("Location: " . base_url . "GestorCasos/index/?filtro=" . $_REQUEST['filtroActual']);
            exit();
        } else {
            header("Location: " . base_url . "Cuenta");
            exit();
        }
    }

/*--------------------------------------FUNCIONES UPDATE TABS -------------------------------------- */


    public function uploadImageFileRemisiones($name, $file, $caso, $carpeta, $fileName){
        $type = $file[$name]['type'];
        $extension = explode("/", $type);

        $imageUploadPath = $carpeta . $fileName;
        $allowed_mime_type_arr = array('jpeg', 'png', 'jpg', 'PNG');

        if (!file_exists($carpeta))
            mkdir($carpeta, 0777, true);

        if (in_array($extension[1], $allowed_mime_type_arr)) {
            $img_temp = $file[$name]['tmp_name'];
            $compressedImg = $this->compressImage($img_temp, $imageUploadPath, 75);
            $band = true;
        } else {
            $band = false;
        }

        return $band;
    }

    public function compressImage($source, $destination, $quality){
        $imgInfo = getimagesize($source);
        $mime = $imgInfo['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source);
                break;
            default:
                $image = imagecreatefromjpeg($source);
        }

        imagejpeg($image, $destination, $quality);

        return $imgInfo;
    }

    public function uploadImagePhotoRemisiones($img, $ficha, $carpeta, $ruta){
            //comprobar los permisos para dejar pasar al módulo
            if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Seguimientos[3] != '1')) {
                header("Location: " . base_url . "Inicio");
                exit();
            }
            /* ----- ----- ----- Existe la carpeta ----- ----- ----- */
            if (!file_exists($carpeta))
                mkdir($carpeta, 0777, true);

            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);

            file_put_contents($ruta, $image_base64);

            return true;
    }
}
?>