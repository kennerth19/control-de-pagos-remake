<?php

namespace App\Http\Controllers;

use App\Models\pago_resumen;
use App\Models\clientes;

use App\fpdf\FPDF;
use App\fpdf\KodePDF;
use App\fpdf\PDF_HTML;

date_default_timezone_set('America/Caracas');

class imprimir_datos extends Controller
{
    public function imprimir_pm()
    {
        $pdf = new KodePDF('P', 'mm', array(75, 75));
        $pdf->AddPage();

        $pdf->Image('http://localhost/pm.jpeg', 0, 0, -340);

        $pdf->Output('I', 'HOLA.pdf');
        exit;
    }

    public function imprimir_z()
    {
        $pdf = new KodePDF('P', 'mm', array(75, 75));
        $pdf->AddPage();

        $pdf->Image('http://localhost/zl.jpeg', 0, 0, -340);

        $pdf->Output('I', 'HOLA.pdf');
        exit;
    }

    public function imprimir_factura($id)
    {
        $factura = pago_resumen::findOrFail($id);
        $bolivares = round(($factura->total * $factura->tasa),2);
        $status = "";

        if($factura->id_cliente > 0){
            $cliente_datos = clientes::findOrFail($factura->id_cliente);
            $fecha_de_corte = date('d-m-Y', strtotime($cliente_datos->corte));

            if($cliente_datos->estado == 1){
                $status = "SOLVENTE";
            }else if($cliente_datos->estado == 2){
                $status = "RESTAN 2 DIAS";
            }else if($cliente_datos->estado == 3){
                $status = "RESTA UN DIA";
            }else if($cliente_datos->estado == 4){
                $status = "DIA DE CORTE";
            }else if($cliente_datos->estado == 5){
                $status = "CLIENTE SUSPENDIDO";
            }else if($cliente_datos->estado == 6){
                $status = "PRORROGA DIA 1 / 2";
            }else if($cliente_datos->estado == 7){
                $status = "PRORROGA DIA 2 / 2";
            }

            if($cliente_datos->ticket == 0){
                $total = "TOTAL CANCELADO: $bolivares Bs.";
            }else{
                $total = "TOTAL CANCELADO: $factura->total$.";
            }
        }else{
            $total = "TOTAL CANCELADO: $bolivares Bs.";
        }

        $pdf = new PDF_HTML();
        $pdf->AddPage('P', array(80, 130));
        $pdf->AddFont('Pixeland', '');
        $pdf->SetLeftMargin(3);
        $pdf->SetRightMargin(2);

        // Variables
        $fecha_de_pago = date('d-m-Y', strtotime($factura->pago));
        $titulo = iconv('UTF-8', 'windows-1252', 'Informática Express C.A');
        $nota_de_entrega = "Nota de Entrega $factura->codigo";
        $direccion = iconv('UTF-8', 'windows-1252', "Calle Bermúdez C/C Carabobo Nivel P.B Local 03 Puerto Cabello Edo. Carabobo.");
        $cobrador = iconv('UTF-8', 'windows-1252', "Cobrador: $factura->cobrador.");
        $pago = iconv('UTF-8', 'windows-1252', "Fecha de pago: $fecha_de_pago.");
        $cliente = iconv('UTF-8', 'windows-1252', "Cliente: $factura->cliente");
        $concepto = iconv('UTF-8', 'windows-1252', "Concepto: $factura->concepto");
        $cliente_direccion = iconv('UTF-8', 'windows-1252', "$factura->direccion");
        $cedula = iconv('UTF-8', 'windows-1252', "C.I o R.I.F: $factura->cedula");
        $telefono = iconv('UTF-8', 'windows-1252', "Teléfono: $factura->telefono");
        
        // Titulo
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->setY($pdf->getX() - 1);
        $pdf->WriteHTML("<p align='center'>$titulo</p>");

        // RIF
        $pdf->SetFont('helvetica', '', 10);
        $pdf->text(45, $pdf->getX() + 16, "R.I.F J-40522311-1");

        // Nota de entrega
        $pdf->setY($pdf->getY() + 7);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->WriteHTML("<p align='left'>$nota_de_entrega</p>");

        // Dirección
        $pdf->setY($pdf->getY() + 5);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->WriteHTML("<p align='left'>$direccion</p>");

        // Cobrador
        $pdf->setY($pdf->getY() + 5);
        $pdf->WriteHTML("<p align='left'>$cobrador</p>");

        // Fecha de pago
        $pdf->setY($pdf->getY() + 5);
        $pdf->WriteHTML("<p align='left'>$pago</p>");

        // Linea separadora
        $pdf->setY($pdf->getY() + 5);
        $pdf->WriteHTML("<hr>");

        // Datos del cliente
        $pdf->setY($pdf->getY());
        $pdf->WriteHTML("<p align='left'>$cliente.</p><br>");
        
        if($factura->id_cliente > 0){
            $pdf->WriteHTML("<p align='left'>$cliente_direccion.</p><br>");
        }

        $pdf->WriteHTML("<p align='left'>$cedula.</p><br>");
        $pdf->WriteHTML("<p align='left'>$telefono.</p><br><br>");

        // Linea separadora
        $pdf->setY($pdf->getY() - 5);

        if($factura->tipo == 0){
            $pdf->SetFont('Pixeland', '', 16);
            $pdf->WriteHTML("<p align='left'>Estado: $status</p><br>");
            $pdf->SetFont('helvetica', '', 10);
        }

        if($factura->id_cliente > 0){
            // Datos de mensualidad y tipo de pago
            $pdf->setY($pdf->getY());
            $pdf->WriteHTML("<p align='left'>Internet Solvente Hasta: $fecha_de_corte</p><br><p align='left'>$concepto.</p><br>");
        }else{
            $pdf->setY($pdf->getY());
            $pdf->WriteHTML("<p align='left'><p align='left'>$concepto.</p><br>");
        }
        
        // Linea separadora
        $pdf->setY($pdf->getY());
        $pdf->WriteHTML("<hr><br>");

        // Total cancelado
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->setY($pdf->getY() - 3);
        $pdf->WriteHTML("<p align='center'>$total</p>");

        // Código QR

        //$pdf->Image('qr.png', 33, $pdf->getY() + 3, 15, 15);
        $pdf->Output('D');
    }
}