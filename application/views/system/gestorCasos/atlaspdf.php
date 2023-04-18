<?php


class PDF extends FPDF
{
    protected $col = 0; // Current column
    protected $y0;      // Ordinate of column start

    function MultiCellRow($cells, $width, $heightizq, $heightder, $data, $pdf,$espacios_blancos,$numllamado)
    {
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $maxheight = 0;
        for ($i = 0; $i < $cells; $i++) {

            if($i == 1){
                $this->SetFillColor(242,242,242); //242
                $x = $pdf->GetX()+$width;
                $width = $width+25;
            } else {
                $this->SetFillColor(200,200,200); //200
                $width = $width -10;
                if($espacios_blancos > 0)
                    $data[$i]= $data[$i].str_repeat(" ", $espacios_blancos);
            }
            $valortamaño=12;
            $this->SetFont('helvetica','',$valortamaño);
            // numero de llamado indica el renglon de la "tabla" 1 alias, 2 curp ,3 udc, 4 utc, 5 facebook, siendo el 2 y el 4 inamobibles de un renglon
            $longituddatoderecha=0;
            $longituddatoderecha = strlen($data[1]);
            switch ($numllamado) {
                case '1': //ALIAS
                    if($i == 0){
                        if( ($longituddatoderecha>=18) && ($longituddatoderecha<=30) ){
                            $heightizq = 14;
                        }
                        $pdf->MultiCell($width, $heightizq, $data[$i],0,1,'L');
                    }else{
                        $pdf->MultiCell($width, $heightder, $data[$i],0,1,'L');
                    }   
                    break;

                case '2': //CURP
                    if($i == 0){
                        $pdf->MultiCell($width, $heightizq, $data[$i],0,1,'L');
                    }else{
                        $pdf->MultiCell($width, $heightder, $data[$i],0,1,'L');
                    }
                    break;

                case '3': //DOMICILIO
                    if($i == 0){
                        if( ($longituddatoderecha>=16) && ($longituddatoderecha<=35) ){
                            $heightizq = 14;
                        }else {
                            if( ($longituddatoderecha>=36) && ($longituddatoderecha<=49) ){
                                $heightizq = 21;
                            }else {
                                if( ($longituddatoderecha>=50) ){
                                    $heightizq = 28;
                                }
                            }
                        }
                        $pdf->MultiCell($width, $heightizq, $data[$i],0,1,'L');
                    }else{
                        $pdf->MultiCell($width, $heightder, $data[$i],0,1,'L');
                    }   
                    break;

                case '4': //TELEFONO
                    if($i == 0){
                        $pdf->MultiCell($width, $heightizq, $data[$i],0,1,'L');
                    }else{
                        $pdf->MultiCell($width, $heightder, $data[$i],0,1,'L');
                    }
                    break;

                case '5': //FACEBOOK
                    if($i == 0){
                        if( ($longituddatoderecha>=24) && ($longituddatoderecha<=35) ){
                            $heightizq = 14;
                        }else {
                            if( ($longituddatoderecha>=36) && ($longituddatoderecha<=60) ){
                                $heightizq = 21;
                            }
                        }
                        $pdf->MultiCell($width, $heightizq, $data[$i],0,1,'L');
                    }else{
                        $pdf->MultiCell($width, $heightder, $data[$i],0,1,'L');
                    }   
                    break;
                
                default:
                    # code...
                    break;
            }

            if ($pdf->GetY() - $y > $maxheight) $maxheight = $pdf->GetY() - $y;
            $pdf->SetXY($x + ($width * ($i + 1)), $y);
            $this->SetFont('helvetica','',12);

        }
        return $maxheight;
    }

    function Header()
    {
        // Page header
        global $title;
        /* ----- ----- ----- Variables ----- ----- ----- */
        $banner             = 'http://localhost/atlas/public/media/images/logo22.png';
        $this->Image($banner,80,0,50);
        $this->SetX(50);
    /*    $this->SetFont('Arial','B',15);
        $w = $this->GetStringWidth($title)+6;
        $this->SetX((210-$w)/2);
        $this->SetDrawColor(0,80,180);
        $this->SetFillColor(230,230,0);
        $this->SetTextColor(220,50,50);
        $this->SetLineWidth(1);
        $this->Cell($w,9,$title,1,1,'C',true);*/
        $this->Ln(10);
        // Save ordinate
        $this->y0 = $this->GetY()+10;
    }

    function Footer()
    {
        // Page footer
        $banner= 'http://localhost/atlas/public/media/images/footeratlas2.png';
        $this->Image($banner,10,277,180);
     /*   $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(128);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');*/
    }

    function SetCol($col)
    {
        // Set position at a given column
        $this->col = $col;
        $x = 10+$col*97.5;
        $this->SetLeftMargin($x);
      //  $this->SetY(20);
        $this->SetX($x);
      //  $this->SetMargins(10,20);
       
        if($contador_integrantes%2==0){
            $pdf->SetY(40);
            $pdf->SetX(22);
            $pdf->Cell(5,4,utf8_decode(mb_strtoupper($data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->NOMBRE." ".$data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->APELLIDO_PATERNO." ".$data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->APELLIDO_MATERNO)));
            //fotografia
            $imagen = explode("?", $data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->PATH_IMAGEN);
            $pathImagesFH = base_url."public/files/GestorCasos/".$data[$cantidad_grupo][0]['grupo']->ID_BANDA."/Grupo/".$imagen[0];
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
            $pdf->Cell(40,4,utf8_decode($data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->ESTATUS),0,0,'C');
            $pdf->SetFont('Arial','',12);
            $pdf->SetTextColor(31, 56, 100);
            $pdf->SetY(92);
            $pdf->SetX(48);
            $pdf->Cell(5,4,utf8_decode($data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->ALIAS));
            $pdf->SetY(99);
            $pdf->SetX(48);
            $pdf->Cell(5,4,utf8_decode($data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->CURP));
            $pdf->SetY(107);
            $pdf->SetX(48);
            $pdf->Multicell(50,4,utf8_decode($data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->UDC));
            $pdf->SetY(120);
            $pdf->SetX(48);
            $pdf->Multicell(50,4,utf8_decode($data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->UTC));
            $pdf->SetY(126);
            $pdf->SetX(48);
            $pdf->Multicell(50,4,utf8_decode($data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->PERFIL_FACEBOOK));
            $pdf->SetFont('helvetica','',11);
            $pdf->SetY(145);
            $pdf->SetX(20);
           
          //  echo (str_replace($repla, '`',$data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->DESCRIPCION));
            
            $pdf->Multicell(80,4,utf8_decode(str_replace($repla, '"',$data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->DESCRIPCION)));
            $pdf->SetY($pdf->GetY()+7);
            $pdf->SetX(20);
            $pdf->Cell(4,4,utf8_decode("Cuenta con antecedentes policiales por: "));
            $pdf->SetY($pdf->GetY()+7);
            $pdf->SetX(25);
            $antecedentes=explode("$",$data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA);
            for($i=0;$i<count($antecedentes);$i++){
                $pdf->SetX(25);
                $pdf->Multicell(80,4,utf8_decode(($i+1).".-".$antecedentes[$i]));
            }
            $this->SetLineWidth(0.5);
            $this->Rect(101,68,91,72,"D");
            $this->Image($pathImagesFH,102,69,89,70,$extension);
        }
        else{
            $pathImagesFH = "http://localhost/atlas/public/files/GestorCasos/placeholdergrupo.jpg";
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
            $pdf->Cell(40,4,utf8_decode($data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->ESTATUS),0,0,'C');
            $pdf->SetFont('Arial','',12);
            $pdf->SetTextColor(31, 56, 100);
            $pdf->SetY(92);
            $pdf->SetX(135);
            $pdf->Cell(5,4,utf8_decode($data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->ALIAS));
            $pdf->SetY(99);
            $pdf->SetX(135);
            $pdf->Cell(5,4,utf8_decode($data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->CURP));
            $pdf->SetY(107);
            $pdf->SetX(135);
            $pdf->Multicell(50,4,utf8_decode($data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->UDC));
            $pdf->SetY(120);
            $pdf->SetX(135);
            $pdf->Multicell(50,4,utf8_decode($data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->UTC));
            $pdf->SetY(126);
            $pdf->SetX(135);
            $pdf->Multicell(50,4,utf8_decode($data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->PERFIL_FACEBOOK));
            $pdf->SetFont('helvetica','',11);
            $pdf->SetY(145);
            $pdf->SetX(110);
            $pdf->Multicell(80,4,utf8_decode(str_replace($repla, '"',$data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->DESCRIPCION)));
            $pdf->SetY($pdf->GetY()+7);
            $pdf->SetX(110);
            $pdf->Cell(4,4,utf8_decode("Cuenta con antecedentes policiales por: "));
            $pdf->SetY($pdf->GetY()+7);
            $pdf->SetX(115);
            $antecedentes=explode("$",$data[$cantidad_grupo][0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA);
            for($i=0;$i<count($antecedentes);$i++){
                $pdf->SetX(115);
                $pdf->Multicell(80,4,utf8_decode(($i+1).".-".$antecedentes[$i]));
            } 
        }
        for($i=0;$i<count($lideres);$i++){
            $this->SetX(106);
            $this->Multicell(80,4,utf8_decode(strtoupper("-".$lideres[$i])));
        }
        $this->AddPage();
        $this->SetCol(0);
        $repla=['"','\'','\"','“','”'];
        for($contador_integrantes=0;$contador_integrantes<count($data[0]['integrantes']);$contador_integrantes++){
            
            $this->SetTextColor(31, 56, 100);
            $this->SetY($this->GetY()+5);
            $this->SetFont('helvetica','B',10);
            $this->Cell(5,4,utf8_decode(mb_strtoupper($data[0]['integrantes'][$contador_integrantes]->NOMBRE." ".$data[0]['integrantes'][$contador_integrantes]->APELLIDO_PATERNO." ".$data[0]['integrantes'][$contador_integrantes]->APELLIDO_MATERNO)));
            $this->SetFont('helvetica','',12);    
            $this->Line($this->GetX()-5, $this->GetY()+5, $this->GetX()+80, $this->GetY()+5);
                //fotografia
                $imagen = explode("?", $data[0]['integrantes'][$contador_integrantes]->PATH_IMAGEN);
                $pathImagesFH = "http://localhost/atlas/public/files/GestorCasos/".$data[0]['grupo']->ID_BANDA."/Grupo/".$imagen[0];
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
                    $this->SetLineWidth(0.5);
                    $this->Rect($this->GetX()-5,$this->GetY()+7,40,44,"D");
                    $this->Image($pathImagesFH,$this->GetX()-4,$this->GetY()+8,38,42,$extension);
                }
                else{
                    $pathImagesFH = "http://localhost/atlas/public/files/GestorCasos/placeholderprofile.jpg";
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
                    $this->SetLineWidth(0.5);
                    $this->Rect($this->GetX()-5,$this->GetY()+7,40,44,"D");
                    $this->Image($pathImagesFH,$this->GetX()-4,$this->GetY()+8,38,42,$extension);
                }
                $this->SetTextColor(255, 0, 0);
                $this->SetY($this->GetY()+10);
                $this->SetX($this->GetX()+45);
                $this->Cell(37,10,utf8_decode($data[0]['integrantes'][$contador_integrantes]->ESTATUS),1,0,'C');
                $this->SetFillColor(200,200,200);
                $this->SetTextColor(31, 56, 100);
                $this->Ln();
                $this->SetY($this->GetY()+33);
                $espacios_blancos = 1;
                $maxheight = $this->MultiCellRow(2, 40, 7, 7, ["ALIAS:",strtoupper($data[0]['integrantes'][$contador_integrantes]->ALIAS)],$this,$espacios_blancos,1);
                $espacios_blancos = 1;
                $this->SetY($this->GetY()+$maxheight);
                $maxheight =$this->MultiCellRow(2, 40, 7, 7, ["CURP: ",strtoupper($data[0]['integrantes'][$contador_integrantes]->CURP)],$this,$espacios_blancos,2);
                $espacios_blancos = 1;
                $this->SetY($this->GetY()+$maxheight);
                $maxheight =$this->MultiCellRow(2, 40, 7, 7, ["UDC: ",strtoupper($data[0]['integrantes'][$contador_integrantes]->UDC)],$this,$espacios_blancos,3);
                $espacios_blancos = 1;
                $this->SetY($this->GetY()+$maxheight);
                $maxheight =$this->MultiCellRow(2, 40, 7, 7, ["UTC: ",strtoupper($data[0]['integrantes'][$contador_integrantes]->UTC)],$this,$espacios_blancos,4);
                $espacios_blancos = 1;
                $this->SetY($this->GetY()+$maxheight);
                $maxheight =$this->MultiCellRow(2, 40, 7, 7, ["FACEBOOK: ",$data[0]['integrantes'][$contador_integrantes]->PERFIL_FACEBOOK],$this,$espacios_blancos,5);
                /*$this->Multicell(80,8,utf8_decode("Alias:    ".$data[0]['integrantes'][$contador_integrantes]->ALIAS),0,1,'L',1);
                $this->Multicell(80,8,utf8_decode("CURP:   ".$data[0]['integrantes'][$contador_integrantes]->CURP),0,1,'L',1);
                $this->Multicell(80,8,utf8_decode("UDC:      ".$data[0]['integrantes'][$contador_integrantes]->UDC),0,1,'L',1);
                $this->Multicell(80,8,utf8_decode("UTC:      ".$data[0]['integrantes'][$contador_integrantes]->UTC),0,1,'L',1);
                $this->Multicell(80,8,utf8_decode("Facebook: " .$data[0]['integrantes'][$contador_integrantes]->PERFIL_FACEBOOK),0,1,'R',1);*/
                $this->SetY($this->GetY()+$maxheight+3);
                $this->Multicell(80,4,utf8_decode(strtoupper(str_replace($repla, '"',$data[0]['integrantes'][$contador_integrantes]->DESCRIPCION))),0,"J",false);
                
                if(trim($data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA)!=""){
                    $antecedentes=explode("$",$data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA);
                    for($i=0;$i<count($antecedentes);$i++){
                        if($i==0 && $antecedentes[$i]!=""){
                            $this->SetY($this->GetY()+5);
                            $this->SetFont('helvetica','B',10);
                            $this->Cell(4,4,utf8_decode(strtoupper("Cuenta con antecedentes policiales por: ")));
                            $this->SetFont('helvetica','',10);
                            $this->SetY($this->GetY()+5);
                        }
                        $this->SetY($this->GetY()+3);
                        $this->Multicell(80,4,utf8_decode(($i+1).".-".strtoupper($antecedentes[$i])));
                    } 
                }
                if($this->GetY()>150){
                    if($this->col==0)
                        $this->SetCol(1);
                    else{
                        if($contador_integrantes!=count($data[0]['integrantes'])-1 )   
                            $this->AddPage();
                        $this->SetCol(0);
                    }
                    $this->SetY(20);
                }
                
        }
    }
    function PrintChapter($data)
    {
        // Add chapter
        for($cantidad_grupo=0;$cantidad_grupo<count($data);$cantidad_grupo++){
            $this->AddPage();
            $this->ChapterTitle($data[$cantidad_grupo]);
            $this->ChapterBody($data[$cantidad_grupo]);
        }
        
    }
}

$pdf = new PDF();
$pdf->AddFont('Avenir','','avenir.php');
$pdf->AliasNbPages();
$pdf->PrintChapter($data);
//$pdf->PrintChapter(2,'THE PROS AND CONS','20k_c2.txt');
$pdf->Output();
?>