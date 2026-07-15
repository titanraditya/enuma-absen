<?php

namespace App\Libraries;

/**
 * Laporan absensi/log berbasis FPDF (pengganti dompdf).
 * Butuh package: composer require setasign/fpdf
 */
class FpdfReport extends \FPDF
{
    protected $reportTitle = '';
    protected $headings = [];
    protected $widths = [];
    protected $lineHeight = 6;

    /**
     * Generate laporan dan tampilkan di browser.
     *
     * @param array  $data     ['title', 'user', 'periode', 'data_show', 'attendance']
     * @param string $filename Nama file PDF
     */
    public function generate(array $data, string $filename): void
    {
        $this->reportTitle = $data['title'];

        if ($data['data_show'] == '2') {
            $this->headings = ['Tanggal', 'Status', 'Waktu'];
            $this->widths   = [40, 60, 90];
        } else if ($data['data_show'] == '3') {
            $this->headings = ['Tanggal', 'Aktivitas', 'Deskripsi'];
            $this->widths   = [40, 60, 90];
        } else {
            $this->headings = ['Tanggal', 'Status', 'Waktu', 'Aktivitas', 'Deskripsi'];
            $this->widths   = [24, 27, 38, 41, 60];
        }

        $this->AliasNbPages();
        $this->SetMargins(10, 10);
        $this->SetAutoPageBreak(true, 15);
        $this->AddPage();
        $this->infoBlock($data['user'], $data['periode']);
        $this->tableHeader();

        $this->SetFont('Arial', '', 9);
        if (!$data['attendance']) {
            $this->Cell(array_sum($this->widths), $this->lineHeight + 2, 'Tidak ada data', 1, 1, 'C');
        } else {
            foreach ($data['attendance'] as $attendance) {
                $cells = [date('Y-m-d', strtotime($attendance['checkin']))];
                if ($data['data_show'] == '2') {
                    $cells[] = $attendance['status'];
                    $cells[] = $this->formatWaktu($attendance);
                } else if ($data['data_show'] == '3') {
                    $cells[] = $attendance['activity'];
                    $cells[] = $attendance['description'];
                } else {
                    $cells[] = $attendance['status'];
                    $cells[] = $this->formatWaktu($attendance);
                    $cells[] = $attendance['activity'];
                    $cells[] = $attendance['description'];
                }
                $this->row($cells);
            }
        }

        $this->Output('I', $filename);
    }

    public function Header()
    {
        $logo = FCPATH . 'enumatech2.png';
        if (is_file($logo)) {
            $this->Image($logo, 10, 8, 35);
        }
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, $this->enc($this->reportTitle), 0, 1, 'C');
        $this->Ln(4);

        if ($this->PageNo() > 1) {
            $this->tableHeader();
            $this->SetFont('Arial', '', 9);
        }
    }

    public function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120);
        $this->Cell(0, 8, 'Halaman ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->SetTextColor(0);
    }

    protected function infoBlock(array $user, string $periode): void
    {
        $this->SetFont('Arial', '', 10);
        $rows = [
            ['Nama', $user['name'], 'Tanggal Cetak', date('d-m-Y')],
            ['Grup', $user['category_name'], 'Periode', $periode],
        ];
        foreach ($rows as $row) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(25, 6, $this->enc($row[0]), 0, 0);
            $this->SetFont('Arial', '', 10);
            $this->Cell(5, 6, ':', 0, 0);
            $this->Cell(75, 6, $this->enc($row[1]), 0, 0);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(30, 6, $this->enc($row[2]), 0, 0);
            $this->SetFont('Arial', '', 10);
            $this->Cell(5, 6, ':', 0, 0);
            $this->Cell(50, 6, $this->enc($row[3]), 0, 1);
        }
        $this->Ln(4);
    }

    protected function tableHeader(): void
    {
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(242, 242, 242);
        foreach ($this->headings as $i => $heading) {
            $this->Cell($this->widths[$i], $this->lineHeight + 2, $this->enc($heading), 1, 0, 'C', true);
        }
        $this->Ln();
    }

    /** Satu baris tabel dengan tinggi mengikuti sel terpanjang (wrap). */
    protected function row(array $cells): void
    {
        $nb = 1;
        foreach ($cells as $i => $text) {
            $nb = max($nb, $this->nbLines($this->widths[$i], $this->enc($text)));
        }
        $h = $this->lineHeight * $nb;
        $this->checkPageBreak($h);

        foreach ($cells as $i => $text) {
            $w = $this->widths[$i];
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, $this->lineHeight, $this->enc($text), 0, 'L');
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }

    protected function checkPageBreak(float $h): void
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }
    }

    /** Jumlah baris yang dibutuhkan MultiCell selebar $w untuk $txt. */
    protected function nbLines(float $w, string $txt): int
    {
        $cw = $this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', (string) $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

    protected function formatWaktu(array $attendance): string
    {
        if ($attendance['status'] == 'Ijin') {
            return '-';
        }
        if ($attendance['no_limit'] == '1') {
            return date('H:i:s', strtotime($attendance['checkin']));
        }
        if ($attendance['checkout'] == null && date('Y-m-d', strtotime($attendance['created_at'])) == date('Y-m-d')) {
            return date('H:i:s', strtotime($attendance['checkin'])) . ' - Belum Pulang';
        }
        if ($attendance['checkout'] == null) {
            return date('H:i:s', strtotime($attendance['checkin'])) . ' - Tidak Absen';
        }
        return date('H:i:s', strtotime($attendance['checkin'])) . ' - ' . date('H:i:s', strtotime($attendance['checkout']));
    }

    /** FPDF memakai encoding windows-1252, bukan UTF-8. */
    protected function enc($text): string
    {
        $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT', (string) $text);
        return $converted === false ? (string) $text : $converted;
    }
}
