<?php


class PDF extends FPDF
{
    protected $col = 0; // Current column
    protected $y0;      // Ordinate of column start

    function MultiCellRow($cells, $width, $height, $data, $pdf,$espacios_blancos)
    {
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $maxheight = 0;
        for ($i = 0; $i < $cells; $i++) {

            if($i == 1){
                $this->SetFillColor(242,242,242);
                $width = $width+20;
            } else {
                $this->SetFillColor(200,200,200);
                $width = $width -10;
                $data[$i]= $data[$i].str_repeat(" ", $espacios_blancos);
            }


            $pdf->MultiCell($width, $height, $data[$i],0,1,'L',1);
            if ($pdf->GetY() - $y > $maxheight) $maxheight = $pdf->GetY() - $y;
            $pdf->SetXY($x + ($width * ($i + 1)), $y);
        }
        return $maxheight;
    }

    function ChapterBody()
    {
        $this->SetFont('Arial','',12);
                $this->SetTextColor(255, 0, 0);
                $this->SetY($this->GetY()+10);
                $this->SetX($this->GetX()+43);
                $this->Cell(40,10,'ACTIVO',1,0,'C');
                $this->SetFillColor(200,200,200);
                $this->SetTextColor(31, 56, 100);
                $this->Ln();
                $this->SetY($this->GetY()+33);
                $this->Multicell(80,8,utf8_decode("Alias:    "."UN ALIAS"),0,1,'L',1);
                $this->Multicell(80,8,utf8_decode("CURP:   "."UN CURP"),0,1,'L',1);
                $this->Multicell(80,8,utf8_decode("UDC:      "."ULTIMO DOMICILIO CONOCIDO"),0,1,'L',1);
                $this->Multicell(80,8,utf8_decode("UTC:      "."ULTIMO TELEFONO CONOCIDO"),0,1,'L',1);
                $this->Multicell(80,8,utf8_decode("Facebook: " ."PERFIL DE FACEBOOK"),0,1,'R',1);
                $this->SetY($this->GetY()+5);
                
                $espacios_blancos = 8-5;
                $maxheight = $this->MultiCellRow(2, 40, 10, ["Alias:","UN ALIAS"],$this,$espacios_blancos);
                $espacios_blancos = 7-4;
                $this->SetY($this->GetY()+$maxheight);
                $maxheight =$this->MultiCellRow(2, 40, 10, ["CURP: ","UN CURP"],$this,$espacios_blancos);
                $espacios_blancos = 25-3;
                $this->SetY($this->GetY()+$maxheight);
                $maxheight =$this->MultiCellRow(2, 40, 10, ["UDC: ","ULTIMO DOMICILIO CONOCIDO"],$this,$espacios_blancos);
                $espacios_blancos = 24-3;
                $this->SetY($this->GetY()+$maxheight);
                $maxheight =$this->MultiCellRow(2, 40, 10, ["UTC: ","ULTIMO TELEFONO CONOCIDO"],$this,$espacios_blancos);
                $espacios_blancos = 27-8;
                $this->SetY($this->GetY()+$maxheight);
                $maxheight =$this->MultiCellRow(2, 40, 10, ["Facebook: ","PERFIL DE FACEBOOKKKKKKKKKK"],$this,$espacios_blancos);


    }

    function PrintChapter()
    {
        // Add chapter
        $this->AddPage();
        $this->ChapterBody();
    }
}

$pdf = new PDF();
$pdf->AddFont('Avenir','','avenir.php');
$pdf->AliasNbPages();
$pdf->PrintChapter();
//$pdf->PrintChapter(2,'THE PROS AND CONS','20k_c2.txt');
$pdf->Output();
?>