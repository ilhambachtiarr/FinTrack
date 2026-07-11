<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf {

    public function __construct()
    {
        // Load composer autoload
        if (file_exists(FCPATH . 'vendor/autoload.php')) {
            require_once FCPATH . 'vendor/autoload.php';
        }
    }

    /**
     * Generate PDF from HTML content
     *
     * @param string $html     The HTML content to render
     * @param string $filename The output filename
     */
    public function generate($html, $filename = 'laporan.pdf')
    {
        try {
            // Inisialisasi mPDF dengan format A4, margin 15mm (Atas, Bawah, Kiri, Kanan)
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_header' => 0,
                'margin_footer' => 10
            ]);

            // Set footer default (Halaman X dari Y)
            $mpdf->SetFooter('Dibuat otomatis oleh FinTrack pada ' . date('d/m/Y H:i') . '||Halaman {PAGENO} dari {nbpg}');

            // Write HTML content
            $mpdf->WriteHTML($html);

            // Output to browser inline
            $mpdf->Output($filename, 'I');
        } catch (\Mpdf\MpdfException $e) {
            echo "Error generating PDF: " . $e->getMessage();
        }
    }
}
