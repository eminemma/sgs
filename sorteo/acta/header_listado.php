<?php @session_start();
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
require dirname(__FILE__) . '/../../librerias/pdf/fpdf.php';

class PDF extends FPDF
{
    //Cabecera de página
    public function Header()
    {
        global $titulo, $titulo2, $titulo3;
        //Logo
        $this->Image(dirname(__FILE__) . '/../../img/LOGOhorizontal.jpg', 15, 6, 30, 10);
        $this->Image(dirname(__FILE__) . '/../../img/LOGOhorizontal.jpg', 170, 6, 30, 10);

        //Times bold 15
        $this->SetFont('Times', 'B', 13);
        $this->Ln(-2);
        $y_line = $this->GetY();
        //Movernos a la derecha
        $this->Cell(80);
        //Título
        $y_line = $this->GetY();
        $this->Cell(30, $y_line, 'LOTERIA DE CORDOBA S.E.', 0, 0, 'C');

        $this->SetFont('Times', '', 8);
        $y_line = $this->GetY();
        $this->Ln(5);
        $this->Cell(80);
        $this->Cell(30, $y_line, '27 de Abril 185 - Córdoba - República Argentina', 0, 1, 'C');

        $this->Ln(2);

        $y_line = $this->GetY();
        $this->Line(10, $y_line, 201, $y_line);

        //Salto de línea
        $this->Ln(-3);
        //Arial bold 15
        $this->SetFont('Arial', 'B', 13);
        $y_line = $this->GetY();
        $this->Cell(190, $y_line, $titulo, 0, 1, 'C');

        if (!empty($titulo3)) {

            $this->Ln(-15);
            $y_line = $this->GetY();
            $this->Cell(190, $y_line, $titulo3, 0, 1, 'C');
            $this->Ln(-6);

        }

        $this->SetFont('Arial', 'B', 13);
        if ($titulo2 != '') {
            $this->Ln(-15);
            $y_line = $this->GetY();
            $this->Cell(190, $y_line, $titulo2, 0, 1, 'C');
            $this->Ln(-5);
        }

    }
    //Pie de página

    public function Footer()
    {
        global $conPie;
        if ($conPie !== false) {
            $this->SetY(-15);
            $y_line = $this->GetY();
            $this->Line(10, $y_line, 200, $y_line);
            $this->SetFont('Arial', '', 8);
            $this->Cell(0, 7, 'Sistema SGS Usuario: ' . utf8_decode($_SESSION['nombre_usuario']) . '  ' . date('d/m/Y h:i:s A'), 0, 0, 'L');
            $this->Cell(0, 7, 'Pagina: ' . $this->PageNo() . "/{nb}", 0, 0, 'R');
        }

    }
}
