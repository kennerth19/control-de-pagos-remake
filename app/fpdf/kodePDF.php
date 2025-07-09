<?php
namespace App\fpdf;
use App\fpdf\FPDF;

class KodePDF extends fpdf {
    protected $fontName = 'Arial';

    //Titulo de informatica express
    function renderTitle($text) {
        $this->SetFont($this->fontName, 'B', 18);
        $this->Cell(60,4, utf8_decode($text),0,1,'C');
    }

    function renderTitle_n($text) {
        $this->SetFont($this->fontName, 'B', 12);
        $this->Cell(70,4, utf8_decode($text), 0, 1,'R');
    }

    function renderText($text) {
        $this->SetLeftMargin(1);
        $this->SetRightMargin(1);
        $this->SetFont($this->fontName, '', 10);
        $this->Cell(60,4, utf8_decode($text), 0, 1,'J');
    }

    function renderText_date($text) {
        $this->SetLeftMargin(1);
        $this->SetRightMargin(1);
        $this->SetFont($this->fontName, '', 10);
        $this->MultiCell(80,4,utf8_decode($text),0,'J',0,8);
    }

    function renderText_plan($text) {
        $this->SetLeftMargin(1);
        $this->SetRightMargin(1);
        $this->SetFont($this->fontName, '', 10);
        $this->MultiCell(80,4,utf8_decode($text),0,'J',0,8);
    }

    function renderText_dir($text) {
        $this->SetLeftMargin(1);
        $this->SetRightMargin(1);
        $this->SetFont($this->fontName, '', 10);
        $this->MultiCell(80,4,utf8_decode($text),0,'L',0,8);
    }

    function renderText_dir_mt($text) {
        $this->SetLeftMargin(1);
        $this->SetRightMargin(1);
        $this->Ln(1.5);
        $this->SetFont($this->fontName, '', 8);
        $this->MultiCell(80,4,utf8_decode($text),0,'C',0,8);
    }

    //linea separadora con guiones "-------"
    function renderText_ln($text) {
        $this->SetFont($this->fontName, '', 12);
        $this->Cell(60,4, utf8_decode($text), 0, 1,'J');
    }
}
?>