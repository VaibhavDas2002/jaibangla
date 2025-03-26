<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCPDF;
use TCPDF_FONTS;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class ValidationPandingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
    }

    public function PDFGeneratorAction(Request $request)
    {
        $on_date = '2021-07-01';
        $on_time = '11:15 AM';
        $on_office = 'Block';
        $on_body = 'Suri';
        $on_dist = 'Birbhum';
        $ben_id = 125252;

        // Initialize TCPDF object
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetAuthor('Jai Bangla');
        $pdf->SetTitle('Beneficiary Invitation Letter');
        $pdf->SetSubject('Beneficiary Invitation Letter PDF');
        $pdf->SetMargins(10, 5, 10);
        $pdf->AddPage();

        // Convert and register the Bengali font (Run this only once)
        $pdf->SetFont('solaimanlipi_22022012', '', 8, '', 'true');
        
        $i= 1;
        $beng_text ='পশ্চিমবঙ্গ সরকার';
        // Generate HTML content
        // $pdfContent = View::make('ValidationPendingPDF.index', [
        //     'on_date' => $on_date,
        //     'ben_id' => $ben_id,
        //     'on_time' => $on_time,
        //     'on_office' => $on_office,
        //     'on_body' => $on_body,
        //     'on_dist' => $on_dist,
        //     'i' => $i,
        //     'beng_text' => $beng_text
        // ])->render();
        return view('ValidationPendingPDF/index', ['on_date' => $on_date,
            'ben_id' => $ben_id,
            'on_time' => $on_time,
            'on_office' => $on_office,
            'on_body' => $on_body,
            'on_dist' => $on_dist,
            'i' => $i
        ]);
        // Write HTML content to PDF
        $pdf->writeHTML($pdfContent, true, false, true, false, '');

        // Output PDF
        return $pdf->Output('Beneficiary_invitation_letter_' . $ben_id . '.pdf', 'D');
    }
}
