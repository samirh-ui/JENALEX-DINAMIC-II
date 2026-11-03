<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function equipos()
    {
        $equipos = Equipo::query()
            ->orderBy('id')
            ->get();

        $pdf = Pdf::loadView('pdf.equipos', compact('equipos'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream('reporte-equipos.pdf');
    }
}
