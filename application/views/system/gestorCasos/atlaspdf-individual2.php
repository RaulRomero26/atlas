<?php
    class PDF extends FPDF
    {
        function OnePage($data, $base_url)
        {
            /* ----- ----- ----- Variables ----- ----- ----- */
            $banner             = $base_url.'public/media/images/logo22.png';
            $this->Image($banner,80,5,50);
            $this->SetFont('helvetica','',30);
            $this->SetTextColor(31, 56, 100);
            $this->SetY(45);
            $this->SetX(16);
            $this->Cell(5,4,utf8_decode("\"".$data[0]['grupo']->NOMBRE_BANDA."\""));
            $this->SetTextColor(156, 156, 156);
            $this->SetFont('helvetica','',14);
            $this->SetY(55);
            $this->SetX(77);
            $this->Cell(5,4,utf8_decode($data[0]['grupo']->PRINCIPALES_DELITOS));
            $this->SetFont('helvetica','B',12);
            $this->SetTextColor(31, 56, 100);
            $this->SetY(63);
            $this->SetX(20);
            $this->SetDrawColor(31,56,100);
            $this->Multicell(75,4,"Antecedentes",'C');
            $this->Line(20, $this->GetY()+2, 95, $this->GetY()+2);
            $this->SetFont('helvetica','',11);
            $this->SetY(75);
            $this->SetX(20);
            $this->Multicell(75,4,utf8_decode($data[0]['grupo']->ANTECEDENTES));
            //fotografia
            $imagen = explode("?", $data[0]['grupo']->FOTOGRAFIA);
            $pathImagesFH = base_url."public/files/GestorCasos/".$data[0]['grupo']->ID_BANDA."/Grupo/".$imagen[0];
            if(isset($pathImagesFH) && getimagesize($pathImagesFH)){
                $type = exif_imagetype($pathImagesFH);
                $extension = '';
                switch($type){
                    case 1:
                        $extension = 'gif';
                    break;
                    case 2:
                        $extension = 'jpeg';
                    break;
                    case 3:
                        $extension = 'png';
                    break;
                }
                $this->Image($pathImagesFH,102,69,90,68,$extension);
            }
            else{
                $pathImagesFH = base_url."public/files/GestorCasos/placeholdergrupo.jpg";
                $type = exif_imagetype($pathImagesFH);
                $extension = '';
                switch($type){
                    case 1:
                        $extension = 'gif';
                    break;
                    case 2:
                        $extension = 'jpeg';
                    break;
                    case 3:
                        $extension = 'png';
                    break;
                }
                $this->Image($pathImagesFH,102,69,90,68,$extension);
            }
            
            $this->SetFont('helvetica','B',12);
            $this->SetTextColor(31, 56, 100);
            $this->SetY(148);
            $this->SetX(100);
            $this->SetDrawColor(31,56,100);
            $this->Multicell(75,4,"Peligrosidad",'C');
            $this->Line(100, $this->GetY()+2, 190, $this->GetY()+2);

            $this->SetFont('helvetica','',11);
            $this->SetY(155);
            $this->SetX(102);
            $this->Multicell(75,4,utf8_decode($data[0]['grupo']->PELIGROSIDAD));

            $this->SetFont('helvetica','B',12);
            $this->SetTextColor(31, 56, 100);
            $this->SetDrawColor(31,56,100);
            $this->SetY($this->GetY()+4);
            $this->SetX(100);
            $this->Multicell(75,4,utf8_decode("Zonas de Operación"),'C');
            $this->Line(100, $this->GetY()+2, 190, $this->GetY()+2);

        /*    $this->SetFont('helvetica','',11);
            $this->SetY(175);
            $this->SetX(102);
            
            $this->SetFont('helvetica','B',12);
            $this->SetTextColor(31, 56, 100);
            $this->SetDrawColor(31,56,100);
            $this->SetY($this->GetY()+4);
            $this->SetX(100);
            $this->Multicell(75,4,utf8_decode("Zonas de Operación"),'C');
            $this->Line(100, 190, 190, 190);*/
            $this->SetFont('helvetica','',11);
            $this->SetY($this->GetY()+4);
            $this->SetX(102);
            
            $zonas=explode(",",$data[0]['grupo']->ZONAS);
            $colonias=explode("$",$data[0]['grupo']->COLONIAS);
            $zonas_final="";
            for($i=0;$i<count($zonas);$i++)
                if($zonas[$i]!="")
                    $zonas_final=$zonas_final.$zonas[$i].": ".$colonias[$i];
            $this->Multicell(90,4,utf8_decode($zonas_final));

            $this->SetFont('helvetica','B',12);
            $this->SetTextColor(31, 56, 100);
            $this->SetDrawColor(31,56,100);
            $this->SetY($this->GetY()+4);
            $this->SetX(100);
            $this->Multicell(75,4,utf8_decode("Actividades ilegales"),'C');
            $this->Line(100, $this->GetY()+2, 190, $this->GetY()+2);
            $this->SetFont('helvetica','',11);
            $this->SetY($this->GetY()+8);
            $this->SetX(102);
            $this->Multicell(90,4,utf8_decode($data[0]['grupo']->ACTIVIDADES_ILEGALES));

            $this->SetFont('helvetica','B',12);
            $this->SetTextColor(31, 56, 100);
            $this->SetDrawColor(31,56,100);
            $this->SetY($this->GetY()+4);
            $this->SetX(100);
            $this->Multicell(75,4,utf8_decode("Líderes"),'C');
            $this->Line(100, $this->GetY()+2, 190, $this->GetY()+2);
            $this->SetFont('helvetica','',11);
            $lideres=[];
            $this->SetY($this->GetY()+4);
            foreach($data[0]['integrantes'] as $integrante){
                if($integrante->TIPO=="LIDER"){
                    $cadena="";
                    $cadena=$cadena.$integrante->NOMBRE." ".$integrante->APELLIDO_PATERNO." ".$integrante->APELLIDO_MATERNO." ";
                    if($integrante->ALIAS!="")  
                        $cadena=$cadena."(a) \"".$integrante->ALIAS."\"";
                    array_push($lideres,$cadena);
                } 
            }
            for($i=0;$i<count($lideres);$i++){
                $this->SetX(106);
                $this->Multicell(80,4,utf8_decode("-".$lideres[$i]));
            }
        }
        function TwoPage($data, $base_url)
        {
            $repla=['"','\'','\"','“','”'];
            $x_columna=20;
            $num_columna=1;
            for($contador_integrantes=0;$contador_integrantes<count($data[0]['integrantes']);$contador_integrantes++){
                if($contador_integrantes!=0){
                    if($num_columna==2){
                        $x_columna=20;
                        $y_columna=20;
                        $num_columna=1;
                        if($contador_integrantes!=0)
                            $this->addPage();  
                    }
                    else{
                        $num_columna=2;
                        $x_columna=110;
                        $y_columna=20;
                    }
                }
                else{
                    $x_columna=20;
                    $y_columna=20;
                }
                
                $this->SetTextColor(31, 56, 100);
                $this->SetY($y_columna);
                $this->SetX($x_columna);
                $this->Cell(5,4,utf8_decode(mb_strtoupper($data[0]['integrantes'][$contador_integrantes]->NOMBRE." ".$data[0]['integrantes'][$contador_integrantes]->APELLIDO_PATERNO." ".$data[0]['integrantes'][$contador_integrantes]->APELLIDO_MATERNO)));
                $this->Line($x_columna, $this->GetY()+5, $x_columna+80, $this->GetY()+5);
                //fotografia
                $imagen = explode("?", $data[0]['integrantes'][$contador_integrantes]->PATH_IMAGEN);
                $pathImagesFH = base_url."public/files/GestorCasos/".$data[0]['grupo']->ID_BANDA."/Grupo/".$imagen[0];
                if(isset($pathImagesFH) && getimagesize($pathImagesFH)){
                    $type = exif_imagetype($pathImagesFH);
                    $extension = '';
                    switch($type){
                        case 1:
                            $extension = 'gif';
                        break;
                        case 2:
                            $extension = 'jpeg';
                        break;
                        case 3:
                            $extension = 'png';
                        break;
                    }
                    $this->Image($pathImagesFH,$x_columna,$this->GetY()+10,38,42,$extension);
                }
                else{
                    $pathImagesFH = base_url."public/files/GestorCasos/placeholderprofile.jpg";
                    $type = exif_imagetype($pathImagesFH);
                    $extension = '';
                    switch($type){
                        case 1:
                            $extension = 'gif';
                        break;
                        case 2:
                            $extension = 'jpeg';
                        break;
                        case 3:
                            $extension = 'png';
                        break;
                    }
                    $this->Image($pathImagesFH,$x_columna,$this->GetY()+10,38,42,$extension);
                }
                $this->SetTextColor(255, 0, 0);
                
                $this->SetY($this->GetY()+10);
                $this->SetX($x_columna+40);
                $this->Cell(40,10,utf8_decode($data[0]['integrantes'][$contador_integrantes]->ESTATUS),1,0,'C');
                $this->SetFillColor(200,200,200);
                $this->SetTextColor(31, 56, 100);
                
                $this->SetY($this->GetY()+43);
                $this->SetX($x_columna);
                $this->Multicell(80,8,utf8_decode("Alias:    ".$data[0]['integrantes'][$contador_integrantes]->ALIAS),0,1,'L',1);
                
                $this->SetX($x_columna);
                $this->Multicell(80,8,utf8_decode("CURP:   ".$data[0]['integrantes'][$contador_integrantes]->CURP),0,1,'L',1);
                
                $this->SetX($x_columna);
                $this->Multicell(80,8,utf8_decode("UDC:      ".$data[0]['integrantes'][$contador_integrantes]->UDC),0,1,'L',1);
                
                $this->SetX($x_columna);
                $this->Multicell(80,8,utf8_decode("UTC:      ".$data[0]['integrantes'][$contador_integrantes]->UTC),0,1,'L',1);
                
                $this->SetX($x_columna);
                $this->Multicell(80,8,utf8_decode("Facebook: " .$data[0]['integrantes'][$contador_integrantes]->PERFIL_FACEBOOK),0,1,'R',1);
                
                $this->SetY($this->GetY()+10);
            //    $this->SetX($x_columna); //YA QUE ARREGLE QUE SE VEA JUSTIFICADO Y BIEN PASA A 3000
                $caracteres=0;
                if(strlen($data[0]['integrantes'][$contador_integrantes]->DESCRIPCION)>1300){
                    $descripcion_palabras= explode("\n",str_replace($repla, '"',$data[0]['integrantes'][$contador_integrantes]->DESCRIPCION));
                //    $bandera=0;
                    for($palabra=0;$palabra<count($descripcion_palabras);$palabra++){
                     //   $this->SetY($y_columna);
                     $valor_x=$this->GetY();
                     if($valor_x>270){
                        $y_columna=20;
                        $this->SetY($y_columna);
                   //     $bandera=0;
                        if($num_columna==2){
                            $this->addPage();
                            $num_columna=1;
                            $x_columna=20;
                        }
                        else{
                            $num_columna=2;
                            $x_columna=110;
                        }
                     }

                        $this->SetX($x_columna);
                 //       $caracteres+=strlen($descripcion_palabras[$palabra]);
                 //       if($caracteres>1300 || $caracteres>2200)
                  /*      $concatenar_p="";
                        $cont_palabras=0;
                        while( (strlen($concatenar_p)<35 || strlen($concatenar_p)<42 )&& ($palabra+$cont_palabras)<count($descripcion_palabras)){
                            $concatenar_p= $concatenar_p." ".$descripcion_palabras[$palabra+$cont_palabras];
                            $cont_palabras++;
                        }*/
                        $this->Multicell(80,4,utf8_decode(str_replace($repla, '"',$descripcion_palabras[$palabra])),0,"J",false);
                      //  $palabra+=$cont_palabras-1;
                /*        $valor_x=$this->GetY();
                        if($valor_x>270){    
                            $y_columna=20;
                            $bandera=1;
                            if($x_columna<=110){
                                $x_columna=110;
                              //  $num_columna=2;
                            }
                            else{    
                                $x_columna=20;
                            //    $num_columna=1;
                            }
                        }*/
                        
                   /*     $valor_x=$this->GetX();
                        switch($num_columna){
                            case 1:
                                if($valor_x>90){
                                    $x_columna=110;
                                    $y_columna=20;
                                  //  $this->SetY($y_columna);
                                  //  $this->SetX($x_columna);
                                }
                                break;
                            case 2:
                                if($valor_x>180){
                                    $x_columna=20;
                                    $y_columna=20;
                                 //   $this->SetY($y_columna);
                                   // $this->SetX($x_columna);    
                                }
                                break;
                        }*/
                       // $this->Cell(strlen($descripcion_palabras[$palabra])+2,4,utf8_decode(" ".$descripcion_palabras[$palabra]));*/
                    }
                 //   $this->Multicell(80,4,"columna ".$num_columna);
                    if(trim($data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA)!=""){
                        $antecedentes=explode("$",$data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA);
                        for($i=0;$i<count($antecedentes);$i++){
                            if($i==0 && $antecedentes[$i]!=""){
                                $this->SetY($this->GetY()+8);
                                $this->SetX($x_columna);
                                $this->Cell(4,4,utf8_decode("Cuenta con antecedentes policiales por: "));
                                $this->SetY($this->GetY()+5);
                                $this->SetX($x_columna);
                            }
                            $this->SetX($x_columna);
                            $this->Multicell(80,4,utf8_decode(($i+1).".-".$antecedentes[$i]));
                        } 
                    }
                }
                else{
                    $this->SetX($x_columna);
                    $this->Multicell(80,4,utf8_decode(str_replace($repla, '"',$data[0]['integrantes'][$contador_integrantes]->DESCRIPCION)));
                    if(trim($data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA)!=""){
                        $antecedentes=explode("$",$data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA);
                        for($i=0;$i<count($antecedentes);$i++){
                            if($i==0 && $antecedentes[$i]!=""){
                                $this->SetY($this->GetY()+8);
                                $this->SetX($x_columna);
                                $this->Cell(4,4,utf8_decode("Cuenta con antecedentes policiales por: "));
                                $this->SetY($this->GetY()+5);
                                $this->SetX($x_columna);
                            }
                            $this->SetX($x_columna);
                            $this->Multicell(80,4,utf8_decode(($i+1).".-".$antecedentes[$i]));
                        } 
                    }
                }
                
             /*   $this->Multicell(80,4,utf8_decode(str_replace($repla, '"',$data[0]['integrantes'][$contador_integrantes]->DESCRIPCION)));
                if(trim($data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA)!=""){
                    $antecedentes=explode("$",$data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA);
                    for($i=0;$i<count($antecedentes);$i++){
                        if($i==0 && $antecedentes[$i]!=""){
                            $this->SetY($this->GetY()+8);
                            $this->SetX($x_columna);
                            $this->Cell(4,4,utf8_decode("Cuenta con antecedentes policiales por: "));
                            $this->SetY($this->GetY()+5);
                            $this->SetX($x_columna);
                        }
                        $this->SetX($x_columna);
                        $this->Multicell(80,4,utf8_decode(($i+1).".-".$antecedentes[$i]));
                    } 
                }*/
            /*    if(strlen($data[0]['integrantes'][$contador_integrantes]->DESCRIPCION)>1350){
                    $this->Multicell(80,4,utf8_decode(str_replace($repla, '"',$data[0]['integrantes'][$contador_integrantes]->DESCRIPCION)));
                    if(trim($data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA)!=""){
                        $antecedentes=explode("$",$data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA);
                        for($i=0;$i<count($antecedentes);$i++){
                            if($i==0 && $antecedentes[$i]!=""){
                                $this->SetY($this->GetY()+8);
                                $this->SetX($x_columna);
                                $this->Cell(4,4,utf8_decode("Cuenta con antecedentes policiales por: "));
                                $this->SetY($this->GetY()+5);
                                $this->SetX($x_columna);
                            }
                            $this->SetX($x_columna);
                            $this->Multicell(80,4,utf8_decode(($i+1).".-".$antecedentes[$i]));
                        } 
                    }
                }
                else{
                    $this->Multicell(80,4,utf8_decode(str_replace($repla, '"',$data[0]['integrantes'][$contador_integrantes]->DESCRIPCION)));
                    if(trim($data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA)!=""){
                        $antecedentes=explode("$",$data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA);
                        for($i=0;$i<count($antecedentes);$i++){
                            if($i==0 && $antecedentes[$i]!=""){
                                $this->SetY($this->GetY()+8);
                                $this->SetX($x_columna);
                                $this->Cell(4,4,utf8_decode("Cuenta con antecedentes policiales por: "));
                                $this->SetY($this->GetY()+5);
                                $this->SetX($x_columna);
                            }
                            $this->SetX($x_columna);
                            $this->Multicell(80,4,utf8_decode(($i+1).".-".$antecedentes[$i]));
                        } 
                    }

                }*/
                
                
                
            }
        }

        
        function Footer()
        {
            $this->SetY(-8);
            $this->SetFont('Avenir','',7);
            $this->Cell(0,10,utf8_decode('NÚMERO DE VALIDACIÓN CONTRALORÍA'),0,0,'C');
            $this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'R');
        }

    }

    $pdf = new PDF();
    $pdf->AddFont('Avenir','','avenir.php');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->OnePage($data, base_url);
    $pdf->AddPage();
    $pdf->TwoPage($data, base_url);
 /*   $pdf->AddPage();
     /*Se añade la nueva funcion que contiene solo la imagen de los objetos asegurados
     $pdf->TwoPageHalf($data, base_url);
    $pdf->AddPage();
    $pdf->ThreePage($data);*/
    $pdf->Output();
?>
