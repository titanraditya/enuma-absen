<?php
namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    protected $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);

        $this->dompdf = new Dompdf($options);
    }
    /**
     * Generate PDF dari HTML
     *
     * @param string $html HTML yang akan dikonversi
     * @param string $filename Nama file PDF
     * @param bool $stream Jika true, tampilkan di browser; jika false, return output sebagai string
     * @return void|string
     */
    public function generate(string $html, string $filename = 'document.pdf', bool $stream = true)
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        if ($stream) {
            $this->dompdf->stream($filename, ['Attachment' => false]);
        } else {
            return $this->dompdf->output();
        }
    }
}