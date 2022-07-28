<?php

class Remision
{

    public $db; //variable para instanciar el objeto PDO
    public function __construct()
    {
        $this->db = new Base(); //se instancia el objeto con los métodos de PDO
    }


    public function getDetitosByCadena($post)
    {
        $sql = "SELECT Descripcion FROM catalogo_falta_delito WHERE Entidad =" . "'" . $post . "'" . "AND Status = 1";
        $this->db->query($sql);
        $resultado =  $this->db->registers();
        return $resultado;
    }

    public function getRowsFromTable($tableName, $field, $no_ficha)
    {
        $sql = "SELECT * FROM $tableName WHERE $field = $no_ficha";
        $this->db->query($sql);
        $this->db->registers();
        return $this->db->rowCount();
    }

    public function getLastFichas()
    {
        $tiempo_hrs = 24; //fichas de las ultimas x horas
        $sql = "
            SELECT No_Ficha
            FROM ficha
            WHERE UNIX_TIMESTAMP(ficha.Fecha_Registro_Ficha) >= (UNIX_TIMESTAMP()-" . $tiempo_hrs . "*3600);    
        ";
        $this->db->query($sql);
        $resultados =  $this->db->registers();
        return $resultados;
    }

    public function getEntidad($info)
    {
        switch ($info) {
            case 'M.P. FUERO COMÚN':
                return "MINISTERIO PÚBLICO";
                break;

            case 'M.P. FEDERAL':
                return "MINISTERIO PÚBLICO FEDERAL";
                break;

            case 'ADOLESCENTES I.':
                return "MINISERIO PÚBLICO PARA ADOLESCENTES";
                break;

            case 'MIGRACION':
                return "INSTITUTO MEXICANO DE MIGRACIÓN (IMM)";
                break;

            case 'JUEZ CALIFICADOR':
                return "JUEZ CALIFICADOR";
                break;

            case 'CERESO':
                return "CERESO";
                break;
        }
    }
/*-----------------------FUNCIONES DE INSERTS-----------------------*/
    public function nuevaRemision($post)
    {
        $response['status'] = true;
        $response['no_remision'] = -1;
        $response['no_ficha'] = -1;
        try {
            /*orden de inserciones:
                ficha
                domicilio (primero obtener el id del municipio que coincida con el del formulario)
                media filiación
                detenido
                alias
                remisión
            */
            $this->db->beginTransaction(); //transaction para evitar errores de inserción
            //insertando en ficha

            $nueva_SiNo = $post['ficha'];

            switch ($nueva_SiNo) {
                case 'Si':
                    $no_ficha = $post['No_Ficha'];
                    break;

                case 'No':
                    $sql = "INSERT 
                        INTO ficha(Tipo_Ficha) 
                        VALUES('REMISION')";
                    $this->db->query($sql);
                    $this->db->execute();
                    $this->db->query("SELECT LAST_INSERT_ID() as No_Ficha"); //se recupera el id de fichas creado recientemente
                    $no_ficha = $this->db->register()->No_Ficha;
                    break;
            }


            //insertando en domicilio (obteniendo primero el id del municipio)
            // $sql = "SELECT Id_Municipio FROM municipio WHERE Nombre = '" . $post['Municipio'] . "'";
            // $this->db->query($sql);
            // $id_municipio = $this->db->register()->Id_Municipio;
            $sql = "    INSERT 
                            INTO domicilio(
                                Municipio,
                                Colonia,
                                Calle,
                                No_Exterior,
                                No_Interior,
                                Coordenada_X,
                                Coordenada_Y,
                                CP,
                                Estado
                            )
                            VALUES(
                                '" . trim($post['Municipio']) . "',
                                '" . trim($post['Colonia']) . "',
                                '" . trim($post['Calle']) . "',
                                '" . trim($post['noExterior']) . "',
                                '" . trim($post['noInterior']) . "',
                                '" . trim($post['cordX']) . "',
                                '" . trim($post['cordY']) . "',
                                '" . trim($post['CP']) . "',
                                '" . trim($post['Estado']) . "'
                                
                            )";
            $this->db->query($sql);
            $this->db->execute();
            $this->db->query("SELECT LAST_INSERT_ID() as Id_Domicilio"); //se recupera el id de domicilio creado recientemente
            $id_domicilio = $this->db->register()->Id_Domicilio;

            //media filiación
            $this->db->query("INSERT INTO media_filiacion VALUES()");
            $this->db->execute();
            $this->db->query("SELECT LAST_INSERT_ID() as Id_Media_Filiacion"); //se recupera el id de media filiación creado recientemente
            $id_media_filiacion = $this->db->register()->Id_Media_Filiacion;

            //detenido
            //campos adicionales
            $alcoholemia    = (isset($post['alcoholemia_principales'])) ? '1' : '0';
            $verificado_CURP = (isset($post['verificado_principales'])) ? '1' : '0';
            $RFC            = (isset($post['RFC_principales'])) ? $post['RFC_principales'] : '';
            $correo         = (isset($post['correo_principales'])) ? $post['correo_principales'] : '';
            $ocupacion      = (isset($post['Ocupacion_principales'])) ? $post['Ocupacion_principales'] : '';
            $facebook       = (isset($post['Facebook_principales'])) ? $post['Facebook_principales'] : '';
            $edo_civil      = (isset($post['edoCivil_principales'])) ? $post['edoCivil_principales'] : '';
            $telefono       = (isset($post['Telefono_principales'])) ? $post['Telefono_principales'] : '';
            $imei1          = (isset($post['imei_1_principales'])) ? $post['imei_1_principales'] : '';
            $imei2          = (isset($post['imei_2_principales'])) ? $post['imei_2_principales'] : '';

            $id_iph = (isset($post['id_iph'])) ? $post['id_iph'] : 'Z';
            $sql = "    INSERT 
                            INTO detenido(
                                Nombre,
                                Ap_Paterno,
                                Ap_Materno,
                                Genero,
                                Edad,
                                Escolaridad,
                                Id_Domicilio,
                                Lugar_Origen,
                                Fecha_Nacimiento,
                                Estado_Civil,
                                Telefono,
                                Imei1,
                                Imei2,
                                CURP,
                                Verificacion_CURP,
                                Alcoholemia,
                                RFC,
                                Correo_Electronico,
                                Facebook,
                                Ocupacion,
                                Pertenencias_Detenido,
                                Id_Media_Filiacion,
                                Id_IPH
                            )
                            VALUES(
                                '" . trim($post['Nombre_principales']) . "',
                                '" . trim($post['appPaterno_principales']) . "',
                                '" . trim($post['appMaterno_principales']) . "',
                                '" . trim($post['sexo_principales']) . "',
                                '" . trim($post['edad_principales']) . "',
                                '" . trim($post['escolaridad_principales']) . "',
                                " . trim($id_domicilio) . ",
                                '" . trim($post['procedencia_principales']) . "',
                                '" . trim($post['FechaNacimiento_principales']) . "',
                                '" . trim($edo_civil) . "',
                                '" . trim($telefono) . "',
                                '" . trim($imei1) . "',
                                '" . trim($imei2) . "',
                                '" . trim($post['CURP_principales']) . "',
                                b'" . trim($verificado_CURP) . "',
                                b'" . trim($alcoholemia) . "',
                                '" . trim($RFC) . "',
                                '" . trim($correo) . "',
                                '" . trim($facebook) . "',
                                '" . trim($ocupacion) . "',
                                '" . trim($post['pertenencias_rem']) . "',
                                " . trim($id_media_filiacion) . ",
                                '" . trim($id_iph) . "'
                            )";
            $this->db->query($sql);
            $this->db->execute();
            $this->db->query("SELECT LAST_INSERT_ID() as No_Remision"); //se recupera el id de detenido creado recientemente
            $no_remision = $this->db->register()->No_Remision;

            if(!(isset($post['id_iph']))){
                $sql = "UPDATE detenido 
                            SET Id_IPH = $no_remision 
                        WHERE No_Remision = $no_remision ";

                $this->db->query($sql);
                $this->db->execute();
            }


            //remisión
            $id_usuario = $_SESSION['userdata']->Id_Usuario;

            $post['tipoFicha'] = 'Policia';
            //     $Zona_Sector = $post['zona'];
            // else
            //     $Zona_Sector = $post['sector'];

            $Zona_Sector = $post['zona'];

            $sql = "    INSERT 
                            INTO remision(
                                No_Ficha,
                                No_Remision,
                                Id_Usuario,
                                Fecha_Hora,
                                /* Status_Nivel_User, */
                                Folio_911,
                                Grupo,
                                Zona_Sector,
                                Instancia,
                                Cia
                            )
                            VALUES(
                                '" . trim($no_ficha) . "',
                                " . trim($no_remision) . ",
                                '" . trim($id_usuario) . "',
                                '" . trim($post['fecha_principales']) . " " . trim($post['hora_principales']) . "',
                                '" . trim($post['911_principales']) . "',
                                '" . trim($post['tipoFicha']) . "',
                                '" . trim($Zona_Sector) . "',
                                '" . trim($post['Remitido']) . "',
                                '" . trim($post['CIA_principales']) . "'
                            )";
            $this->db->query($sql);
            $this->db->execute();

            //alias
            $sql = "    INSERT 
                            INTO alias_detenido(
                                No_Remision,
                                Nombre
                            )
                            VALUES(
                                '" . trim($no_remision) . "',
                                '" . trim($post['Alias']) . "'
                            )";
            $this->db->query($sql);
            $this->db->execute();

            $this->updateNoDetentidoByFicha($no_ficha);

            $sql = "SELECT COUNT(*) AS Num_Remitidos FROM remision WHERE No_Ficha = $no_ficha";
            $this->db->query($sql);
            $Num_Detenidos = $this->db->register()->Num_Remitidos;

            $sql = "UPDATE detenido SET No_Detenido = trim($Num_Detenidos) WHERE No_Remision =  $no_remision";
            $this->db->query($sql);
            $this->db->execute();

            $response['no_remision'] = $no_remision; //se asigna el no_remision para retornar a nueva/
            $response['no_ficha'] = $no_ficha; //se obtiene la ficha
            $response['remitido'] = $post['Remitido'];


            $this->db->commit(); //si todo sale bien se realiza los commits

        } catch (Exception $e) {
            //echo "Ficha: $no_ficha - municipio $id_municipio - domicilio $id_domicilio - media $id_media_filiacion - Remision: $no_remision -user: $id_usuario - Error DB: ".$e;
            $response['status'] = false;
            $response['error_message'] = $e;
            $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
        }
        return $response;
    }

    public function peticionarioInsert($post)
    {
        /*orden inserciones
            domicilio
            obtener id del municipio que tenga el domicilio
            $this->db->query("SELECT LAST_INSERT_ID() as Id_Domicilio"); //se recupera el id de domicilio creado recientemente
            $id_domicilio = $this->db->register()->Id_Domicilio;
            peticionario
        */

        $response['status'] = true;

        try {

            $this->db->beginTransaction();

            //insertando en domicilio (obteniendo primero el id del municipio)
            $sql = "SELECT Id_Municipio FROM municipio WHERE Nombre = '" . $post['Municipio_peticionario'] . "'";
            $this->db->query($sql);
            $id_municipio = $this->db->register()->Id_Municipio;
            $sql = "    INSERT 
                        INTO domicilio(
                            Id_Municipio,
                            Colonia,
                            Calle,
                            No_Exterior,
                            No_Interior,
                            Coordenada_X,
                            Coordenada_Y
                        )
                        VALUES(
                            " . trim($id_municipio) . ",
                            '" . trim($post['Colonia_peticionario']) . "',
                            '" . trim($post['Calle_peticionario']) . "',
                            '" . trim($post['noExterior_peticionario']) . "',
                            '" . trim($post['noInterior_peticionario']) . "',
                            '" . trim($post['cordX_peticionario']) . "',
                            '" . trim($post['cordY_peticionario']) . "'
                        )";
            $this->db->query($sql);
            $this->db->execute();
            $this->db->query("SELECT LAST_INSERT_ID() as Id_Domicilio"); //se recupera el id de domicilio creado recientemente
            $id_domicilio = $this->db->register()->Id_Domicilio;

            //Inserción del peticionario
            $sql =  "   INSERT
                        INTO peticionario_remision(
                            No_Ficha,
                            Nombre,
                            Ap_Paterno,
                            Ap_Materno,
                            Fecha_Nacimiento,
                            Genero,
                            Edad,
                            Id_Domicilio,
                            Lugar_Origen
                        )
                        VALUES(
                            '" . trim($post['no_ficha_peticionario']) . "',
                            '" . trim($post['peticionario_Nombres']) . "',
                            '" . trim($post['peticionario_appPaterno']) . "',
                            '" . trim($post['peticionario_appMaterno']) . "',
                            '" . trim($post['peticionario_Fecha_n']) . "',
                            '" . trim($post['peticionario_Sexo']) . "',
                            '" . trim($post['peticionario_Edad']) . "',
                            '" . trim($id_domicilio) . "',
                            '" . trim($id_municipio) . "'
                        )";

            $this->db->query($sql);
            $this->db->execute();
            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            //echo "Ficha: $no_ficha - municipio $id_municipio - domicilio $id_domicilio - media $id_media_filiacion - Remision: $no_remision -user: $id_usuario - Error DB: ".$e;
            $response['status'] = false;
            $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
        }
        return $response;
    }


    public function ubicacionHechosInsert($post)
    {
        /*orden inserciones
            ubicacion
            obetener el id de ubicacion
            obtener el no de ficha a la remision que se tiene
            ubicacion de los hechos remision (meter aquí tanto el id de ubicacion como el num de ficha)

            insertar en tabla falta_delito detenido
        */

        $response['status'] = true;

        try {
            $this->db->beginTransaction();
            $sql = "INSERT
                    INTO ubicacion(
                        Calle_1,
                        Calle_2,
                        No_Ext,
                        No_Int,
                        Colonia,
                        Coordenada_X,
                        Coordenada_Y,
                        CP
                    )
                    VALUES(
                        '" . trim($post['Calle_hechos']) . "',
                        '" . trim($post['Calle2_hechos']) . "',
                        '" . trim($post['noExterior_hechos']) . "',
                        '" . trim($post['noInterior_hechos']) . "',
                        '" . trim($post['Colonia_hechos']) . "',
                        '" . trim($post['cordX_hechos']) . "',
                        '" . trim($post['cordY_hechos']) . "',
                        '" . trim($post['CP_hechos']) . "'
                    )";

            $this->db->query($sql);
            $this->db->execute();
            $this->db->query("SELECT LAST_INSERT_ID() as Id_Ubicacion"); //se recupera el id de la ubicacion creada recientemente
            $id_ubicacion = $this->db->register()->Id_Ubicacion;

            //echo $id_ubicacion;

            $no_ficha =  $post['no_ficha_ubicacionHechos'];
            $no_remision = $post['no_remision_ubicacionHechos'];

            //SELECT Instancia FROM remision AS Entidad WHERE No_Ficha = 1006  LIMIT 1
            $this->db->query("SELECT Instancia AS Instancia FROM remision WHERE No_Remision = $no_remision");
            $aux_entidad = $this->db->register()->Instancia;
            $entidad = $this->getentidad($aux_entidad);

            $Faltadelito = $post['delito_1'];

            switch ($Faltadelito) {
                case 'ROBO A COMERCIO':
                    $sql = "INSERT 
                    INTO ubicacion_hechos_remision(
                        No_Ficha,
                        Id_Ubicacion,
                        Negocio_Afectado,
                        No_Participantes,
                        Hora_Reporte
                    )
                    VALUES(
                        " . trim($no_ficha) . ",
                        " . trim($id_ubicacion) . ",
                        '" . trim($post['negocio']). "',
                        '" . trim($post['participantes_hechos']). "',
                        '" . trim($post['hora_hechos']) . "'
                    )";

                    break;

                case 'ROBO A TRANSPORTE PUBLICO':
                    $sql = "INSERT 
                    INTO ubicacion_hechos_remision(
                        No_Ficha,
                        Id_Ubicacion,
                        Ruta_Afectada,
                        Unidad_Ruta_Afectada,
                        No_Participantes,
                        Hora_Reporte
                    )
                    VALUES(
                        " . trim($no_ficha) . ",
                        " . trim($id_ubicacion) . ",
                        '" . trim($post['ruta']) . "',
                        '" . trim($post['unidad']) . "',
                        '" . trim($post['participantes_hechos']) . "',
                        '" . trim($post['hora_hechos']) . "'
                    )";
                    break;

                default:

                    $sql = "INSERT 
                            INTO ubicacion_hechos_remision(
                                No_Ficha,
                                Id_Ubicacion,
                                No_Participantes,
                                Hora_Reporte
                            )
                            VALUES(
                                " . trim($no_ficha) . ",
                                " . trim($id_ubicacion) . ",
                                '" . trim($post['participantes_hechos']) . "',
                                '" . trim($post['hora_hechos']) . "'
                            )";
                    break;
            }

            $this->db->query($sql);
            $this->db->execute();

            //Se actualiza el numero de detenidos
            $this->updateNoDetentidoByFicha($no_ficha);

            //SELECT Falta_Delito FROM catalogo_falta_delito AS Tipo WHERE Descripcion = 'ROBO A COMERCIO' LIMIT 1

            $this->db->query("SELECT Falta_Delito AS Tipo FROM catalogo_falta_delito WHERE Entidad = '" . $entidad . "' AND Descripcion = '" . $Faltadelito . "'");
            $tipo = $this->db->register()->Tipo;

            //echo $tipo;


            $sql = "UPDATE remision 
                    SET Falta_Delito_Tipo = '" . trim($tipo) . "', Falta_Delito_Descripcion = '" . trim($Faltadelito) . "' 
                    WHERE No_Remision = " . trim($no_remision) . "";
            $this->db->query($sql);
            $this->db->execute();

            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            //echo "Ficha: $no_ficha - municipio $id_municipio - domicilio $id_domicilio - media $id_media_filiacion - Remision: $no_remision -user: $id_usuario - Error DB: ".$e;
            $response['status'] = false;
            $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
        }
        return $response;
    }


    function updateNoDetentidoByFicha($no_Ficha)
    {
        $sql = "SELECT Total_Detenidos FROM ubicacion_hechos_remision WHERE No_Ficha = " . $no_Ficha;

        $this->db->query($sql);
        $aux_detenidos = $this->db->register();

        if ($aux_detenidos) {
            $sql = "SELECT COUNT(*) AS num_detenidos FROM remision WHERE No_Ficha = $no_Ficha";
            $this->db->query($sql);
            $no_detenidos = $this->db->register()->num_detenidos;

            //echo $no_detenidos;
            $sql = "UPDATE ubicacion_hechos_remision SET Total_Detenidos = trim($no_detenidos) WHERE No_Ficha = $no_Ficha";
            $this->db->query($sql);
            $this->db->execute();
        } else {


            $sql = "INSERT
                    INTO ubicacion(
                        Coordenada_Y
                    )
                    VALUES(
                        ''
                    )";

            $this->db->query($sql);
            $this->db->execute();
            $this->db->query("SELECT LAST_INSERT_ID() as Id_Ubicacion"); //se recupera el id de la ubicacion creada recientemente
            $id_ubicacion = $this->db->register()->Id_Ubicacion;

            $sql = "INSERT 
                    INTO ubicacion_hechos_remision(
                        No_Ficha,
                        Id_Ubicacion,
                        Hora_Reporte, 
                        Total_Detenidos
                    )
                    VALUES(
                        " . trim($no_Ficha) . ",
                        " . trim($id_ubicacion) . ",
                        '',
                        '1'
                    )";
            $this->db->query($sql);
            $this->db->execute();
        }
    }

    public function ubicacionDetencionInsert($post)
    {
        /*orden inserciones
            ubicacion
            obetener el id de ubicacion
            obtener el no de remision
            ubicacion_detencion
        */

        $response['status'] = true;

        try {
            $this->db->beginTransaction();

            $sql = "INSERT
                    INTO ubicacion(
                        Calle_1,
                        Calle_2,
                        No_Ext,
                        No_Int,
                        Colonia,
                        Coordenada_X,
                        Coordenada_Y
                    )
                    VALUES(
                        '" . trim($post['Calle_1_detencion']) . "',
                        '" . trim($post['Calle_2_detencion']) . "',
                        '" . trim($post['noExterior_detencion']) . "',
                        '" . trim($post['noInterior_detencion']) . "',
                        '" . trim($post['Colonia_detencion']) . "',
                        '" . trim($post['cordX_detencion']) . "',
                        '" . trim($post['cordY_detencion']) . "'
                    )";

            $this->db->query($sql);
            $this->db->execute();
            $this->db->query("SELECT LAST_INSERT_ID() as Id_Ubicacion"); //se recupera el id de la ubicacion creada recientemente
            $id_ubicacion = $this->db->register()->Id_Ubicacion;

            $no_remision = $post['no_remision_detencion'];

            $sql = "INSERT
                    INTO ubicacion_detencion(
                        No_Remision,
                        Id_Ubicacion,
                        Forma_Detencion,
                        Tipo_Violencia,
                        Fecha_Hora_Detencion,
                        Observaciones
                    )VALUES(
                        " . trim($no_remision) . ",
                        " . trim($id_ubicacion) . ",
                        '" . trim($post['formaDetencion']) . "',
                        '" . trim($post['tipoViolencia']) . "',
                        '" . trim($post['fecha_detencion']) . " " . trim($post['hora_detencion']) . "',
                        '" . trim($post['observaciones_detencion']) . "'
                    )";

            $this->db->query($sql);
            $this->db->execute();
            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $response['status'] = false;
            $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
        }
        return $response;
    }

    public function mediaFiliacionInsert($post)
    {
        /*orden inserciones
            primero obtener el id_media_filiacion de la remision en cuestion
            actualizar media filiacion
            insertar en contacto poniendo el no_remision en el que estes
        */
        //SELECT Id_Media_Filiacion from detenido where No_Remision = 111;
        $response['status'] = true;
        try {
            $this->db->beginTransaction();


            $this->db->query("SELECT Id_Media_Filiacion AS id_mediaF from detenido where No_Remision = '" . $post['no_remision_peticionario'] . "'");
            $id_mediaF = $this->db->register()->id_mediaF;

            $sql = "UPDATE media_filiacion 
                SET Complexion      = '" . trim($post['Complexion']) . "',
                    Estatura_cm     = " . trim($post['Estarura']) . ",
                    Color_Piel      = '" . trim($post['Color_p']) . "',
                    Forma_Cara      = '" . trim($post['formaCara']) . "',
                    Pomulos         = '" . trim($post['Pomulos']) . "',
                    Cabello         = '" . trim($post['Cabello']) . "',
                    Color_Cabello   = '" . trim($post['colorCabello']) . "',
                    Tam_Cabello     = '" . trim($post['tamCabello']) . "',
                    Forma_Cabello   = '" . trim($post['formaCabello']) . "',
                    Frente          = '" . trim($post['Frente']) . "',
                    Cejas           = '" . trim($post['Cejas']) . "',
                    Tipo_Cejas      = '" . trim($post['tipoCejas']) . "',
                    Color_Ojos      = '" . trim($post['colorOjo']) . "',
                    Tam_Ojos        = '" . trim($post['tamOjos']) . "',
                    Forma_Ojos      = '" . trim($post['formaOjos']) . "',
                    Nariz           = '" . trim($post['Nariz']) . "',
                    Tam_Boca        = '" . trim($post['tamBoca']) . "',
                    Labios          = '" . trim($post['Labios']) . "',
                    Menton          = '" . trim($post['Menton']) . "',
                    Tam_Orejas      = '" . trim($post['tamOrejas']) . "',
                    Lobulos         = '" . trim($post['Lobulos']) . "',
                    Barba           = '" . trim($post['Barba']) . "',
                    Tam_Barba       = '" . trim($post['tamBarba']) . "',
                    Color_Barba     = '" . trim($post['colorBarba']) . "',
                    Bigote          = '" . trim($post['Bigote']) . "',
                    Tam_Bigote      = '" . trim($post['tamBigote']) . "',
                    Color_Bigote    = '" . trim($post['colorBigote']) . "'
                    WHERE Id_Media_Filiacion = '" . $id_mediaF . "'";

            $this->db->query($sql);
            $this->db->execute();



            $ConocidoSiNo = $post['infoConocido'];

            if ($ConocidoSiNo == 'Si') {
                $sql = "INSERT
                        INTO contacto_detenido(
                            No_Remision,
                            Nombre,
                            Ap_Paterno,
                            Ap_Materno,
                            Telefono,
                            Parentesco,
                            Edad,
                            Genero
                        )VALUES(
                            " . trim($post['no_remision_peticionario']) . ",
                            '" . trim($post['Nombre_conocido']) . "',
                            '" . trim($post['apaterno_conocido']) . "',
                            '" . trim($post['amaterno_conocido']) . "',
                            '" . trim($post['telefono_conocido']) . "',
                            '" . trim($post['parentezco_conocido']) . "',
                            " . trim($post['edad_conocido']) . ",
                            '" . trim($post['sexo_conocido']) . "'
                        )";

                $this->db->query($sql);
                $this->db->execute();
            }

            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $response['status'] = false;
            $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
        }
        return $response;
    }

    public function elementosParticipantes($post, $remision)
    {
        $band = true;
        try {
            $this->db->beginTransaction();
            $elementos = json_decode($post['elementos_table']);
            foreach ($elementos as $elemento) {
                $sql = " INSERT
                        INTO elemento_participante_remision(
                            No_Remision,
                            No_Control,
                            Sector_Area,
                            Nombre,
                            Tipo_Llamado,
                            Cargo
                        )
                        VALUES(
                            $remision,
                            '" . trim($elemento->row->noControl) . "',
                            '" . trim($elemento->row->grupo) . "',
                            '" . trim($elemento->row->nombre) . "',
                            b'" . trim($elemento->row->respondiente) . "',
                            '" . trim($elemento->row->cargo) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $sql = " UPDATE elemento_participante_remision
                    SET Observaciones = '" . trim($post['observacionesElementos']) . "'
                    WHERE No_Remision = $remision
                    AND Tipo_Llamado = true
            ";
            $this->db->query($sql);
            $this->db->execute();

            $this->db->commit();
        } catch (Exception $e) {
            echo "Sucedio un error " . $e;
            $band = false;
            $this->db->rollBack();
        }

        return $band;
    }

    public function elementoParticipante($respondiente, $remision)
    {
        $fullname = $respondiente['nombreElemento'].' '.$respondiente['primerApellidoElemento'].' '.$respondiente['segundoApellidoElemento'];
        try {
            $this->db->beginTransaction();
            $sql = " INSERT
                    INTO elemento_participante_remision(
                        No_Remision,
                        No_Control,
                        Sector_Area,
                        Nombre,
                        Tipo_Llamado,
                        Cargo
                    )
                    VALUES(
                        $remision,
                        '" . trim($respondiente['numControl']) . "',
                        '" . trim($respondiente['sector']) . "',
                        '" . trim($fullname) . "',
                        b'1',
                        '" . trim($respondiente['cargoElemento']) . "'
                    )
            ";
            $this->db->query($sql);
            $this->db->execute();

            $this->db->commit();
            $response['status'] = true;
        } catch (Exception $e) {
            echo "Sucedio un error " . $e;
            $response['status'] = false;
            $response['message_error'] = $e;
            $this->db->rollBack();
        }

        return $response;
    }

    public function objetosRecuperados($post, $ficha, $remision, $name_image)
    {
        $band = true;
        try {
            $this->db->beginTransaction();

            $armas = json_decode($post['armas_table']);
            foreach ($armas as $arma) {
                $sql = " INSERT
                        INTO arma_asegurada_detenido(
                            No_Remision,
                            Tipo_Arma,
                            Descripcion_Arma,
                            Cantidad
                        )
                        VALUES(
                            trim($remision),
                            '" . trim($arma->row->tipo) . "',
                            '" . trim($arma->row->descripcion) . "',
                            '" . trim($arma->row->cantidad) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $drogas = json_decode($post['drogas_table']);
            foreach ($drogas as $droga) {
                $sql = " INSERT
                        INTO droga_asegurada_detenido(
                            No_Remision,
                            Tipo_Droga,
                            Cantidad,
                            Unidad,
                            Descripcion_Droga
                        )
                        VALUES(
                            trim($remision),
                            '" . trim($droga->row->tipo) . "',
                            '" . trim($droga->row->cantidad) . "',
                            '" . trim($droga->row->unidad) . "',
                            '" . trim($droga->row->descripcion) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $objetos = json_decode($post['objetos_table']);
            foreach ($objetos as $objeto) {
                $sql = " INSERT
                        INTO objeto_asegurado_detenido(
                            No_Remision,
                            Descripcion_Objeto
                        )
                        VALUES(
                            trim($remision),
                            '" . trim(str_replace('"', '`',$objeto->row->descripcion)) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            if ($name_image != NULL) {
                $sql = " UPDATE ficha
                        SET Path_Objetos = '" . trim($name_image) . "'
                        WHERE No_Ficha = $ficha
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $this->db->commit();
        } catch (Exception $e) {
            echo "Sucedio un error " . $e;
            $band = false;
            $this->db->rollBack();
        }

        return $band;
    }

    public function fotosyhuellas($post, $remision)
    {
        $band = true;
        try {
            $this->db->beginTransaction();

            $images = json_decode($post['captura_images']);
            foreach ($images as $image) {

                $sql = " INSERT
                        INTO imagen_detenido(
                            No_Remision,
                            Tipo,
                            Perfil,
                            Path_Imagen
                        )
                        VALUES(
                            trim($remision),
                            '" . trim($image->row->tipo) . "',
                            '" . trim($image->row->perfil) . "',
                            '" . trim($image->row->name) . ".png" . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $this->db->commit();
        } catch (Exception $e) {
            echo "Sucedio un error " . $e;
            $band = false;
            $this->db->rollBack();
        }

        return $band;
    }

    public function senasParticulares($post, $remision)
    {
        $band = true;
        try {
            $this->db->beginTransaction();

            $senas = json_decode($post['senas_table']);
            foreach ($senas as $sena) {
                if ($sena->row->nameImage == null) {
                    $name = NULL;
                } else {
                    $name = $sena->row->nameImage . ".png";
                }
                $sql = " INSERT
                        INTO senia_particular_detenido(
                            No_Remision,
                            Tipo_Senia_Particular,
                            Perfil,
                            Ubicacion_Corporal,
                            Color,
                            Clasificacion,
                            Descripcion,
                            Path_Imagen
                        )
                        VALUES(
                            trim($remision),
                            '" . trim($sena->row->tipo) . "',
                            '" . trim($sena->row->perfil) . "',
                            '" . trim($sena->row->partes) . "',
                            b'" . trim($sena->row->color) . "',
                            '" . trim($sena->row->clasificacion) . "',
                            '" . trim($sena->row->descripcion) . "',
                            '" . trim($name) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $sql = " INSERT
                    INTO vestimenta_detenido(
                        No_Remision,
                        Tipo_Vestimenta,
                        Descripcion_Vestimenta
                    )
                    VALUES(
                        trim($remision),
                        '" . trim($post['tipoVestimenta']) . "',
                        '" . trim($post['descripcionVestimenta']) . "'
                    )
            ";
            $this->db->query($sql);
            $this->db->execute();

            $accesorios = json_decode($post['accesorios_table']);
            foreach ($accesorios as $accesorio) {
                $sql = " INSERT
                        INTO accesorio_detenido(
                            No_Remision,
                            Tipo_Accesorio,
                            Descripcion
                        )
                        VALUES(
                            $remision,
                            '" . trim($accesorio->row->accesorio) . "',
                            '" . trim($accesorio->row->descripcion) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $this->db->commit();
        } catch (Exception $e) {
            echo "Sucedio un error " . $e;
            $band = false;
            $this->db->rollBack();
        }

        return $band;
    }

    public function entrevistaDetenido($post, $no_remision)
    {
        $band = true;
        try {
            $this->db->beginTransaction();

            $sql = " INSERT
                    INTO entrevista_detenido(
                        No_Remision,
                        Vinculacion_Grupo_D,
                        Motivo_Delinquir,
                        Modus_Operandi
                    )
                    VALUES(
                        trim($no_remision),
                        '" . trim($post['probableVinculacion']) . "',
                        '" . trim($post['motivoDelinquir']) . "',
                        '" . trim($post['modusOperandi']) . "'
                    )
            ";
            $this->db->query($sql);
            $this->db->execute();

            $instituciones = json_decode($post['instituciones_table']);
            foreach ($instituciones as $institucion) {
                $sql = " INSERT
                        INTO institucion_seguridad_detenido(
                            No_Remision,
                            Tipo_Institucion,
                            Nombre_Institucion
                        )
                        VALUES(
                            trim($no_remision),
                            '" . trim($institucion->row->tipo) . "',
                            '" . trim($institucion->row->corporacion) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $adicciones = json_decode($post['adicciones_table']);
            foreach ($adicciones as $adiccion) {
                $roba = 0;
                if (strlen($adiccion->row->robar) > 0) {
                    $roba = 1;
                }
                $sql = " INSERT
                        INTO adiccion_detenido(
                            No_Remision,
                            Nombre_Adiccion,
                            Robo_Para_Consumo,
                            Frecuencia_Consumo,
                            Tiempo_Consumo,
                            Que_Suele_Robar
                        )
                        VALUES(
                            trim($no_remision),
                            '" . trim($adiccion->row->tipo) . "',
                            $roba,
                            '" . trim($adiccion->row->frecuencia) . "',
                            '" . trim($adiccion->row->tiempo) . "',
                            '" . trim($adiccion->row->robar) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $faltas = json_decode($post['faltasAdministrativas_table']);
            foreach ($faltas as $falta) {
                $sql = " INSERT
                        INTO falta_delito_detenido(
                            No_Remision,
                            Descripcion,
                            Fecha_FD_Detenido
                        )
                        VALUES(
                            trim($no_remision),
                            '" . trim($falta->row->descripcion) . "',
                            '" . trim($falta->row->fecha) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $antecedentes = json_decode($post['antecedentesPenales_table']);
            foreach ($antecedentes as $antecedente) {
                $sql = " INSERT
                        INTO antecedente_penal(
                            No_Remision,
                            Descripcion,
                            Fecha_Antecedente
                        )
                        VALUES(
                            trim($no_remision),
                            '" . trim($antecedente->row->descripcion) . "',
                            '" . trim($antecedente->row->fecha) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $this->db->commit();
        } catch (Exception $e) {
            echo "Sucedio un error " . $e;
            $band = false;
            $this->db->rollBack();
        }

        return $band;
    }

    public function narrativas($post, $no_ficha, $no_remision, $file)
    {
        if ($file)
            $nameFile = "IPH_" . $no_remision . ".pdf";
        $response['status'] = true;
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM peticionario_remision WHERE No_Ficha =" . $no_ficha;
            $this->db->query($sql);
            if ($this->db->register()) {
                $sql = " UPDATE peticionario_remision
                    SET Narrativa_Hechos = '" . trim($post['narrativaPeticionario']) . "'
                    WHERE No_Ficha = $no_ficha
                ";
                $this->db->query($sql);
                $this->db->execute();
            } else {
                $response['Peticionario'] = false;
            }

            $sql = "SELECT * FROM elemento_participante_remision WHERE No_Remision = $no_remision AND Tipo_Llamado = true";
            $this->db->query($sql);
            if ($this->db->register()) {
                $sql = " UPDATE elemento_participante_remision
                    SET Narrativa_Hechos = '" . trim($post['narrativaElementos']) . "'
                    WHERE No_Remision = $no_remision
                    AND Tipo_Llamado = true
                ";
                $this->db->query($sql);
                $this->db->execute();
            } else {
                $response['Elemento'] = false;
            }

            $sql = "SELECT * FROM entrevista_detenido WHERE No_Remision = $no_remision";
            $this->db->query($sql);
            if ($this->db->register()) {
                $sql = " UPDATE entrevista_detenido
                    SET Narrativa_Hechos = '" . trim(str_replace('"', '`',$post['narrativaDetenido'])) . "'
                    WHERE No_Remision = $no_remision
                ";
            } else {
                $sql = " INSERT
                    INTO entrevista_detenido(
                        No_Remision,
                        Narrativa_Hechos
                    )
                    VALUES(
                        $no_remision,
                        '" . trim(str_replace('"', '`',$post['narrativaDetenido'])) . "'
                    )
                ";
            }

            $this->db->query($sql);
            $this->db->execute();

            if ($file) {
                $sql = " UPDATE detenido
                    SET Extracto_IPH = '" . trim($post['extractoIPH']) . "', Path_IPH = '" . trim($nameFile) . "', CDI = '" . trim($post['cdiFolioJC']) . "'
                    WHERE No_Remision = $no_remision
                ";
            } else {
                $sql = " UPDATE detenido
                    SET Extracto_IPH = '" . trim($post['extractoIPH']) . "', CDI = '" . trim($post['cdiFolioJC']) . "'
                    WHERE No_Remision = $no_remision
                ";
            }
            $this->db->query($sql);
            $this->db->execute();

            $this->db->commit();
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = $e;
            $this->db->rollBack();
        }

        return $response;
    }
/*--------------------------FIN DE INSERTS--------------------------*/

/*-----------------------FUNCIONES DE UPDATE-----------------------*/
    public function updatePrincipales($post)
    {
        $response['status'] = true;

        try {
            /*no hay un orden específico*/

            $this->db->beginTransaction(); //transaction para evitar errores de inserción

            //actualizando domicilio
            // $sql = "SELECT Id_Municipio FROM municipio WHERE Nombre = '" . $post['Municipio'] . "'";
            // $this->db->query($sql);
            // $id_municipio = $this->db->register()->Id_Municipio;
            $sql = "    UPDATE domicilio
                            SET Municipio = '" . trim($post['Municipio']) . "',
                                Colonia = '" . trim($post['Colonia']) . "',
                                Calle = '" . trim($post['Calle']) . "',
                                No_Exterior = '" . trim($post['noExterior']) . "',
                                No_Interior = '" . trim($post['noInterior']) . "',
                                Coordenada_X = '" . trim($post['cordX']) . "',
                                Coordenada_Y = '" . trim($post['cordY']) . "',
                                Estado = '" . trim($post['Estado']) . "',
                                CP           = '" . trim($post['CP']) . "'
                            WHERE Id_Domicilio = " . trim($post['Id_Domicilio']) . "
                            ";
            $this->db->query($sql);
            $this->db->execute();

            //detenido
            //campos adicionales
            $alcoholemia    = (isset($post['alcoholemia_principales'])) ? '1' : '0';
            $verificado_CURP = (isset($post['verificado_principales'])) ? '1' : '0';
            $RFC            = (isset($post['RFC_principales'])) ? $post['RFC_principales'] : '';
            $correo         = (isset($post['correo_principales'])) ? $post['correo_principales'] : '';
            $ocupacion      = (isset($post['Ocupacion_principales'])) ? $post['Ocupacion_principales'] : '';
            $facebook       = (isset($post['Facebook_principales'])) ? $post['Facebook_principales'] : '';
            $edo_civil      = (isset($post['edoCivil_principales'])) ? $post['edoCivil_principales'] : '';
            $telefono       = (isset($post['Telefono_principales'])) ? $post['Telefono_principales'] : '';
            $imei1          = (isset($post['imei_1_principales'])) ? $post['imei_1_principales'] : '';
            $imei2          = (isset($post['imei_2_principales'])) ? $post['imei_2_principales'] : '';

            $sql = "    UPDATE detenido
                            SET Nombre              = '" . trim($post['Nombre_principales']) . "',
                                Ap_Paterno          = '" . trim($post['appPaterno_principales']) . "',
                                Ap_Materno          = '" . trim($post['appMaterno_principales']) . "',
                                Genero              = '" . trim($post['sexo_principales']) . "',
                                Edad                = '" . trim($post['edad_principales']) . "',
                                Escolaridad         = '" . trim($post['escolaridad_principales']) . "',
                                Id_Domicilio        = " .  trim($post['Id_Domicilio']) . ",
                                Lugar_Origen        = '" . trim($post['procedencia_principales']) . "',
                                Fecha_Nacimiento    = '" . trim($post['FechaNacimiento_principales']) . "',
                                Estado_Civil        = '" . trim($edo_civil) . "',
                                Telefono            = '" . trim($telefono) . "',
                                Imei1               = '" . trim($imei1) . "',
                                Imei2               = '" . trim($imei2) . "',
                                CURP                = '" . trim($post['CURP_principales']) . "',
                                Verificacion_CURP   = b'". trim($verificado_CURP) . "',
                                Alcoholemia         = b'". trim($alcoholemia) . "',
                                RFC                 = '" . trim($RFC) . "',
                                Correo_Electronico  = '" . trim($correo) . "',
                                Facebook            = '" . trim($facebook) . "',
                                Ocupacion           = '" . trim($ocupacion) . "',
                                Pertenencias_Detenido ='". trim($post['pertenencias_rem']) . "'
                            WHERE   No_Remision = " . $post['no_remision_principales'] . "     
                            ";
            $this->db->query($sql);
            $this->db->execute();

            //remisión
            // if ($post['tipoFicha'] == 'Policia')
            //     $Zona_Sector = $post['zona'];
            // else
            //     $Zona_Sector = $post['sector'];

            $cancelar   = (isset($post['cancelar_ficha'])) ? 'cancelada' : '';
            $no_f       = $post['no_ficha_principales'];
        
            $sql = " UPDATE ficha
                    SET Estatus = '" . trim($cancelar) . "'
                    WHERE No_Ficha = '" . trim($no_f) . "'
            ";
            $this->db->query($sql);
            $this->db->execute();
            $can= ($cancelar=='cancelada')? '0' : '1';
            
            if($post['estado_ficha_original']!=$cancelar){
                //Añadido para permitir cancelar fichas y remisiones por separado
                $sql = " UPDATE remision
                    SET Status_Remision = '" .$can . "'
                    WHERE No_Ficha = '" . trim($no_f) . "'
                ";
                $this->db->query($sql);
                $this->db->execute();
            }
            $sql = " UPDATE remision
                SET Status_Remision = '" .trim($post['remision_cancelada']) . "'
                WHERE No_Remision = '" . trim($post['no_remision_principales']) . "'
            ";
            $this->db->query($sql);
            $this->db->execute();

            $Zona_Sector = $post['zona'];

            $sql = "    UPDATE remision
                            SET Fecha_Hora      = '" . trim($post['fecha_principales']) . " " . trim($post['hora_principales']) . "',
                                Folio_911       = '" . trim($post['911_principales']) . "',
                                Grupo           = '" . trim($post['tipoFicha']) . "',
                                Zona_Sector     = '" . trim($Zona_Sector) . "',
                                Instancia       = '" . trim($post['Remitido']) . "',
                                Cia             = '" . trim($post['CIA_principales']) . "'
                            WHERE No_Remision = " . trim($post['no_remision_principales']) . "
                        ";
            $this->db->query($sql);
            $this->db->execute();

            //alias
            $sql = "    UPDATE alias_detenido
                            SET Nombre = '" . trim($post['Alias']) . "'
                            WHERE No_Remision = " . $post['no_remision_principales'] . "
                        ";
            $this->db->query($sql);
            $this->db->execute();

            $this->updateNoDetentidoByFicha($post['no_ficha_principales']);

            $this->db->commit(); //si todo sale bien se realiza los commits

        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = $e;
            $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
        }
        return $response;
    }


    public function updatePeticionario($post)
    {

        /*sin orden especifico, a menos que sea insert*/

        $response['status'] = true;

        try {

            $this->db->beginTransaction();

            $sexoAux = (isset($post['peticionario_Sexo']) ? $post['peticionario_Sexo'] : '');
            $escolaridadAux = (isset($post['peticionario_Escolaridad']) ? $post['peticionario_Escolaridad'] : '');
            //comprobar si existe peticionario
            $sql = "SELECT * FROM peticionario_remision WHERE No_Ficha = " . $post['no_ficha_peticionario'];
            $this->db->query($sql);
            $aux = $this->db->register();
            if ($aux) { //el peticionario si existe
                //obtenemos el id_municipio
                // $sql = "SELECT Id_Municipio FROM municipio WHERE Nombre = '" . $post['Municipio_peticionario'] . "'";
                // $this->db->query($sql);
                // $id_municipio = $this->db->register()->Id_Municipio;
                //actualizamos el domicilio
                $sql = "UPDATE domicilio
                            SET Colonia = '" . trim($post['Colonia_peticionario']) . "',
                                Calle = '" . trim($post['Calle_peticionario']) . "',
                                No_Exterior = '" . trim($post['noExterior_peticionario']) . "',
                                No_Interior = '" . trim($post['noInterior_peticionario']) . "',
                                Coordenada_X = '" . trim($post['cordX_peticionario']) . "',
                                Coordenada_Y = '" . trim($post['cordY_peticionario']) . "',
                                CP = '" . trim($post['CP_peticionario']) . "',
                                Municipio = '" . trim($post['Municipio_peticionario']) . "'
                            WHERE Id_Domicilio = " . $aux->Id_Domicilio . "
                            ";
                $this->db->query($sql);
                $this->db->execute();

                //actualizamos peticionario
                $sql =  "   UPDATE peticionario_remision
                                SET Nombre = '" . trim($post['peticionario_Nombres']) . "',
                                    Ap_Paterno = '" . trim($post['peticionario_appPaterno']) . "',
                                    Ap_Materno = '" . trim($post['peticionario_appMaterno']) . "',
                                    Fecha_Nacimiento = '" . trim($post['peticionario_Fecha_n']) . "',
                                    Genero = '" .  trim($sexoAux) . "',
                                    Edad = '" . trim($post['peticionario_Edad']) . "',
                                    Escolaridad = '" . trim($escolaridadAux) . "',
                                    Lugar_Origen = '" . trim($post['peticionario_Procedencia']) . "'
                                WHERE No_Ficha = " . trim($post['no_ficha_peticionario']) . "
                            ";
                $this->db->query($sql);
                $this->db->execute();
            } else { //no existe, se debe crear uno nuevo
                //insertando en domicilio (obteniendo primero el id del municipio)
                // $sql = "SELECT Id_Municipio FROM municipio WHERE Nombre = '" . $post['Municipio_peticionario'] . "'";
                // $this->db->query($sql);
                // $id_municipio = $this->db->register()->Id_Municipio;
                $sql = "    INSERT 
                                INTO domicilio(
                                    Municipio,
                                    Colonia,
                                    Calle,
                                    No_Exterior,
                                    No_Interior,
                                    Coordenada_X,
                                    Coordenada_Y,
                                    CP
                                )
                                VALUES(
                                    '" . trim($post['Municipio_peticionario']) . "',
                                    '" . trim($post['Colonia_peticionario']) . "',
                                    '" . trim($post['Calle_peticionario']) . "',
                                    '" . trim($post['noExterior_peticionario']) . "',
                                    '" . trim($post['noInterior_peticionario']) . "',
                                    '" . trim($post['cordX_peticionario']) . "',
                                    '" . trim($post['cordY_peticionario']) . "',
                                    '" . trim($post['CP_peticionario']) . "'
                                )";
                $this->db->query($sql);
                $this->db->execute();
                $this->db->query("SELECT LAST_INSERT_ID() as Id_Domicilio"); //se recupera el id de domicilio creado recientemente
                $id_domicilio = $this->db->register()->Id_Domicilio;

                //Inserción del peticionario
                $sql =  "   INSERT
                                INTO peticionario_remision(
                                    No_Ficha,
                                    Nombre,
                                    Ap_Paterno,
                                    Ap_Materno,
                                    Fecha_Nacimiento,
                                    Genero,
                                    Edad,
                                    Escolaridad,
                                    Id_Domicilio,
                                    Lugar_Origen
                                )
                                VALUES(
                                    '" . trim($post['no_ficha_peticionario']) . "',
                                    '" . trim($post['peticionario_Nombres']) . "',
                                    '" . trim($post['peticionario_appPaterno']) . "',
                                    '" . trim($post['peticionario_appMaterno']) . "',
                                    '" . trim($post['peticionario_Fecha_n']) . "',
                                    '" . trim($sexoAux) . "',
                                    '" . trim($post['peticionario_Edad']) . "',
                                    '" . trim($escolaridadAux) . "',
                                    '" . trim($id_domicilio) . "',
                                    '" . trim($post['peticionario_Procedencia']) . "'
                                )";

                $this->db->query($sql);
                $this->db->execute();
            }


            $this->db->commit();
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = $e;
            $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
        }
        return $response;
    }

    public function updateUbicacionH($post)
    {
        /*sin orden especifico, a menos que sea insert*/
        $response['status'] = true;
        try {
            $this->db->beginTransaction();
            //comprobar si existe registro de ubicacion de los hechos
            $sql = "SELECT * FROM ubicacion_hechos_remision WHERE No_Ficha = " . $post['no_ficha_ubicacionHechos'];
            $this->db->query($sql);

            $zona = (isset($post['ZonaUH']) ? $post['ZonaUH'] : '');
            $vector = (isset($post['VectorUH']) ? $post['VectorUH'] : '');

            if ($this->db->register()) { //el registro si existe
                //echo "Si existe un registro";
                $sql = "UPDATE ubicacion
                        SET Calle_1         = '" . trim($post['Calle_hechos']) . "',      
                            Calle_2         = '" . trim($post['Calle2_hechos']) . "',
                            No_Ext          = '" . trim($post['noExterior_hechos']) . "',
                            No_Int          = '" . trim($post['noInterior_hechos']) . "',
                            Colonia         = '" . trim($post['Colonia_hechos']) . "',
                            Coordenada_X    = '" . trim($post['cordX_hechos']) . "',
                            Coordenada_Y    = '" . trim($post['cordY_hechos']) . "',
                            CP              = '" . trim($post['CP_hechos']) . "', 
                            Fraccionamiento = '" . trim($post['Fraccionamiento_hechos']) . "'
                        WHERE Id_Ubicacion =" . $post['Id_Ubicacion'];
                $this->db->query($sql);
                $this->db->execute();

                //se actualiza hora reporte y no_participantes
                $sql = "UPDATE ubicacion_hechos_remision
                        SET No_Participantes    = '" . $post['participantes_hechos'] . "',
                            Hora_Reporte        = '" . $post['hora_hechos'] . "',
                            Vector = '" . $vector . "'
                        WHERE No_Ficha = " . $post['no_ficha_ubicacionHechos'];

                $this->db->query($sql);
                $this->db->execute();
            } else { //no existe, se debe crear uno nuevo
                //echo 'NO existe';
                //-----------------------------------------------
                $sql = "INSERT
                    INTO ubicacion(
                        Calle_1,
                        Calle_2,
                        No_Ext,
                        No_Int,
                        Colonia,
                        Coordenada_X,
                        Coordenada_Y,
                        CP,
                        Fraccionamiento
                    )
                    VALUES(
                        '" . trim($post['Calle_hechos']) . "',
                        '" . trim($post['Calle2_hechos']) . "',
                        '" . trim($post['noExterior_hechos']) . "',
                        '" . trim($post['noInterior_hechos']) . "',
                        '" . trim($post['Colonia_hechos']) . "',
                        '" . trim($post['cordX_hechos']) . "',
                        '" . trim($post['cordY_hechos']) . "',
                        '" . trim($post['CP_hechos']) . "',
                        '" . trim($post['Fraccionamiento_hechos']) . "'
                        
                    )";

                $this->db->query($sql);
                $this->db->execute();
                $this->db->query("SELECT LAST_INSERT_ID() as Id_Ubicacion"); //se recupera el id de la ubicacion creada recientemente
                $id_ubicacion = $this->db->register()->Id_Ubicacion;

                //echo $id_ubicacion;

                $no_ficha =  $post['no_ficha_ubicacionHechos'];
                $no_remision = $post['no_remision_ubicacionHechos'];


                $sql = "INSERT 
                    INTO ubicacion_hechos_remision(
                        No_Ficha,
                        Id_Ubicacion,
                        No_Participantes,
                        Hora_Reporte, 
                        Vector
                    )
                    VALUES(
                        " . trim($no_ficha) . ",
                        " . trim($id_ubicacion) . ",
                        '" . trim($post['participantes_hechos']) . "',
                        '" . trim($post['hora_hechos']) . "',
                        '" . trim($vector ). "'
                )";

                $this->db->query($sql);
                $this->db->execute();
                //-----------------------------------------------
            }
            //actualizar Remitido por...
            $sql = "UPDATE remision SET 
                        Falta_Delito_Tipo = '" . trim($post['Falta_Delito_Tipo']) . "' , 
                        Remitido_Por = '" . trim($post['RemitidoPor']) . "' 
                    WHERE No_Remision = " . $post['no_remision_ubicacionHechos'];
            $this->db->query($sql);
            $this->db->execute();

            //insertando en falta_delito_detenido_uh
            if (isset($post['delito_1'])) {
                //se borran las faltas y delitos anteriores por facilidad
                $this->db->query("DELETE FROM falta_delito_detenido_uh WHERE No_Remision = " . $post['no_remision_ubicacionHechos']);
                $this->db->execute();


                $faltas_delitos = json_decode($post['delito_1']);
                foreach ($faltas_delitos as $falta_delito) {
                    $sql = "";
                    switch ($falta_delito->faltaDelito) {
                        case 'ROBO A COMERCIO':
                            $sql = "INSERT INTO falta_delito_detenido_uh (
                                                No_Remision,
                                                Descripcion,
                                                Negocio_Afectado
                                                )
                                            VALUES( " . trim($post['no_remision_ubicacionHechos']) . ",
                                                    '" . trim($falta_delito->faltaDelito) . "',
                                                    '" . trim($falta_delito->comercio) . "')";
                            break;

                        case 'ROBO A TRANSPORTE PUBLICO':
                            $sql = "INSERT INTO falta_delito_detenido_uh (
                                No_Remision,
                                Descripcion,
                                Ruta_Afectada,
                                Unidad_Ruta_Afectada
                                )
                            VALUES( " . trim($post['no_remision_ubicacionHechos']) . ",
                                    '" . trim($falta_delito->faltaDelito) . "',
                                    '" . trim($falta_delito->ruta) . "',
                                    '" . trim($falta_delito->unidad) . "')";
                            break;

                        default:
                            $sql = "INSERT INTO falta_delito_detenido_uh (
                                No_Remision,
                                Descripcion
                                )
                            VALUES( " . trim($post['no_remision_ubicacionHechos']) . ",
                                    '" . trim($falta_delito->faltaDelito) . "')";
                            break;
                    }
                    $this->db->query($sql);
                    $this->db->execute();
                }
            }

            $this->db->commit();
        } catch (Exception $e) {
            //echo "Ficha: $no_ficha - municipio $id_municipio - domicilio $id_domicilio - media $id_media_filiacion - Remision: $no_remision -user: $id_usuario - Error DB: ".$e;
            $response['status'] = false;
            $response['error_message'] = $e;
            $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
        }
        return $response;
    }

    public function updateUbicacionD($post)
    {
        $response['status'] = true;
        try {
            $this->db->beginTransaction();
            //comprobar si existe registro de ubicacion de los hechos
            $sql = "SELECT * FROM ubicacion_detencion WHERE No_Remision = " . $post['no_remision_detencion'];
            $this->db->query($sql);

            $zona = (isset( $post['ZonaUD'] )) ? $post['ZonaUD'] : '';

            if ($this->db->register()) { //el registro si existe
                //echo "Si existe";
               
                $sql = "UPDATE ubicacion
                        SET Calle_1         = '" . trim($post['Calle_1_detencion']) . "',
                            Calle_2         = '" . trim($post['Calle_2_detencion']) . "',
                            No_Ext          = '" . trim($post['noExterior_detencion']) . "',
                            No_Int          = '" . trim($post['noInterior_detencion']) . "',
                            Colonia         = '" . trim($post['Colonia_detencion']) . "',
                            Coordenada_X    = '" . trim($post['cordX_detencion']) . "',
                            Coordenada_Y    = '" . trim($post['cordY_detencion']) . "',
                            CP              = '" . trim($post['CP_detencion']) . "',
                            Fraccionamiento = '" . trim($post['Fraccionamiento_detencion']) . "'
                        WHERE Id_Ubicacion = " . $post['Id_Ubicacion'];
                $this->db->query($sql);
                $this->db->execute();

                $sql = "UPDATE ubicacion_detencion
                        SET Forma_Detencion         = '" . trim($post['modalidadDetencion']) . "',
                            Tipo_Violencia          = '" . trim($post['tipoViolencia']) . "',
                            Fecha_Hora_Detencion    = '" . trim($post['fecha_detencion']) . " " . trim($post['hora_detencion']) . "',
                            Descripcion_Forma_Detencion = '" . trim($post['modalidadSelectDetencion']) . "',
                            Observaciones           = '" . trim($post['observaciones_detencion']) . "',
                            Zona                    = '". $zona ."'
                        WHERE No_Remision =" . $post['no_remision_detencion'];
                $this->db->query($sql);
                $this->db->execute();
            } else { //El registro no existe y se debe de insertar a la base de datos
                $sql = "INSERT
                INTO ubicacion(
                    Calle_1,
                    Calle_2,
                    No_Ext,
                    No_Int,
                    Colonia,
                    Coordenada_X,
                    Coordenada_Y, 
                    CP,
                    Fraccionamiento
                )
                VALUES(
                    '" . trim($post['Calle_1_detencion']) . "',
                    '" . trim($post['Calle_2_detencion']) . "',
                    '" . trim($post['noExterior_detencion']) . "',
                    '" . trim($post['noInterior_detencion']) . "',
                    '" . trim($post['Colonia_detencion']) . "',
                    '" . trim($post['cordX_detencion']) . "',
                    '" . trim($post['cordY_detencion']) . "',
                    '" . trim($post['CP_detencion']) . "',
                    '" . trim($post['Fraccionamiento_detencion']) . "'
                )";

                $this->db->query($sql);
                $this->db->execute();
                $this->db->query("SELECT LAST_INSERT_ID() as Id_Ubicacion"); //se recupera el id de la ubicacion creada recientemente
                $id_ubicacion = $this->db->register()->Id_Ubicacion;

                if ($post['Id_Ubicacion'] == 'undefined')
                    $post['Id_Ubicacion'] = $id_ubicacion;

                $no_remision = $post['no_remision_detencion'];

                /*
                        $sql = "UPDATE ubicacion_detencion
                        SET Forma_Detencion         = '" . $post['modalidadDetencion'] . "',
                            Tipo_Violencia          = '" . $post['tipoViolencia'] . "',
                            Fecha_Hora_Detencion    = '" . $post['fecha_detencion'] . " " . $post['hora_detencion'] . "',
                            Descripcion_Forma_Detencion = '" . $post['modalidadSelectDetencion'] . "',
                            Observaciones           = '" . $post['observaciones_detencion'] . "'
                        WHERE No_Remision =" . $post['no_remision_detencion'];
                
                */

                $sql = "INSERT
                        INTO ubicacion_detencion(
                            No_Remision,
                            Id_Ubicacion,
                            Forma_Detencion,
                            Tipo_Violencia,
                            Descripcion_Forma_Detencion,
                            Fecha_Hora_Detencion,
                            Observaciones,
                            Zona            
                        )VALUES(
                            " . trim($no_remision) . ",
                            " . trim($id_ubicacion) . ",
                            '" . trim($post['modalidadDetencion'] ). "',
                            '" . trim($post['tipoViolencia']) . "',
                            '" . trim($post['modalidadSelectDetencion']) . "',
                            '" . trim($post['fecha_detencion']) . " " . trim($post['hora_detencion']) . "',
                            '" . trim($post['observaciones_detencion']) . "',
                            '". $zona ."'
                        )";

                $this->db->query($sql);
                $this->db->execute();
            }



            $this->db->commit();
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = $e;
            //echo "Ficha: $no_ficha - municipio $id_municipio - domicilio $id_domicilio - media $id_media_filiacion - Remision: $no_remision -user: $id_usuario - Error DB: ".$e;
            // $response['status'] = false;
            $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
        }
        return $response;
    }

    /*Se modifico esta funcion para que realice solo la actualizacion de la media filiacion
    manteniendo la actualizacion de los contactos en otra funcion*/
    public function updateMediaF($post)
    {
        $response['status'] = true;
        try {
            $this->db->beginTransaction();
            $this->db->query("SELECT Id_Media_Filiacion AS id_mediaF from detenido where No_Remision = '" . $post['no_remision_mediaFiliacion'] . "'");
            $id_mediaF = $this->db->register()->id_mediaF;
            $sql = "UPDATE media_filiacion 
                SET Complexion      = '" . trim($post['Complexion']) . "',
                    Estatura_cm     = " .  trim($post['Estarura']) . ",
                    Color_Piel      = '" . trim($post['Color_p']) . "',
                    Forma_Cara      = '" . trim($post['formaCara']) . "',
                    Pomulos         = '" . trim($post['Pomulos']) . "',
                    Cabello         = '" . trim($post['Cabello']) . "',
                    Color_Cabello   = '" . trim($post['colorCabello']) . "',
                    Tam_Cabello     = '" . trim($post['tamCabello']) . "',
                    Forma_Cabello   = '" . trim($post['formaCabello']) . "',
                    Frente          = '" . trim($post['Frente']) . "',
                    Cejas           = '" . trim($post['Cejas']) . "',
                    Tipo_Cejas      = '" . trim($post['tipoCejas']) . "',
                    Color_Ojos      = '" . trim($post['colorOjo']) . "',
                    Tam_Ojos        = '" . trim($post['tamOjos']) . "',
                    Forma_Ojos      = '" . trim($post['formaOjos']) . "',
                    Nariz           = '" . trim($post['Nariz']) . "',
                    Tam_Boca        = '" . trim($post['tamBoca']) . "',
                    Labios          = '" . trim($post['Labios']) . "',
                    Menton          = '" . trim($post['Menton']) . "',
                    Tam_Orejas      = '" . trim($post['tamOrejas']) . "',
                    Lobulos         = '" . trim($post['Lobulos']) . "',
                    Barba           = '" . trim($post['Barba']) . "',
                    Tam_Barba       = '" . trim($post['tamBarba']) . "',
                    Color_Barba     = '" . trim($post['colorBarba']) . "',
                    Bigote          = '" . trim($post['Bigote']) . "',
                    Tam_Bigote      = '" . trim($post['tamBigote']) . "',
                    Color_Bigote    = '" . trim($post['colorBigote']) . "'
                WHERE Id_Media_Filiacion = '" . $id_mediaF . "'";

            $this->db->query($sql);
            $this->db->execute();
            $this->db->commit();
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = $e;
            // echo $e;
            //echo "Ficha: $no_ficha - municipio $id_municipio - domicilio $id_domicilio - media $id_media_filiacion - Remision: $no_remision -user: $id_usuario - Error DB: ".$e;
            $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
        }
        return $response;
    }
    /*Se creo esta nueva funcion para mantener separada la media filiacion
    de los contactos conocidos*/
    public function updateContactosConocidos($post)
    {
        $response['status'] = true;
        try {
            $this->db->beginTransaction();
            $sql = "DELETE FROM contacto_detenido  WHERE No_Remision =" .trim($post['no_remision_mediaFiliacion']);
            $this->db->query($sql);
            $this->db->execute();
            $contactos = json_decode($post['contactos_table']);
            foreach ($contactos as $contacto) {
                if(($contacto->row->sexo)=="HOMBRE"){
                    $tipo="h";
                }
                else{
                    $tipo="m";
                }
                $sql = "INSERT
                        INTO contacto_detenido(
                            No_Remision,
                            Nombre,
                            Ap_Paterno,
                            Ap_Materno,
                            Telefono,
                            Parentesco,
                            Edad,
                            Genero
                        )VALUES(
                            " .  trim($post['no_remision_mediaFiliacion']) . ",
                            '" . trim($contacto->row->nombres) . "',
                            '" . trim($contacto->row->apellido_p) . "',
                            '" . trim($contacto->row->apellido_m) . "',
                            '" . trim($contacto->row->telefono) . "',
                            '" . trim($contacto->row->tipo_relacion) . "',
                            " .  trim($contacto->row->edad) . ",
                            '" . trim($tipo) . "'
                        )";
                
                $this->db->query($sql);
                $this->db->execute();
            }
            $this->db->commit();
            $response['status'] = true;

        }catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = $e;
            $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
            }
            return $response;
    }

    public function updateElementosParticipantes($post, $remision)
    {

        $response['status'] = true;
        try {
            $this->db->beginTransaction();

            $sql = "DELETE FROM elemento_participante_remision WHERE No_Remision =" . $remision;
            $this->db->query($sql);
            $this->db->execute();

            $elementos = json_decode($post['elementos_table']);
            foreach ($elementos as $elemento) {
                $sql = " INSERT
                        INTO elemento_participante_remision(
                            No_Remision,
                            No_Control,
                            Placa,
                            Sector_Area,
                            Nombre,
                            Tipo_Llamado,
                            Cargo,
                            No_Unidad,
                            En_Colaboracion,
                            Seguimiento_GPS
                        )
                        VALUES(
                            $remision,
                            '" . trim($elemento->row->noControl) . "',
                            '" . trim($elemento->row->placa) . "',
                            '" . trim($elemento->row->grupo) . "',
                            '" . trim($elemento->row->nombre) . "',
                            b'". trim($elemento->row->respondiente) . "',
                            '" . trim($elemento->row->cargo) . "',
                            '" . trim($elemento->row->unidad) . "',
                            b'". trim($elemento->row->enColaboracion) . "',
                            b'". trim($elemento->row->segGPS) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $sql = " UPDATE elemento_participante_remision
                    SET Observaciones = '" . trim($post['observacionesElementos']) . "', Narrativa_Hechos = '" . trim($post['narrativaElementos']) . "'
                    WHERE No_Remision = $remision
                    AND Tipo_Llamado = true
            ";
            $this->db->query($sql);
            $this->db->execute();

            $this->db->commit();
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = $e;
            $this->db->rollBack();
        }

        return $response;
    }

    public function updateObjRecuperados($post, $remision, $ficha)
    {
        try {
            $this->db->beginTransaction();

            $sql = "DELETE FROM arma_asegurada_detenido WHERE No_Remision =" . $remision;
            $this->db->query($sql);
            $this->db->execute();

            $armas = json_decode($post['armas_table']);
            foreach ($armas as $arma) {
                $sql = " INSERT
                        INTO arma_asegurada_detenido(
                            No_Remision,
                            Tipo_Arma,
                            Descripcion_Arma,
                            Cantidad
                        )
                        VALUES(
                            trim($remision),
                            '" . trim($arma->row->tipo) . "',
                            '" . trim($arma->row->descripcion) . "',
                            '" . trim($arma->row->cantidad) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $sql = "DELETE FROM droga_asegurada_detenido WHERE No_Remision =" . $remision;
            $this->db->query($sql);
            $this->db->execute();

            $drogas = json_decode($post['drogas_table']);
            foreach ($drogas as $droga) {
                $sql = " INSERT
                        INTO droga_asegurada_detenido(
                            No_Remision,
                            Tipo_Droga,
                            Cantidad,
                            Unidad,
                            Descripcion_Droga
                        )
                        VALUES(
                            trim($remision),
                            '" . trim($droga->row->tipo) . "',
                            '" . trim($droga->row->cantidad) . "',
                            '" . trim($droga->row->unidad) . "',
                            '" . trim($droga->row->descripcion) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $sql = "DELETE FROM objeto_asegurado_detenido WHERE No_Remision =" . $remision;
            $this->db->query($sql);
            $this->db->execute();

            $objetos = json_decode($post['objetos_table']);
            foreach ($objetos as $objeto) {
                $sql = " INSERT
                        INTO objeto_asegurado_detenido(
                            No_Remision,
                            Descripcion_Objeto
                        )
                        VALUES(
                            trim($remision),
                            '" . trim(str_replace('"', '`',$objeto->row->descripcion)) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }
            /*se cambio la forma en que se actualizaba la tabla de la base de datos, ya que
            como en las tablas ya hechas, se eliminan los registros y se añaden
            los nuevos leidos de la tabla de vehiculos*/
            $sql = "DELETE FROM vehiculo_remision WHERE No_Remision =" . $remision;
            $this->db->query($sql);
            $this->db->execute();

            $vehiculos = json_decode($post['vehiculos_table']);
            foreach ($vehiculos as $vehiculo) {
                if(($vehiculo->row->situacion)=="Recuperado"){
                    $tipo=0;
                }
                else{
                    $tipo=1;
                }
                $sql = " INSERT
                        INTO vehiculo_remision(
                            No_Remision,
                            Tipo_Situacion,
                            Placa_Vehiculo,
                            Marca,
                            Submarca,
                            Tipo_Vehiculo,
                            Modelo,
                            Color,
                            Senia_particular,
                            Procedencia_Vehiculo,
                            Observacion_Vehiculo,
                            No_serie
                        )
                        VALUES ($remision,
                            b'". trim($tipo) . "',
                            '" . trim($vehiculo->row->placa) . "',
                            '" . trim($vehiculo->row->marca) . "',
                            '" . trim($vehiculo->row->submarca) . "',
                            '" . trim($vehiculo->row->tipo) . "',
                            '" . trim($vehiculo->row->modelo) . "',
                            '" . trim($vehiculo->row->color) . "',
                            '" . trim($vehiculo->row->senia) . "',
                            '" . trim($vehiculo->row->procedencia) . "',
                            '" . trim($vehiculo->row->observaciones) . "',
                            '" . trim($vehiculo->row->num_serie) . "'
                        )
                ";
            
                $this->db->query($sql);
                $this->db->execute();
            }
            $this->db->commit();
            $response['status'] = true;
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = 'Error al insertar en la base de datos';
            $this->db->rollBack();
        }

        return $response;
    }

    public function updateImgObjRecuperados($ficha, $name_image)
    {
        $response['status'] = true;
        try {
            $this->db->beginTransaction();

            $sql = " UPDATE ficha
                    SET Path_Objetos = '" . trim($name_image) . "'
                    WHERE No_Ficha = $ficha
            ";
            $this->db->query($sql);
            $this->db->execute();
            $this->db->commit();
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = $e;
            $this->db->rollBack();
        }
        return $response;
    }

    public function updateNameFile($tipo, $perfil, $name, $remision)
    {
        try {
            $this->db->beginTransaction();

            $sql = "UPDATE imagen_detenido SET Path_Imagen = '" . trim($name) . "' WHERE No_Remision = " . $remision . " AND Tipo = '" . $tipo . "' AND Perfil = '" . $perfil . "'";
            $this->db->query($sql);
            $response['status'] = true;
            $this->db->execute();

            $this->db->commit();
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = $e;
            $this->db->rollBack();
        }

        return $response;
    }

    public function updateFotosyhuellas($post, $remision, $name)
    {
        $response['status'] = true;
        $date = date("Ymdhis");
        $perfil = explode('_', $post['perfil']);
        $name = $name . "?v=" . $date;
        $response['nameFile'] = $name;
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM imagen_detenido WHERE No_Remision = " . $remision . " AND Tipo = '" . $perfil[0] . "' AND Perfil = '" . $perfil[1] . "'";
            $this->db->query($sql);
            if ($this->db->register()) {
                $sql = "UPDATE imagen_detenido SET Path_Imagen = '" . trim($name) . "' WHERE No_Remision = " . $remision . " AND Tipo = '" . $perfil[0] . "' AND Perfil = '" . $perfil[1] . "'";
                $this->db->query($sql);
                $response['status'] = true;
                $this->db->execute();
            } else {
                $sql = "INSERT INTO imagen_detenido(
                    No_Remision,
                    Tipo,
                    Perfil,
                    Path_Imagen
                )
                VALUES(
                    trim($remision),
                    '" . trim($perfil[0]) . "',
                    '" . trim($perfil[1]) . "',
                    '" . trim($name) . "'
                )";
                $this->db->query($sql);
                $this->db->execute();

                $response['status'] = true;
            }

            $this->db->commit();
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = $e;
            $this->db->rollBack();
        }

        return $response;
    }

    public function deleteFotosHuellas($remision, $perfil)
    {
        $perfil = explode('_', $perfil);
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM imagen_detenido WHERE No_Remision = " . $remision . " AND Tipo = '" . $perfil[0] . "' AND Perfil = '" . $perfil[1] . "'";
            $this->db->query($sql);
            if ($this->db->register()) {
                $id = $this->db->register()->Id_Imagen_R;
                $sql = "DELETE FROM imagen_detenido WHERE Id_Imagen_R = " . $id;
                $this->db->query($sql);
                $this->db->execute();

                $response['status'] = true;
            } else {
                $response['status'] = false;
                $response['error_message'] = 'No se encontro registro con estas caracteristicas';
            }

            $this->db->commit();
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = $e;
            $this->db->rollBack();
        }

        return $response;
    }

    public function updateSenasParticulares($post, $remision)
    {
        $response['status'] = true;
        $date = date("Ymdhis");
        try {
            $this->db->beginTransaction();

            $sql = "DELETE FROM senia_particular_detenido WHERE No_Remision =" . $remision;
            $this->db->query($sql);
            $this->db->execute();

            $senas = json_decode($post['senas_table']);
            foreach ($senas as $sena) {
                $name = '';
                if ($sena->row->typeImage == 'File') {
                    $type = $_FILES[$sena->row->nameImage]['type'];
                    $extension = explode("/", $type);
                    $name = $sena->row->nameImage . "." . $extension[1] . "?v=" . $date;
                } else {
                    $name = $sena->row->nameImage . ".png?v=" . $date;
                }

                $sql = " INSERT
                        INTO senia_particular_detenido(
                            No_Remision,
                            Tipo_Senia_Particular,
                            Perfil,
                            Ubicacion_Corporal,
                            Color,
                            Clasificacion,
                            Descripcion,
                            Path_Imagen
                        )
                        VALUES(
                            trim($remision),
                            '" . trim($sena->row->tipo) . "',
                            '" . trim($sena->row->perfil) . "',
                            '" . trim($sena->row->partes) . "',
                            b'". trim($sena->row->color) . "',
                            '" . trim($sena->row->clasificacion) . "',
                            '" . trim($sena->row->descripcion) . "',
                            '" . trim($name) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $sql = "SELECT * FROM vestimenta_detenido WHERE No_Remision =" . $remision;
            $this->db->query($sql);
            if ($this->db->register()) {
                $sql = " UPDATE vestimenta_detenido
                    SET Tipo_Vestimenta = '" . trim($post['tipoVestimenta']) . "', Descripcion_Vestimenta = '" . trim($post['descripcionVestimenta'] ). "'
                    WHERE No_Remision = $remision
                ";
            } else {
                $sql = " INSERT
                    INTO vestimenta_detenido(
                        No_Remision,
                        Tipo_Vestimenta,
                        Descripcion_Vestimenta
                    )
                    VALUES(
                        trim($remision),
                        '" . trim($post['tipoVestimenta']) . "',
                        '" . trim($post['descripcionVestimenta']) . "'
                    )
                ";
            }
            $this->db->query($sql);
            $this->db->execute();

            $sql = "DELETE FROM accesorio_detenido WHERE No_Remision =" . $remision;
            $this->db->query($sql);
            $this->db->execute();

            $accesorios = json_decode($post['accesorios_table']);
            foreach ($accesorios as $accesorio) {
                $sql = " INSERT
                        INTO accesorio_detenido(
                            No_Remision,
                            Tipo_Accesorio,
                            Descripcion
                        )
                        VALUES(
                            trim($remision),
                            '" . trim($accesorio->row->accesorio) . "',
                            '" . trim($accesorio->row->descripcion) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $this->db->commit();
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = 'Hubo un error en la Base de datos.';
            $this->db->rollBack();
        }

        return $response;
    }

    public function updateEntrevistaDetenido($post, $no_remision)
    {
        $response['status'] = true;
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM entrevista_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            if ($this->db->register()) {
                $sql = " UPDATE entrevista_detenido
                    SET Vinculacion_Grupo_D ='" . trim(str_replace('"', '`',$post['probableVinculacion'])) . "', Motivo_Delinquir = '" . trim($post['motivoDelinquir']) . "', Modus_Operandi = '" . trim($post['modusOperandi']) . "'
                    WHERE No_Remision = $no_remision
                ";
            } else {
                $sql = " INSERT
                    INTO entrevista_detenido(
                        No_Remision,
                        Vinculacion_Grupo_D,
                        Motivo_Delinquir,
                        Modus_Operandi
                    )
                    VALUES(
                        trim($no_remision),
                        '" . trim(str_replace('"', '`',$post['probableVinculacion'])) . "',
                        '" . trim($post['motivoDelinquir']) . "',
                        '" . trim($post['modusOperandi']) . "'
                    )
                ";
            }
            $this->db->query($sql);
            $this->db->execute();

            $sql = "DELETE FROM institucion_seguridad_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $this->db->execute();

            $instituciones = json_decode($post['instituciones_table']);
            foreach ($instituciones as $institucion) {
                $sql = " INSERT
                        INTO institucion_seguridad_detenido(
                            No_Remision,
                            Tipo_Institucion,
                            Nombre_Institucion
                        )
                        VALUES(
                            trim($no_remision),
                            '" . trim($institucion->row->tipo) . "',
                            '" . trim($institucion->row->corporacion) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $sql = "DELETE FROM adiccion_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $this->db->execute();

            $adicciones = json_decode($post['adicciones_table']);
            foreach ($adicciones as $adiccion) {
                $roba = 0;
                if (strlen($adiccion->row->robar) > 0) {
                    $roba = 1;
                }
                $sql = " INSERT
                        INTO adiccion_detenido(
                            No_Remision,
                            Nombre_Adiccion,
                            Robo_Para_Consumo,
                            Frecuencia_Consumo,
                            Tiempo_Consumo,
                            Que_Suele_Robar
                        )
                        VALUES(
                            trim($no_remision),
                            '" . trim($adiccion->row->tipo) . "',
                            b'". $roba."',
                            '" . trim($adiccion->row->frecuencia) . "',
                            '" . trim($adiccion->row->tiempo) . "',
                            '" . trim($adiccion->row->robar) . "'

                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $sql = "DELETE FROM falta_delito_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $this->db->execute();

            $faltas = json_decode($post['faltasAdministrativas_table']);
            foreach ($faltas as $falta) {
                $sql = " INSERT
                        INTO falta_delito_detenido(
                            No_Remision,
                            Descripcion,
                            Fecha_FD_Detenido
                        )
                        VALUES(
                            trim($no_remision),
                            '" . trim($falta->row->descripcion) . "',
                            '" . trim($falta->row->fecha) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $sql = "DELETE FROM antecedente_penal WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $this->db->execute();

            $antecedentes = json_decode($post['antecedentesPenales_table']);
            foreach ($antecedentes as $antecedente) {
                $sql = " INSERT
                        INTO antecedente_penal(
                            No_Remision,
                            Descripcion,
                            Fecha_Antecedente
                        )
                        VALUES(
                            trim($no_remision),
                            '" . trim($antecedente->row->descripcion) . "',
                            '" . trim($antecedente->row->fecha) . "'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
            }

            $this->db->commit();
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = $e;
            $this->db->rollBack();
        }

        return $response;
    }

    public function updateValidarTab($remision, $tab)
    {
        $band = true;
        try {

            $this->db->beginTransaction();

            $sql = "SELECT reverse(EXPORT_SET(Validacion_Tab,'1','0','',11)) AS Reverse FROM remision WHERE NO_Remision =" . $remision;
            $this->db->query($sql);
            if ($this->db->register()) {
                $bit = $this->db->register()->Reverse;
                $bit[$tab] = 1;
                $sql = " UPDATE remision
                    SET Validacion_Tab = b'" . trim($bit) . "'
                    WHERE No_Remision = $remision
                ";
                $this->db->query($sql);
                $this->db->execute();
            } else {
                $band = false;
            }

            $this->db->commit();
        } catch (Exception $e) {
            echo "Sucedio un error " . $e;
            $band = false;
            $this->db->rollBack();
        }

        return $band;
    }

    public function updateGuardarTab($remision, $tab)
    {
        $band = true;
        try {

            $this->db->beginTransaction();

            $sql = "SELECT reverse(EXPORT_SET(Llenado_Tab,'1','0','',11)) AS Reverse FROM remision WHERE NO_Remision =" . $remision;
            $this->db->query($sql);
            if ($this->db->register()) {
                $bit = $this->db->register()->Reverse;
                $bit[$tab] = 1;
                $sql = " UPDATE remision
                    SET Llenado_Tab = b'" . trim($bit) . "'
                    WHERE No_Remision = $remision
                ";
                $this->db->query($sql);
                $this->db->execute();
            } else {
                $band = false;
            }

            $this->db->commit();
        } catch (Exception $e) {
            echo "Sucedio un error " . $e;
            $band = false;
            $this->db->rollBack();
        }

        return $band;
    }

    public function getTabsValidados($remision)
    {
        try {

            $this->db->beginTransaction();

            $sql = "SELECT reverse(EXPORT_SET(Validacion_Tab,'1','0','',11)) AS Reverse FROM remision WHERE NO_Remision =" . $remision;
            $this->db->query($sql);
            $data['tabs_validados'] = $this->db->register();
            $this->db->commit();
        } catch (Exception $e) {
            echo "Sucedio un error " . $e;
            $this->db->rollBack();
        }

        return $data;
    }

    public function getTabsGuardados($remision)
    {
        try {

            $this->db->beginTransaction();

            $sql = "SELECT reverse(EXPORT_SET(Llenado_Tab,'1','0','',11)) AS Reverse FROM remision WHERE NO_Remision =" . $remision;
            $this->db->query($sql);
            $data['tabs_guardados'] = $this->db->register();
            $this->db->commit();
        } catch (Exception $e) {
            echo "Sucedio un error " . $e;
            $this->db->rollBack();
        }

        return $data;
    }

    /* * * * * * * * * * * * * * *   Funciones para insertar accine del historial de Remisiones   * * * * * * * * * * * * * * */

    //Funcionn que inserta los movimientos de los usuarios en la tabla historial
    public function historial($user, $ip, $movimiento, $descripcion)
    {
        $band = true;
        try {

            $this->db->beginTransaction();

            $sql = " INSERT
                    INTO historial(
                        Id_Usuario,
                        Ip_Acceso,
                        Movimiento,
                        Descripcion
                    )
                    VALUES(
                        trim($user),
                        '" . trim($ip) . "',
                        trim($movimiento),
                        '" . trim($descripcion) . "'
                    )
            ";
            $this->db->query($sql);
            $this->db->execute();

            $this->db->commit();
        } catch (Exception $e) {
            echo "Sucedio un error " . $e;
            $band = false;
            $this->db->rollBack();
        }

        return $band;
    }

/*--------------------------FIN DE UPDATE--------------------------*/
    //funcion para obtener la info principal de una remisión para generar el primer IPH
    public function getIPH1($no_ficha = null, $no_remision = null)
    {
        if ($no_ficha == null || $no_remision == null) {
            return false;
        }
        $data['General'] = [];              //info general 
        $data['Elementos'] = [];            //elementos participantes
        $data['Delitos'] = [];              //Delitos por los que se remite  
        $data['Armas_Asegurados'] = [];     //armas asegurados
        $data['Drogas_Asegurados'] = [];    //drogas asegurados 
        $data['Objetos_Asegurados'] = [];   //objetos asegurados 
        //se obtiene la info general
        $sql = "
                SELECT  remision.*,
                        detenido.Edad AS Edad_Det,
                        detenido.Genero AS Genero_Det,
                        detenido.Escolaridad AS Escolaridad_Det,
                        detenido.Pertenencias_Detenido,
                        peticionario_remision.Edad AS Edad_Petic,
                        detenido.Alcoholemia,
                        peticionario_remision.Genero AS Genero_Petic,
                        peticionario_remision.Escolaridad AS Escolaridad_Petic,
                        CONCAT_WS('',peticionario_remision.Nombre,' ',peticionario_remision.Ap_Paterno,' ',peticionario_remision.Ap_Materno) AS Nombre_Peticionario,
                        dom_p.Calle         AS Calle_Petic,
                        dom_p.No_Exterior   AS No_Ext_Petic,
                        dom_p.No_Interior   AS No_Int_Petic,
                        dom_p.Colonia       AS Colonia_Petic,
                        dom_p.CP            AS CP_Petic,
                        dom_p.Municipio     AS Municipio_Petic,
                        CONCAT_WS('',dom_p.Calle,' #ext: ',dom_p.No_Exterior,' #int: ',dom_p.No_Interior,' Col. ',dom_p.Colonia,' ',dom_p.Municipio) AS Domicilio_Peticionario,
                        CONCAT_WS('',detenido.Nombre,' ',detenido.Ap_Paterno,' ',detenido.Ap_Materno) AS Nombre_Detenido,
                        dom_r.Calle         AS Calle_Det,
                        dom_r.No_Exterior   AS No_Ext_Det,
                        dom_r.No_Interior   AS No_Int_Det,
                        dom_r.Colonia       AS Colonia_Det,
                        dom_r.CP            AS CP_Det,
                        dom_r.Municipio     AS Municipio_Det,
                        CONCAT_WS('',dom_r.Calle,' #ext: ',dom_r.No_Exterior,' #int: ',dom_r.No_Interior,' Col. ',dom_r.Colonia,' ',dom_r.Municipio) AS Domicilio_Detenido,
                        CONCAT_WS('',ubi_h.Calle_1,' y ',ubi_h.Calle_2,' ',ubi_h.No_Ext,' Col. ',ubi_h.Colonia) AS Ubicacion_Hechos,
                        ubi_h.Calle_1       AS Calle_1_UH,
                        ubi_h.Calle_2       AS Calle_2_UH,
                        ubi_h.No_Ext        AS No_Ext_UH,
                        ubi_h.Colonia       AS Colonia_UH,
                        ubicacion_hechos_remision.Hora_Reporte  AS Hora_Reporte_UH,
                        ubicacion_hechos_remision.Observaciones AS Observaciones_UH
                FROM remision
                LEFT JOIN detenido ON detenido.No_Remision = remision.No_Remision
                LEFT JOIN peticionario_remision ON peticionario_remision.No_Ficha = remision.No_Ficha
                LEFT JOIN domicilio AS dom_p ON dom_p.Id_Domicilio = peticionario_remision.Id_Domicilio
                LEFT JOIN domicilio AS dom_r ON dom_r.Id_Domicilio = detenido.Id_Domicilio
                LEFT JOIN ubicacion_hechos_remision ON ubicacion_hechos_remision.No_Ficha = remision.No_Ficha
                LEFT JOIN ubicacion AS ubi_h ON ubi_h.Id_Ubicacion = ubicacion_hechos_remision.Id_Ubicacion
                WHERE remision.No_Ficha = " . $no_ficha . " AND remision.No_Remision = " . $no_remision . "
                ";
        $this->db->query($sql);
        $data['General'] = $this->db->register();
        //elementos participantes
        $sql = "
                    SELECT *, CONCAT_WS('',elemento_participante_remision.Nombre,' ',elemento_participante_remision.Ap_Paterno,' ',elemento_participante_remision.Ap_Materno) AS Nombre_Elemento
                    FROM elemento_participante_remision 
                    WHERE No_Remision = " . $no_remision . " AND Cargo <> 'policiaDeGuardiaElementos' 
                    ORDER BY Tipo_Llamado DESC
                ";
        $this->db->query($sql);
        $data['Elementos'] = $this->db->registers();
        //elemento guardia
        $sql = "
                    SELECT Nombre 
                    FROM elemento_participante_remision 
                    WHERE No_Remision = " . $no_remision . " AND Cargo <=> 'policiaDeGuardiaElementos'
                ";
        $this->db->query($sql);
        $data['Elemento_Guardia'] = $this->db->register();
        //armas aseguradas
        $sql = "
                    SELECT * FROM arma_asegurada_detenido WHERE No_Remision = " . $no_remision . "
                ";
        $this->db->query($sql);
        $data['Armas_Asegurados'] = $this->db->registers();
        //drogas aseguradas
        $sql = "
                    SELECT * FROM droga_asegurada_detenido WHERE No_Remision = " . $no_remision . "
                ";
        $this->db->query($sql);
        $data['Drogas_Asegurados'] = $this->db->registers();
        //objetos asegurados
        $sql = "
                    SELECT * FROM objeto_asegurado_detenido WHERE No_Remision = " . $no_remision . "
                ";
        $this->db->query($sql);
        $data['Objetos_Asegurados'] = $this->db->registers();
        //vehiculos asegurados
        $sql = "
                    SELECT * FROM vehiculo_remision WHERE No_Remision = " . $no_remision . "
                ";
        $this->db->query($sql);
        $data['Vehiculos_Asegurados'] = $this->db->registers();
        /*$auxVehiculo = $this->db->register();
        $data['Vehiculo_Asegurado'] = '';
        /*Se añadieron los datos faltantes de vehiculo para que puedan visualizarse en el IPH, que fueron seña, serie, procedencia, observacion
        if(isset($auxVehiculo->Tipo_Vehiculo)){
            $data['Vehiculo_Asegurado'] =   $auxVehiculo->Tipo_Vehiculo." ".
                                            $auxVehiculo->Marca." ".
                                            $auxVehiculo->Modelo." ".
                                            $auxVehiculo->Color." ".
                                            $auxVehiculo->Senia_Particular." ".
                                            $auxVehiculo->No_Serie." ".
                                            $auxVehiculo->Procedencia_Vehiculo." ".
                                            $auxVehiculo->Observacion_Vehiculo." ".
                                            $auxVehiculo->Placa_Vehiculo;
        }*/

        //delitos por los que se le remite
        $sql = "
                    SELECT Descripcion 
                    FROM falta_delito_detenido_uh 
                    WHERE No_Remision = " . $no_remision . "
                ";
        $this->db->query($sql);
        $data['Delitos'] = $this->db->registers();
        return $data;
    }

    /*------------------FUNCIONES PARA FILTRADO Y BÚSQUEDA------------------*/
    //obtener el total de páginas y de registros de la consulta
    public function getTotalPages($no_of_records_per_page, $from_where_sentence = "")
    {
        //quitamos todo aquello que este fuera de los parámetros para solo obtener el substring desde FROM
        $from_where_sentence = strstr($from_where_sentence, 'FROM');

        $sql_total_pages = "SELECT COUNT(*) as Num_Pages " . $from_where_sentence; //total registros
        $this->db->query($sql_total_pages);      //prepararando query
        $total_rows = $this->db->register()->Num_Pages; //ejecutando query y recuperando el valor obtenido
        $total_pages = ceil($total_rows / $no_of_records_per_page); //calculando el total de paginations

        $data['total_rows'] = $total_rows;
        $data['total_pages'] = $total_pages;
        return $data;
    }

    //obtener los registros de la pagina actual
    public function getDataCurrentPage($offset, $no_of_records_per_page, $from_where_sentence = "")
    {

        $sql = "
                SELECT * "
            . $from_where_sentence . " 
                LIMIT $offset,$no_of_records_per_page
                ";

        $this->db->query($sql);
        return $this->db->registers();
    }

    //genera la consulta where dependiendo del filtro
    public function generateFromWhereSentence($cadena = "", $filtro = '1')
    {

        $from_where_sentence = "";
        switch ($filtro) {
            case '1':   //general
                $from_where_sentence .= "
                                        FROM rem_view_filtro_1

                                        WHERE  (    No_Remision LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Ubicacion_Hechos LIKE '%" . $cadena . "%' OR 
                                                    Zona LIKE '%" . $cadena . "%' OR 
                                                    Vector LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Usuario LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Folio_911 LIKE '%" . $cadena . "%' OR 
                                                    Averiguacion_Previa LIKE '%" . $cadena . "%' OR 
                                                    Instancia LIKE '%" . $cadena . "%' OR 
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
            case '2':   //peticionarios
                $from_where_sentence .= "
                                        FROM rem_view_filtro_2

                                        WHERE  (    No_Remision LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Peticionario LIKE '%" . $cadena . "%' OR 
                                                    Domicilio_Peticionario LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Folio_911 LIKE '%" . $cadena . "%' OR 
                                                    Averiguacion_Previa LIKE '%" . $cadena . "%' OR 
                                                    Instancia LIKE '%" . $cadena . "%' OR 
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
            case '3':   //ubicacion de los hechos
                $from_where_sentence .= "
                                        FROM rem_view_filtro_3

                                        WHERE  (    No_Remision LIKE '%" . $cadena . "%' OR 
                                                    Ubicacion_Hechos LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Folio_911 LIKE '%" . $cadena . "%' OR 
                                                    Averiguacion_Previa LIKE '%" . $cadena . "%' OR 
                                                    Instancia LIKE '%" . $cadena . "%' OR 
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
            case '4':   //elementos participantes
                $from_where_sentence .= "
                                        FROM rem_view_filtro_4

                                        WHERE  (    No_Remision LIKE '%" . $cadena . "%' OR 
                                                    Domicilio_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Elemento LIKE '%" . $cadena . "%' OR 
                                                    Cargo LIKE '%" . $cadena . "%' OR 
                                                    Placa LIKE '%" . $cadena . "%' OR 
                                                    Tipo_Llamado LIKE '%" . $cadena . "%' OR 
                                                    No_Unidad LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Folio_911 LIKE '%" . $cadena . "%' OR 
                                                    Averiguacion_Previa LIKE '%" . $cadena . "%' OR 
                                                    Instancia LIKE '%" . $cadena . "%' OR 
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
            case '5':   //ubicación de la detención
                $from_where_sentence .= "
                                        FROM rem_view_filtro_5

                                        WHERE  (    No_Remision LIKE '%" . $cadena . "%' OR 
                                                    Ubicacion_Detencion LIKE '%" . $cadena . "%' OR 
                                                    Domicilio_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Folio_911 LIKE '%" . $cadena . "%' OR 
                                                    Averiguacion_Previa LIKE '%" . $cadena . "%' OR 
                                                    Instancia LIKE '%" . $cadena . "%' OR 
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
            case '6':   //objetos asegurados
                $from_where_sentence .= "
                                        FROM rem_view_filtro_6

                                        WHERE  (    No_Remision LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Descripcion_Objeto LIKE '%" . $cadena . "%' OR 
                                                    Cantidad LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Folio_911 LIKE '%" . $cadena . "%' OR 
                                                    Averiguacion_Previa LIKE '%" . $cadena . "%' OR 
                                                    Instancia LIKE '%" . $cadena . "%' OR 
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
            case '7':   //armas asegurados
                $from_where_sentence .= "
                                        FROM rem_view_filtro_7

                                        WHERE  (    No_Remision LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Tipo_Arma LIKE '%" . $cadena . "%' OR 
                                                    Caracteristica LIKE '%" . $cadena . "%' OR 
                                                    Descripcion_Arma LIKE '%" . $cadena . "%' OR 
                                                    Cantidad LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Folio_911 LIKE '%" . $cadena . "%' OR 
                                                    Averiguacion_Previa LIKE '%" . $cadena . "%' OR 
                                                    Instancia LIKE '%" . $cadena . "%' OR 
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
            case '8':   //droga asegurada
                $from_where_sentence .= "
                                        FROM rem_view_filtro_8

                                        WHERE  (    No_Remision LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Tipo_Droga LIKE '%" . $cadena . "%' OR 
                                                    Cantidad LIKE '%" . $cadena . "%' OR 
                                                    Unidad LIKE '%" . $cadena . "%' OR 
                                                    Descripcion_Droga LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Folio_911 LIKE '%" . $cadena . "%' OR 
                                                    Averiguacion_Previa LIKE '%" . $cadena . "%' OR 
                                                    Instancia LIKE '%" . $cadena . "%' OR 
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
            case '9':   //accesorios detenido
                $from_where_sentence .= "
                                        FROM rem_view_filtro_9

                                        WHERE  (    No_Remision LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Tipo_Accesorio LIKE '%" . $cadena . "%' OR 
                                                    Descripcion LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Folio_911 LIKE '%" . $cadena . "%' OR 
                                                    Averiguacion_Previa LIKE '%" . $cadena . "%' OR 
                                                    Instancia LIKE '%" . $cadena . "%' OR 
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
            case '10':   //accesorios detenido
                $from_where_sentence .= "
                                        FROM rem_view_filtro_10

                                        WHERE  (    No_Remision LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Contacto LIKE '%" . $cadena . "%' OR 
                                                    Parentesco LIKE '%" . $cadena . "%' OR 
                                                    Telefono_Contacto LIKE '%" . $cadena . "%' OR 
                                                    Edad_Contacto LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Folio_911 LIKE '%" . $cadena . "%' OR 
                                                    Averiguacion_Previa LIKE '%" . $cadena . "%' OR 
                                                    Instancia LIKE '%" . $cadena . "%' OR 
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
            case '11':   //adicion detenido
                $from_where_sentence .= "
                                        FROM rem_view_filtro_11

                                        WHERE  (    No_Remision1 LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Adiccion LIKE '%" . $cadena . "%' OR 
                                                    Robo_Para_Consumo LIKE '%" . $cadena . "%' OR 
                                                    Frecuencia_Consumo LIKE '%" . $cadena . "%' OR 
                                                    Tiempo_Consumo LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Folio_911 LIKE '%" . $cadena . "%' OR 
                                                    Averiguacion_Previa LIKE '%" . $cadena . "%' OR 
                                                    Instancia LIKE '%" . $cadena . "%' OR 
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
                case '12':  //tatuajes Se modifico todo el caso para habilitar la búsqueda mediante un lenguaje mas natural enlazando varias palabras ej: "santa muerte en el perctoral"
                    $palabras = explode(" ", strtolower($cadena));//Se obtiene la cadena con la cual se quiere buscar
                    $articulos = array('el', 'la', 'los', 'las', 'un', 'de', 'en', 'unos', 'una', 'unas', 'a', 'con', 'y', 'o', 'u');//arreglo con las palabras a ignorar en la busqueda, normalmete artículos
                    $palabras = array_diff($palabras, $articulos); // con esta instruccion se eliminan las palabras no deseadas de las que si se desan buscar
                    //se inicializa la instruccion del query 
                    $from_where_sentence .= "
                                            FROM rem_view_filtro_12
                                            WHERE   Tipo_Senia_Particular IN ('TATUAJES', 'MUTILACIONES/MALFORMACIONES', 'LUNARES, MANCHAS Y/O VERRUGAS', 'PERFORACIONES', 'CICATRICES')
                                            ";
                                    //obtenido un arreglo con todas las palabras a buscar se recorre una por una para añadirla al query de busqueda de forma que cada palabra es exclusiva, es decr buscara todos los tatuajes que contengan la plabra uno y la palabra 2 y la palabra 3 y así sucesivamente
                                    foreach($palabras as $palabra){
                                        $from_where_sentence .= "
                                        AND
                                        (   No_Remision LIKE '%" . $palabra . "%' OR
                                            Nombre_Detenido LIKE '%" . $palabra . "%' OR
                                            Perfil LIKE '%" . $palabra . "%' OR 
                                            Ubicacion_Corporal LIKE '%" . $palabra . "%' OR 
                                            Clasificacion LIKE '%" . $palabra . "%' OR 
                                            Tipo_Senia_Particular LIKE '%" . $palabra . "%' OR 
                                            Descripcion LIKE '%" . $palabra . "%') 
                                                ";
                                    }
                break;
            case '13':   //registros por validar
                $from_where_sentence .= "
                                        FROM rem_view_filtro_13
                                        WHERE  (    No_Remision LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Domicilio_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Usuario LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Folio_911 LIKE '%" . $cadena . "%' OR 
                                                    Averiguacion_Previa LIKE '%" . $cadena . "%' OR 
                                                    Instancia LIKE '%" . $cadena . "%' OR 
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
            case '14':   //registros validados
                $from_where_sentence .= "
                                        FROM rem_view_filtro_14
                                        WHERE  (    No_Remision LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Domicilio_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Usuario LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Folio_911 LIKE '%" . $cadena . "%' OR 
                                                    Averiguacion_Previa LIKE '%" . $cadena . "%' OR 
                                                    Instancia LIKE '%" . $cadena . "%' OR 
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
            case '15':   //registros validados
                $from_where_sentence .= "
                                        FROM rem_view_filtro_15
                                        WHERE  (    No_Remision LIKE '%" . $cadena . "%' OR 
                                                    Ficha LIKE '%" . $cadena . "%' OR 
                                                    Nombre_Detenido LIKE '%" . $cadena . "%' OR 
                                                    Tipo_Vehiculo LIKE '%" . $cadena . "%' OR 
                                                    Marca LIKE '%" . $cadena . "%' OR 
                                                    Modelo LIKE '%" . $cadena . "%' OR 
                                                    No_Serie LIKE '%" . $cadena . "%' OR
                                                    Placa_Vehiculo LIKE '%" . $cadena . "%' OR
                                                    Fecha_Hora LIKE '%" . $cadena . "%') 
                                            ";
                break;
        }

        //where complemento fechas (si existe)
        $from_where_sentence .= $this->getFechaCondition();
        //order by
        $from_where_sentence .= " ORDER BY No_Remision";
        return $from_where_sentence;
    }

    public function getRemisionDByCadena($cadena, $filtro = '1')
    {
        //CONSULTA COINCIDENCIAS DE CADENA PARA EVENTOS DELICTIVOS
        if (!is_numeric($filtro) || !($filtro >= MIN_FILTRO_REM) || !($filtro <= MAX_FILTRO_REM))
            $filtro = 1;

        //sentencia from_where para hacer la busqueda por la cadena ingresada
        $from_where_sentence = $this->generateFromWhereSentence($cadena, $filtro);
        $numPage = 1;
        $no_of_records_per_page = NUM_MAX_REG_PAGE; //total de registros por pagination
        $offset = ($numPage - 1) * $no_of_records_per_page; // desplazamiento conforme a la pagina

        $results = $this->getTotalPages($no_of_records_per_page, $from_where_sentence);  //total de páginas conforme a la busqueda
        //info de retorno para la creacion de los links conforme a la cadena ingresada
        $data['rows_Rems'] = $this->getDataCurrentPage($offset, $no_of_records_per_page, $from_where_sentence);   //se obtiene la información de la página actual
        $data['numPage'] = $numPage; //numero pag actual para la pagination footer
        $data['total_pages'] = $results['total_pages']; //total pages para la pagination
        $data['total_rows'] = $results['total_rows'];   //total de registro hallados

        return $data;
    }

    //obtener todos los registros de un cierto filtro para su exportación
    public function getAllInfoRemisionDByCadena($from_where_sentence = "")
    {
        $sqlAux = "SELECT *"
            . $from_where_sentence . "
                    ";  //query a la DB
        $this->db->query($sqlAux);          //se prepara el query mediante PDO
        //$registros = $this->db->registers();  
        //$regprint = print_r($registros);
        return $this->db->registers();      //retorna todos los registros devueltos por la consulta
    }
    //complementaria de getAll para vista general para exportación EXCEL
    public function getTodosApartados($rows)
    {

        $data['Remision'] = [];                 //Remision
        $data['Detenido'] = [];                 //Detenido
        $data['Peticionario'] = [];             //Peticionario
        $data['Ubicacion_Hechos'] = [];         //Ubicacion_Hechos
        $data['Faltas_Delitos_Detenido'] = [];  //Faltas_Delitos_Detenido
        $data['Elementos_Participantes'] = [];  //Elementos_Participantes
        $data['Elementos_Participantes_Nombres'] = [];  //Elementos_Participantes_Nombres
        $data['Ubicacion_Detencion'] = [];      //Ubicacion_Detencion

        $data['Narrativas_Elementos'] = []; //Narrativas Elementos
        $data['Objetos_Asegurados'] = [];   //Objetos_Asegurados
        $data['Armas_Aseguradas'] = [];     //Armas_Aseguradas
        $data['Drogas_Aseguradas'] = [];    //Drogas_Aseguradas

        $data['Vehiculos_Remision'] = [];      //Vehiculos_Remision

        $data['Ficha'] = [];                     //Estatus de ficha
        $data['Alias'] = [];                     //Alias del detenido
        $data['Narrativadet'] = [];              //Narrativa de hechos del detenido
        $data['Antecedente'] = [];              //Narrativa de hechos del detenido
        $data['Contacto_Detenido'] = [];              //Narrativa de hechos del detenido


        foreach ($rows as $key => $row) {

            $data['Remision'][$key] = (object)[];
            $data['Detenido'][$key] = (object)[];
            $data['Peticionario'][$key] = (object)[];
            $data['Ubicacion_Hechos'][$key] = (object)[];
            $data['Faltas_Delitos_Detenido'][$key] = '';
            $data['Elementos_Participantes'][$key] = [];
            $data['Elementos_Participantes_Nombres'][$key] = '';
            $data['Ubicacion_Detencion'][$key] = (object)[];

            $data['Objetos_Asegurados'][$key] = '';
            $data['Armas_Aseguradas'][$key] = '';
            $data['Drogas_Aseguradas'][$key] = '';
            $data['Narrativas_Elementos'][$key] = '';
            $data['Vehiculos_Remision'][$key] = '';
            $data['Ficha'][$key] = '';
            $data['Alias'][$key]  = '';
            $data['Narrativadet'][$key] = '';
            $data['Antecedente'][$key] = '';
            $data['Contacto_Detenido'][$key] = '';


            //GET ALL INFO REMISION
            $sql = "SELECT * FROM remision WHERE No_Remision = " . $row->No_Remision;
            $this->db->query($sql);
            $data['Remision'][$key] = $this->db->register();
            //GET ALL INFO Detenido
            $sql = "SELECT 	detenido.*,
                            media_filiacion.*, 
                            domicilio.Calle, 
                            domicilio.No_Exterior, 
                            domicilio.No_Interior, 
                            domicilio.Colonia, 
                            domicilio.Coordenada_X, 
                            domicilio.Coordenada_Y, 
                            domicilio.CP, 
                            domicilio.Municipio, 
                            domicilio.Estado 
                    FROM detenido 
                    LEFT JOIN domicilio ON domicilio.Id_Domicilio = detenido.Id_Domicilio
                    LEFT JOIN media_filiacion ON media_filiacion.Id_Media_Filiacion = detenido.Id_Media_Filiacion
                    WHERE detenido.No_Remision = " . $row->No_Remision;
            $this->db->query($sql);
            $data['Detenido'][$key] = $this->db->register();

            //ALIAS
            $sql = "
                    SELECT Nombre AS Alias
                    FROM alias_detenido
                    WHERE No_Remision = ". $row->No_Remision
                    ;
            $this->db->query($sql);
            $data['Alias'][$key] = $this->db->register();

            //NARRATIVA DETENIDO
            $sql = "SELECT * FROM entrevista_detenido WHERE No_Remision = ". $row->No_Remision;
            $this->db->query($sql);
            $data['Narrativadet'][$key] = $this->db->register();


            //ANTECEDENTES PENALES
            $sql = "
                    SELECT Descripcion AS Antecedentes
                    FROM antecedente_penal
                    WHERE No_Remision = ". $row->No_Remision
                   ;
            $this->db->query($sql);
            
            $auxAP = $this->db->registers();

            foreach ($auxAP as $ante) {
                $data['Antecedente'][$key] .= $ante->Antecedentes . ", ";
            }
            $data['Antecedente'][$key] = substr($data['Antecedente'][$key], 0, -2);


            //GET ALL INFO Peticionario
            $sql = "SELECT 	peticionario_remision.*, 
                            domicilio.Calle, 
                            domicilio.No_Exterior, 
                            domicilio.No_Interior, 
                            domicilio.Colonia, 
                            domicilio.Coordenada_X, 
                            domicilio.Coordenada_Y, 
                            domicilio.CP, 
                            domicilio.Municipio, 
                            domicilio.Estado 
                    FROM peticionario_remision 
                    LEFT JOIN domicilio ON domicilio.Id_Domicilio = peticionario_remision.Id_Domicilio 
                    WHERE peticionario_remision.No_Ficha = " . $row->Ficha;
            $this->db->query($sql);
            $auxPet = $this->db->register();
            // echo "Ficha: ".$row->Ficha."<br><br>";
            if (!$auxPet) {
                //echo "Entre aqui <br><br>";
                $data['Peticionario'][$key]->Nombre = '';
                $data['Peticionario'][$key]->Ap_Paterno = '';
                $data['Peticionario'][$key]->Ap_Materno = '';
                $data['Peticionario'][$key]->Colonia = '';
                $data['Peticionario'][$key]->Calle = '';
                $data['Peticionario'][$key]->No_Exterior = '';
                $data['Peticionario'][$key]->No_Interior = '';
                $data['Peticionario'][$key]->Municipio = '';
                $data['Peticionario'][$key]->CP = '';
                $data['Peticionario'][$key]->Estado = '';
                $data['Peticionario'][$key]->Coordenada_X = '';
                $data['Peticionario'][$key]->Coordenada_Y = '';
                $data['Peticionario'][$key]->Lugar_Origen = '';
                $data['Peticionario'][$key]->Edad = '';
                $data['Peticionario'][$key]->Genero = '';
                $data['Peticionario'][$key]->Escolaridad = '';
            } else {
                $data['Peticionario'][$key] = $auxPet;
            }
            //GET ALL INFO Ubicacion_Hechos
            $sql = "SELECT 	ubicacion_hechos_remision.*, 
                            ubicacion.Calle_1, 
                            ubicacion.Calle_2, 
                            ubicacion.No_Ext, 
                            ubicacion.No_Int, 
                            ubicacion.Colonia, 
                            ubicacion.Coordenada_X, 
                            ubicacion.Coordenada_Y, 
                            ubicacion.CP, 
                            ubicacion.Fraccionamiento 
                    FROM ubicacion_hechos_remision 
                    LEFT JOIN ubicacion ON ubicacion.Id_Ubicacion = ubicacion_hechos_remision.Id_Ubicacion 
                    WHERE ubicacion_hechos_remision.No_Ficha = " . $row->Ficha;
            $this->db->query($sql);
            $data['Ubicacion_Hechos'][$key] = $this->db->register();
            //GET ALL INFO Faltas_Delitos_Detenido
            $sql = "SELECT 	falta_delito_detenido_uh.* 
                    FROM falta_delito_detenido_uh 
                    WHERE falta_delito_detenido_uh.No_Remision = " . $row->No_Remision;
            $this->db->query($sql);
            $auxFD = $this->db->registers();

            foreach ($auxFD as $faltas_delitos) {
                $data['Faltas_Delitos_Detenido'][$key] .= $faltas_delitos->Descripcion . ", ";
            }
            $data['Faltas_Delitos_Detenido'][$key] = substr($data['Faltas_Delitos_Detenido'][$key], 0, -2);


            //GET ALL INFO Elementos_Participantes
            $sql = "SELECT 	elemento_participante_remision.*, 
                            CONCAT_WS('',elemento_participante_remision.Nombre,' ',elemento_participante_remision.Ap_Paterno,' ',elemento_participante_remision.Ap_Materno) AS Nombre_Elemento 
                    FROM elemento_participante_remision 
                    WHERE elemento_participante_remision.No_Remision = " . $row->No_Remision . " AND elemento_participante_remision.Cargo <> 'policiaDeGuardiaElementos'  
                    ORDER BY elemento_participante_remision.Tipo_Llamado DESC,elemento_participante_remision.Id_EP_R";
            $this->db->query($sql);
            $auxEP = $this->db->registers();

            if (!$auxEP) {
                $data['Elementos_Participantes'][$key][0] = (object)[];
                $data['Elementos_Participantes'][$key][1] = (object)[];

                $data['Elementos_Participantes'][$key][0]->No_Unidad = '';
                $data['Elementos_Participantes'][$key][1]->No_Unidad = '';
                $data['Elementos_Participantes'][$key][0]->Sector_Area = '';
            } else {
                $data['Elementos_Participantes'][$key] = $auxEP;
            }
            foreach ($auxEP as $elemento) {
                $data['Elementos_Participantes_Nombres'][$key] .= $elemento->Nombre_Elemento . ", ";
            }
            $data['Elementos_Participantes_Nombres'][$key] = substr($data['Elementos_Participantes_Nombres'][$key], 0, -2);
            //GET ALL INFO Ubicacion_Detencion
            $sql = "SELECT 	ubicacion_detencion.*
                    FROM ubicacion_detencion
                    WHERE ubicacion_detencion.No_Remision = " . $row->No_Remision;
            $this->db->query($sql);
            $auxUbiDet = $this->db->register();
            if (!$auxUbiDet) {
                $data['Ubicacion_Detencion'][$key]->Fecha_Hora_Detencion = '';
            } else {
                $data['Ubicacion_Detencion'][$key] = $auxUbiDet;
            }
            //GET OBJETOS ASEGURADOS DEL DETENIDO
            $sql = "
                    SELECT objeto_asegurado_detenido.Descripcion_Objeto,CONCAT_WS('',objeto_asegurado_detenido.Cantidad,' ',objeto_asegurado_detenido.Descripcion_Objeto) AS Objeto_Asegurado
                    FROM objeto_asegurado_detenido
                    WHERE objeto_asegurado_detenido.No_Remision = $row->No_Remision
                    ";
            $this->db->query($sql);
            $aux1 = $this->db->registers();

            foreach ($aux1 as $objeto) {
                $data['Objetos_Asegurados'][$key] .= $objeto->Descripcion_Objeto . ", ";
            }
            $data['Objetos_Asegurados'][$key] = substr($data['Objetos_Asegurados'][$key], 0, -2);
            //GET ARMAS ASEGURADAS DEL DETENIDO
            $sql = "
                    SELECT arma_asegurada_detenido.Descripcion_Arma ,CONCAT_WS('',arma_asegurada_detenido.Tipo_Arma,' ',arma_asegurada_detenido.Caracteristica,' ',arma_asegurada_detenido.Descripcion_Arma) AS Arma_Asegurada
                    FROM arma_asegurada_detenido
                    WHERE arma_asegurada_detenido.No_Remision = $row->No_Remision
                    ";
            $this->db->query($sql);
            $aux1 = $this->db->registers();

            foreach ($aux1 as $arma) {
                $data['Armas_Aseguradas'][$key] .= $arma->Descripcion_Arma . ", ";
            }
            $data['Armas_Aseguradas'][$key] = substr($data['Armas_Aseguradas'][$key], 0, -2);

            //GET DROGAS ASEGURADAS DETENIDO
            $sql = "
                    SELECT droga_asegurada_detenido.Descripcion_Droga,CONCAT_WS('',droga_asegurada_detenido.Cantidad,' ',droga_asegurada_detenido.Unidad,' ',droga_asegurada_detenido.Tipo_Droga) AS Droga_Asegurada
                    FROM droga_asegurada_detenido
                    WHERE droga_asegurada_detenido.No_Remision = $row->No_Remision
                    ";
            $this->db->query($sql);
            $aux1 = $this->db->registers();

            foreach ($aux1 as $droga) {
                $data['Drogas_Aseguradas'][$key] .= $droga->Descripcion_Droga . ", ";
            }
            $data['Drogas_Aseguradas'][$key] = substr($data['Drogas_Aseguradas'][$key], 0, -2);

            //GET NARRATIVA ELEMENTO PRIMER RESPONDIENTE
            $sql = "
                    SELECT Narrativa_Hechos AS Narrativa_Elemento
                    FROM elemento_participante_remision
                    WHERE Tipo_Llamado = b'1' AND No_Remision = $row->No_Remision
                    LIMIT 1
                    ";
            $this->db->query($sql);
            $aux2 = $this->db->register();

            if (isset($aux2->Narrativa_Elemento)) {
                $data['Narrativas_Elementos'][$key] = $aux2->Narrativa_Elemento;
            }

            // VEHICULOS ASEGURADOS REMISIÓN
            /*Se cambia la forma de la consulta del vehiculo, se regresan todos los campos concatenado en una oracion
            ademas de tomar en cuenta que puede ser mas de un registro, y se comenta la forma en que se hacia antes*/
            $sql = "
                    SELECT vehiculo_remision.Tipo_Vehiculo,CONCAT_WS('',vehiculo_remision.Marca,' ',CASE WHEN Tipo_Situacion THEN 
                    'Involucrado' else 'Recuperado' END,' ',vehiculo_remision.Submarca,' ',vehiculo_remision.Modelo,' ',
                    vehiculo_remision.Color,' ',vehiculo_remision.Placa_Vehiculo,' ',vehiculo_remision.Senia_Particular,' ',
                    vehiculo_remision.Procedencia_Vehiculo,' ',vehiculo_remision.Observacion_Vehiculo,' ',vehiculo_remision.No_Serie) AS Vehiculo_Asegurado
                    FROM vehiculo_remision
                    WHERE vehiculo_remision.No_Remision = $row->No_Remision
                    ";
            $this->db->query($sql);
            $aux1 = $this->db->registers();

            foreach ($aux1 as $vehiculo) {
                $data['Vehiculos_Remision'][$key] .= $vehiculo->Vehiculo_Asegurado . ", ";
            }
           /* if (isset($aux2->Placa_Vehiculo)) {
                $data['Vehiculos_Remision'][$key] = $aux2->Tipo_Vehiculo." ".
                                                    $aux2->Marca." ".
                                                    $aux2->Modelo." ".
                                                    $aux2->Color." ".
                                                    $aux2->Placa_Vehiculo;
            }*/
            //FICHA
            $sql = "
                    SELECT Estatus AS fcancelada
                    FROM ficha
                    WHERE No_Ficha = ".$row->Ficha
                    ;
            $this->db->query($sql);
            $data['Ficha'][$key] = $this->db->register();


            //Telefono Familiar
            $sql = "
                    SELECT Telefono AS Telefono_fam
                    FROM contacto_detenido
                    WHERE No_Remision = ".$row->No_Remision
                    ;
            $this->db->query($sql);
            $data['Contacto_Detenido'][$key] = $this->db->register();


            //$registros =  $data['Ficha'];

            //$regprint = print_r($registros);

            //return $regprint;

        }
        return $data;
    }

    //función auxiliar para filtrar por un rango de fechas específicado por el usuario
    public function getFechaCondition()
    {
        $cad_fechas = "";
        if (isset($_SESSION['userdata']->rango_inicio_rem) && isset($_SESSION['userdata']->rango_fin_rem)) { //si no ingresa una fecha se seleciona el día de hoy como máximo
            $rango_inicio = $_SESSION['userdata']->rango_inicio_rem;
            $rango_fin = $_SESSION['userdata']->rango_fin_rem;
            $cad_fechas = " AND 
                            Fecha_Hora >= '" . $rango_inicio . " 00:00:00'  AND 
                            Fecha_Hora <= '" . $rango_fin . " 23:59:59' 
                            ";
        }
        // else{
        //     $hoy = date("Y-m-d");
        //     $_SESSION['userdata']->rango_inicio_rem = $hoy;
        //     $_SESSION['userdata']->rango_fin_rem = $hoy;
        //     $rango_inicio = $_SESSION['userdata']->rango_inicio_rem;
        //     $rango_fin = $_SESSION['userdata']->rango_fin_rem;
        //     $cad_fechas = " AND 
        //                     Fecha_Hora >= '" . $rango_inicio . " 00:00:00'  AND 
        //                     Fecha_Hora <= '" . $rango_fin . " 23:59:59' 
        //                     ";
        // }

        return $cad_fechas;
    }

    //-------------------------------CONSULTAS PARA JALAR INFORMACIÓN-------------------------------
    public function getPrincipales($no_remision)
    {

        //echo "Hoooooooola".$no_remision;

        $sql = "SELECT 	detenido.*,
                        remision.*,
                        alias_detenido.Nombre AS alias_detenido,
                        domicilio.*,
                        ficha.Estatus AS f_cancelada
                FROM detenido

                LEFT JOIN remision ON remision.No_Remision = detenido.No_Remision
                LEFT JOIN alias_detenido ON alias_detenido.No_Remision = detenido.No_Remision
                LEFT JOIN domicilio ON domicilio.Id_Domicilio = detenido.Id_Domicilio
                LEFT JOIN ficha ON ficha.No_Ficha = remision.No_Ficha
                WHERE detenido.No_Remision = " . $no_remision;

        $this->db->query($sql);
        return $this->db->register();
    }

    public function getPeticionario($no_ficha)
    {

        $sql = "SELECT 	peticionario_remision.*,
                        domicilio.*
                FROM    peticionario_remision
                        

                LEFT JOIN domicilio ON domicilio.Id_Domicilio = peticionario_remision.Id_Domicilio
                WHERE peticionario_remision.No_Ficha = " . $no_ficha;
        $this->db->query($sql);
        return $this->db->register();
    }

    public function getUbicacionH($no_ficha, $no_remision)
    {
        $data['ubicacion_h'] = [];
        $data['faltas_delitos'] = [];

        $sql = "SELECT  remision.*,
                        ubicacion_hechos_remision.*,
                        ubicacion.*
                FROM remision
                LEFT JOIN ubicacion_hechos_remision ON ubicacion_hechos_remision.No_Ficha = remision.No_Ficha
                LEFT JOIN ubicacion ON ubicacion.Id_Ubicacion = ubicacion_hechos_remision.Id_Ubicacion
                WHERE remision.No_Ficha = " . $no_ficha . " AND remision.No_Remision = " . $no_remision;
        $this->db->query($sql);
        $data['ubicacion_h'] = $this->db->register();
        //consulta falta_delito_detenido_uh

        $sql =  "SELECT * 
                 FROM falta_delito_detenido_uh
                 WHERE No_Remision = " . $no_remision;

        $this->db->query($sql);
        $data['faltas_delitos'] = $this->db->registers();

        $sql = "SELECT * FROM remision WHERE No_Ficha = " . $no_ficha . " ORDER BY No_Remision ASC";
        $this->db->query($sql);
        $data['remisiones_ficha'] = $this->db->registers();

        return $data;
    }

    public function getUbicacionD($no_remision)
    {

        $sql = "SELECT 	ubicacion_detencion.*,
                        ubicacion.*
                FROM ubicacion_detencion

                LEFT JOIN ubicacion ON ubicacion.Id_Ubicacion = ubicacion_detencion.Id_Ubicacion
                WHERE ubicacion_detencion.No_Remision =" . $no_remision;
        $this->db->query($sql);
        $data['ubicacionD'] = $this->db->registers();



        return $data;
    }
    //Se modifico esta funcion para que solo realice la consulta a la base de datos
    //de media filiacion separando los contactos detenidos
    public function getMediaFiliacion($no_remision)
    {
        try {
            $this->db->beginTransaction();

            $sql = "SELECT	media_filiacion.*,
                            detenido.No_Remision
                    FROM detenido
                    LEFT JOIN media_filiacion ON media_filiacion.Id_Media_Filiacion = detenido.Id_Media_Filiacion
                    LEFT JOIN contacto_detenido ON contacto_detenido.No_Remision = detenido.No_Remision
                    WHERE detenido.No_Remision = " . $no_remision;
            $this->db->query($sql);
            $data = $this->db->register();
            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
        }

        return $data;
    }
    //Se creo esta nueva funcion para obtener por separado
    //la informacion de los contactos conocidos del detenido
    public function getContactoDetenido($no_remision)
        {
            try {
                $this->db->beginTransaction();
    
                $sql = "SELECT * FROM contacto_detenido
                    WHERE No_Remision = " . $no_remision;
                $this->db->query($sql);
                $data['conocidos'] = $this->db->registers();
    
                $this->db->commit();
            } catch (Exception $e) {
                echo $e;
                $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
            }
            return $data;
    }

    public function getElementosParticipantes($no_remision)
    {
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM elemento_participante_remision WHERE No_Remision = '" . $no_remision . "' AND Cargo != 'policiaDeGuardiaElementos' ";
            $this->db->query($sql);
            $data['elementos_participantes'] = $this->db->registers();

            $sql = "SELECT * FROM elemento_participante_remision WHERE No_Remision = '" . $no_remision . "' AND Cargo = 'policiaDeGuardiaElementos' ";
            $this->db->query($sql);
            $data['guardia'] = $this->db->register();

            $sql = "SELECT Observaciones FROM elemento_participante_remision WHERE No_Remision = '" . $no_remision . "' AND Tipo_Llamado = true";
            $this->db->query($sql);
            $data['observaciones'] = $this->db->register();

            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $this->db->rollBack();
        }

        return $data;
    }
    /*Se añade registers() para $data['vehiculos'], ademas se agregan las consultas para obtener
    lo contenido en las tablas catalogo_marca_vehiculos_io,catalogo_tipos_vehiculos y catalogo_submarcas_vehiculos
    en los apartados $data['marcas_vehiculos'],$data['tipos_vehiculos'] y $data['submarcas_vehiculos']*/
    public function getObjetosRecuperados($no_remision, $no_ficha)
    {
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM arma_asegurada_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $data['armas'] = $this->db->registers();

            $sql = "SELECT * FROM droga_asegurada_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $data['drogas'] = $this->db->registers();

            $sql = "SELECT * FROM objeto_asegurado_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $data['objetos'] = $this->db->registers();

            $sql = "SELECT * FROM vehiculo_remision WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            /*Se cambia por registers para que sea más de uno*/
            $data['vehiculos'] = $this->db->registers();
            /*Se añaden catalogos de marcas, tipos, submarcas*/
            $sql = "SELECT * FROM catalogo_marca_vehiculos_io ORDER BY Marca";
            $this->db->query($sql);
            $data['marcas_vehiculos'] = $this->db->registers();

            $sql = "SELECT * FROM catalogo_tipos_vehiculos ORDER BY Tipo";
            $this->db->query($sql);
            $data['tipos_vehiculos'] = $this->db->registers();

            $sql = "SELECT * FROM catalogo_submarcas_vehiculos ORDER BY Submarca";
            $this->db->query($sql);
            $data['submarcas_vehiculos'] = $this->db->registers();

            $sql = "SELECT Path_Objetos FROM ficha WHERE No_Ficha =" . $no_ficha;
            $this->db->query($sql);
            $data['image'] = $this->db->register();

            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $this->db->rollBack();
        }

        return $data;
    }

    public function getFotosHuellas($post)
    {
        try {
            $this->db->beginTransaction();

            $perfil = explode('_', $post['perfil']);
            $sql = "SELECT * FROM imagen_detenido WHERE No_Remision = " . $post['remision'] . " AND Tipo = '" . $perfil[0] . "' AND Perfil = '" . $perfil[1] . "'";
            $this->db->query($sql);
            if ($this->db->register()) {
                $response['data'] = $this->db->register();
                $response['status'] = true;
            } else {
                $response['error_message'] = 'No se encontraron registros';
                $response['status'] = false;
            }
            /* $response['data'] = $this->db->register(); */

            $this->db->commit();
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error_message'] = $e;
            $this->db->rollBack();
        }

        return $response;
    }

    public function getAllFotosHuellas($no_remision)
    {
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM imagen_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);

            $response['status'] = true;
            $response['fotos'] = $this->db->registers();

            $this->db->commit();
        } catch (Exception $e) {
            $response['status'] = true;
            $response['error_message'] = $e;
            $this->db->rollBack();
        }

        return $response;
    }

    public function getSenasParticulares($no_remision)
    {
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM senia_particular_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $data['senas'] = $this->db->registers();

            $sql = "SELECT * FROM vestimenta_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $data['vestimenta'] = $this->db->register();

            $sql = "SELECT * FROM accesorio_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $data['accesorios'] = $this->db->registers();

            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $this->db->rollBack();
        }

        return $data;
    }

    public function getEntrevistaDetenido($no_remision)
    {
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM entrevista_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $data['entrevista'] = $this->db->register();

            $sql = "SELECT * FROM institucion_seguridad_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $data['instituciones'] = $this->db->registers();

            $sql = "SELECT * FROM adiccion_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $data['addiciones'] = $this->db->registers();

            $sql = "SELECT * FROM falta_delito_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $data['faltas'] = $this->db->registers();

            $sql = "SELECT * FROM antecedente_penal WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $data['antecedentes'] = $this->db->registers();

            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $this->db->rollBack();
        }

        return $data;
    }

    public function getNarrativas($no_remision, $no_ficha)
    {
        try {
            $this->db->beginTransaction();

            $sql = "SELECT Narrativa_Hechos FROM peticionario_remision WHERE No_Ficha =" . $no_ficha;
            $this->db->query($sql);
            $data['peticionario'] = $this->db->register();

            $sql = "SELECT Narrativa_Hechos FROM elemento_participante_remision WHERE No_Remision ='" . $no_remision . "'AND Tipo_Llamado = true";
            $this->db->query($sql);
            $data['elemento'] = $this->db->register();

            $sql = "SELECT Narrativa_Hechos FROM entrevista_detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $data['detenido'] = $this->db->register();

            $sql = "SELECT Extracto_IPH, Path_IPH, CDI FROM detenido WHERE No_Remision =" . $no_remision;
            $this->db->query($sql);
            $data['iph'] = $this->db->register();

            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $this->db->rollBack();
        }

        return $data;
    }

    public function getRemision($no_remision)
    {
        $response = true;

        try {
            $this->db->beginTransaction(); //transaction para evitar errores de inserción
            //se comprueba la existencia del remitido
            $sql = "SELECT Nombre FROM detenido WHERE No_Remision = " . $no_remision;
            $this->db->query($sql);
            $response = $this->db->register();
            $this->db->commit(); //si todo sale bien se realiza los commits

        } catch (Exception $e) {
            echo $e;
            $response = false;
            $this->db->rollBack(); //si algo falla realiza el rollBack por seguridad
        }
        return $response;
    }

    public function getObservacion($no_remision)
    {
        try {
            $this->db->beginTransaction();

            $sql = "SELECT Observaciones FROM elemento_participante_remision WHERE No_Remision ='" . $no_remision . "'AND Tipo_Llamado = true LIMIT 1";
            $this->db->query($sql);
            $data['observacion'] = $this->db->register();

            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $this->db->rollBack();
        }

        return $data;
    }

    public function getUserRemision($no_remision)
    {
        try {
            $this->db->beginTransaction();
            $sql = "SELECT Nombre, Ap_Paterno, Ap_Materno, Area FROM remision INNER JOIN usuario ON remision.Id_Usuario = usuario.Id_Usuario  WHERE remision.No_Remision =" . $no_remision;
            $this->db->query($sql);
            $res['userCreate'] = $this->db->register();
            $sql = "SELECT Nombre, Ap_Paterno, Ap_Materno FROM usuario INNER JOIN historial ON usuario.Id_Usuario = historial.Id_Usuario WHERE SUBSTRING_INDEX(Descripcion,' ',2) = 'Remisión: " . $no_remision . "' AND Movimiento = 4";
            $this->db->query($sql);
            $res['userValidate'] = $this->db->register();

            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $res = false;
            $this->db->rollBack();
        }

        return $res;
    }

    public function getStatusIris($no_remision)
    {
        $sql = "SELECT COUNT(No_Remision) AS NumRows FROM biometrico_detenido WHERE No_Remision =" . $no_remision;
        $this->db->query($sql);
        $data = $this->db->register();
        return $data->NumRows;
    }
}
