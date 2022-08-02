<?php
//print_r($data);
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;
$archivo = '../public/media/pdf/casi-completo.pdf';
$pdf = new Fpdi();
$pageCount = $pdf->setSourceFile($archivo);
$pageId = $pdf->importPage(1);
$pageId2 = $pdf->importPage(2);
$pageId3 = $pdf->importPage(3);
$pageId4 = $pdf->importPage(4);
for($cantidad_grupo=0;$cantidad_grupo<count($data);$cantidad_grupo++){
    $pdf->addPage();
    $pdf->useImportedPage($pageId, 1, 1,207,295);
    $pdf->SetFont('helvetica','',30);
    $pdf->SetTextColor(31, 56, 100);
    $pdf->SetY(45);
    $pdf->SetX(16);
    $pdf->Cell(5,4,utf8_decode("\"".$data[$cantidad_grupo]['grupo']->NOMBRE_BANDA."\""));
    $pdf->SetTextColor(156, 156, 156);
    $pdf->SetFont('helvetica','',14);
    $pdf->SetY(55);
    $pdf->SetX(77);
    $pdf->Cell(5,4,utf8_decode($data[$cantidad_grupo]['grupo']->PRINCIPALES_DELITOS));
    $pdf->SetFont('helvetica','',11);
    $pdf->SetTextColor(31, 56, 100);
    $pdf->SetY(75);
    $pdf->SetX(20);
    $pdf->Multicell(75,4,utf8_decode($data[$cantidad_grupo]['grupo']->ANTECEDENTES));
    //fotografia
    $imagen = explode("?", $data[$cantidad_grupo]['grupo']->FOTOGRAFIA);
    $pathImagesFH = base_url."public/files/GestorCasos/".$data[$cantidad_grupo]['grupo']->ID_BANDA."/Grupo/".$imagen[0];
   // echo($pathImagesFH);
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
        $pdf->Image($pathImagesFH,102,69,90,68,$extension);
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
        $pdf->Image($pathImagesFH,102,69,90,68,$extension);
    }
    $pdf->SetY(155);
    $pdf->SetX(102);
    $pdf->Multicell(75,4,utf8_decode($data[$cantidad_grupo]['grupo']->PELIGROSIDAD));
    $pdf->SetY(175);
    $pdf->SetX(102);
    $lideres=[];
    foreach($data[$cantidad_grupo]['integrantes'] as $integrante){
        if($integrante->TIPO=="LIDER"){
            $cadena="";
            $cadena=$cadena.$integrante->NOMBRE." ".$integrante->APELLIDO_PATERNO." ".$integrante->APELLIDO_MATERNO." ";
            if($integrante->ALIAS!="")  
                $cadena=$cadena."(a) \"".$integrante->ALIAS."\"";
            array_push($lideres,$cadena);
        } 
    }
    for($i=0;$i<count($lideres);$i++){
        $pdf->SetX(106);
        $pdf->Multicell(80,4,utf8_decode("-".$lideres[$i]));
    }
    $pdf->SetY(200);
    $pdf->SetX(100);
    $zonas=explode(",",$data[$cantidad_grupo]['grupo']->ZONAS);
    $colonias=explode("$",$data[$cantidad_grupo]['grupo']->COLONIAS);
    $zonas_final="";
    for($i=0;$i<count($zonas);$i++)
        if($zonas[$i]!="")
            $zonas_final=$zonas_final.$zonas[$i].": ".$colonias[$i];
    $pdf->Multicell(90,4,utf8_decode($zonas_final));
    $pdf->SetY(225);
    $pdf->SetX(100);
    $pdf->Multicell(90,4,utf8_decode($data[$cantidad_grupo]['grupo']->ACTIVIDADES_ILEGALES));
    $total=count($data[$cantidad_grupo]['integrantes']);
    $repla=['"','\'','\"','“','”'];
    for($contador_integrantes=0;$contador_integrantes<count($data[$cantidad_grupo]['integrantes']);$contador_integrantes++){  
        if($contador_integrantes%2==0){
            if($contador_integrantes==$total-1){
                $pdf->addPage();
                $pdf->useImportedPage($pageId3, 1, 1,207,295);
                $pdf->SetFont('helvetica','',11);
                $pdf->SetTextColor(31, 56, 100);
            }
            else{
                $pdf->addPage();
                $pdf->useImportedPage($pageId2, 1, 1,207,295);
                $pdf->SetFont('helvetica','',11);
                $pdf->SetTextColor(31, 56, 100);
            }
        }
       
        if($contador_integrantes%2==0){
            $pdf->SetY(40);
            $pdf->SetX(22);
            $pdf->Cell(5,4,utf8_decode(mb_strtoupper($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->NOMBRE." ".$data[$cantidad_grupo]['integrantes'][$contador_integrantes]->APELLIDO_PATERNO." ".$data[$cantidad_grupo]['integrantes'][$contador_integrantes]->APELLIDO_MATERNO)));
            //fotografia
            $imagen = explode("?", $data[$cantidad_grupo]['integrantes'][$contador_integrantes]->PATH_IMAGEN);
            $pathImagesFH = base_url."public/files/GestorCasos/".$data[$cantidad_grupo]['grupo']->ID_BANDA."/Grupo/".$imagen[0];
           // echo($pathImagesFH);
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
                $pdf->Image($pathImagesFH,23,47,38,42,$extension);
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
                $pdf->Image($pathImagesFH,23,47,38,42,$extension);
            }
            $pdf->SetY(50);
            $pdf->SetX(63);
            $pdf->SetTextColor(255, 0, 0);
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(41,4,utf8_decode($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->ESTATUS),0,0,'C');
            $pdf->SetFont('Arial','',12);
            $pdf->SetTextColor(31, 56, 100);
            $pdf->SetY(92);
            $pdf->SetX(48);
            $pdf->Cell(5,4,utf8_decode($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->ALIAS));
            $pdf->SetY(99);
            $pdf->SetX(48);
            $pdf->Cell(5,4,utf8_decode($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->CURP));
            $pdf->SetY(107);
            $pdf->SetX(48);
            $pdf->Multicell(50,4,utf8_decode($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->UDC));
            $pdf->SetY(120);
            $pdf->SetX(48);
            $pdf->Multicell(50,4,utf8_decode($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->UTC));
            $pdf->SetY(126);
            $pdf->SetX(48);
            $pdf->Multicell(50,4,utf8_decode($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->PERFIL_FACEBOOK));
            $pdf->SetFont('helvetica','',11);
            $pdf->SetY(145);
            $pdf->SetX(20);
           
          //  echo (str_replace($repla, '`',$data[$cantidad_grupo]['integrantes'][$contador_integrantes]->DESCRIPCION));
            
            $pdf->Multicell(80,4,utf8_decode(str_replace($repla, '"',$data[$cantidad_grupo]['integrantes'][$contador_integrantes]->DESCRIPCION)));
            $pdf->SetY($pdf->GetY()+7);
            $pdf->SetX(20);
            $pdf->Cell(4,4,utf8_decode("Cuenta con antecedentes policiales por: "));
            $pdf->SetY($pdf->GetY()+7);
            $pdf->SetX(25);
            $antecedentes=explode("$",$data[$cantidad_grupo]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA);
            for($i=0;$i<count($antecedentes);$i++){
                $pdf->SetX(25);
                $pdf->Multicell(80,4,utf8_decode(($i+1).".-".$antecedentes[$i]));
            }
        }
        else{
            //------------------------------2
            $pdf->SetFont('helvetica','',11);
            $pdf->SetY(40);
            $pdf->SetX(110);
            $pdf->Cell(5,4,utf8_decode(mb_strtoupper($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->NOMBRE." ".$data[$cantidad_grupo]['integrantes'][$contador_integrantes]->APELLIDO_PATERNO." ".$data[$cantidad_grupo]['integrantes'][$contador_integrantes]->APELLIDO_MATERNO)));
            //fotografia
            $imagen = explode("?", $data[$cantidad_grupo]['integrantes'][$contador_integrantes]->PATH_IMAGEN);
            $pathImagesFH = base_url."public/files/GestorCasos/".$data[$cantidad_grupo]['grupo']->ID_BANDA."/Grupo/".$imagen[0];
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
                $pdf->Image($pathImagesFH,109,47,38,42,$extension);
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
                $pdf->Image($pathImagesFH,109,47,38,42,$extension);
            }
            
            
            $pdf->SetY(50);
            $pdf->SetX(150);
            $pdf->SetTextColor(255, 0, 0);
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(41,4,utf8_decode($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->ESTATUS));
            $pdf->SetFont('Arial','',12);
            $pdf->SetTextColor(31, 56, 100);
            $pdf->SetY(92);
            $pdf->SetX(135);
            $pdf->Cell(5,4,utf8_decode($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->ALIAS));
            $pdf->SetY(99);
            $pdf->SetX(135);
            $pdf->Cell(5,4,utf8_decode($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->CURP));
            $pdf->SetY(107);
            $pdf->SetX(135);
            $pdf->Multicell(50,4,utf8_decode($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->UDC));
            $pdf->SetY(120);
            $pdf->SetX(135);
            $pdf->Multicell(50,4,utf8_decode($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->UTC));
            $pdf->SetY(126);
            $pdf->SetX(135);
            $pdf->Multicell(50,4,utf8_decode($data[$cantidad_grupo]['integrantes'][$contador_integrantes]->PERFIL_FACEBOOK));
            $pdf->SetFont('helvetica','',11);
            $pdf->SetY(145);
            $pdf->SetX(110);
            $pdf->Multicell(80,4,utf8_decode(str_replace($repla, '"',$data[$cantidad_grupo]['integrantes'][$contador_integrantes]->DESCRIPCION)));
            $pdf->SetY($pdf->GetY()+7);
            $pdf->SetX(110);
            $pdf->Cell(4,4,utf8_decode("Cuenta con antecedentes policiales por: "));
            $pdf->SetY($pdf->GetY()+7);
            $pdf->SetX(115);
            $antecedentes=explode("$",$data[$cantidad_grupo]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA);
            for($i=0;$i<count($antecedentes);$i++){
                $pdf->SetX(115);
                $pdf->Multicell(80,4,utf8_decode(($i+1).".-".$antecedentes[$i]));
            } 
        }
    }
    //----------------integrantes---------------------------------------------
    ///----------------1
    
    
    
    
}
$pdf->Output('I', 'generated.pdf');
?>