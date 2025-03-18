<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\UserModel;
use App\Models\ScheduleModel;
use App\Libraries\PdfGenerator;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use DateTime;

class Attendance extends BaseController
{
    protected $attendanceModel;
    protected $userModel;
    protected $scheduleModel;

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->userModel = new UserModel();
        $this->scheduleModel = new ScheduleModel();
    }

    public function printxls()
    {
        $tgl_awal = $this->request->getPost('min');
        $tgl_akhir = $this->request->getPost('max');
        $id = $this->request->getPost('id');

        $user = $this->userModel
            ->select('users.*, categories.name as category_name')
            ->join('categories', 'users.category_id = categories.id', 'left')
            ->find($id);

        if ($tgl_awal == '' && $tgl_akhir == '') {
            $attendance = $this->attendanceModel
                ->where('user_id', $id)
                ->orderBy('created_at','DESC')
                ->findAll();
            
        } else {
            $start_date = $tgl_awal;
            $dt = new DateTime($tgl_akhir);
		    $dt->modify('+1 day');
            $end_date = $dt->format('Y-m-d');

            $attendance = $this->attendanceModel
                ->where('user_id', $id)
                ->where('created_at >=',$start_date)
                ->where('created_at <=',$end_date)
                ->orderBy('created_at','DESC')
                ->findAll();
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getProperties()
            ->setCreator("Enuma Technology")
            ->setLastModifiedBy("Enuma Technology")
            ->setSubject("Laporan Absen dan Log")
            ->setTitle("Laporan Absen dan Berita Acara")
            ->setCategory("Laporan");

        $sheet->setCellValue('A1', 'Laporan Absensi dan Berita Acara');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Anggota Enuma Technology');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A3', 'Nama');
        $sheet->setCellValue('B3', ': '.$user['name']);
        $sheet->mergeCells('B3:H3');

        $sheet->setCellValue('A4', 'Grup');
        $sheet->setCellValue('B4', ': '.$user['category_name']);
        $sheet->mergeCells('B4:H4');

        $sheet->setCellValue('A5', 'Waktu');
        if ($tgl_awal == "") {
            $outputtglawal = "-";    
        } else {
            $outputtglawal = $tgl_awal;
        }

        if ($tgl_akhir == "") {
            $outputtglakhir = "-";    
        } else {
            $outputtglakhir = $tgl_akhir;
        }
        $sheet->setCellValue('B5', ': '.$outputtglawal." sd. ".$outputtglakhir);
        $sheet->mergeCells('B5:H5');

        // Isi data ke dalam spreadsheet
        $sheet->setCellValue('A7', 'Tanggal');
        $sheet->setCellValue('B7', 'Status');
        $sheet->setCellValue('C7', 'Waktu');
        $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A7')->getFont()->setBold(true);
        $sheet->getStyle('B7')->getFont()->setBold(true);
        $sheet->getStyle('C7')->getFont()->setBold(true);
        $sheet->getStyle("A7:C7")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

        $row = 8;
        foreach ($attendance as $attendance) {

            $sheet->setCellValue("A$row", date('Y-m-d',strtotime($attendance['created_at'])));
            $sheet->setCellValue("B$row", $attendance['status']);

            if($attendance['status'] == 'Ijin') {
                $waktu = "-";
            } else {
                if($attendance['no_limit'] == '1') {
                    $waktu = date('H:i:s',strtotime($attendance['checkin']));
                } else {
                    if($attendance['checkout'] == null && date('Y-m-d',strtotime($attendance['created_at'])) == date("Y-m-d")) {
                        $waktu = date('H:i:s',strtotime($attendance['checkin']))." - Belum Pulang";
                    } else if($attendance['checkout'] == null) {
                        $waktu = date('H:i:s',strtotime($attendance['checkin']))." - Tidak Absen";
                    } else {
                        $waktu = date('H:i:s',strtotime($attendance['checkin']))." - ".date('H:i:s',strtotime($attendance['checkout']));
                    }
                }
            }
            $sheet->setCellValue("C$row", $waktu);
            $sheet->getStyle("A$row:C$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            $row++;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$user['name'].'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function printpdf()
    {
        $tgl_awal = $this->request->getPost('min');
        $tgl_akhir = $this->request->getPost('max');
        $id = $this->request->getPost('id');

        $user = $this->userModel
            ->select('users.*, categories.name as category_name')
            ->join('categories', 'users.category_id = categories.id', 'left')
            ->find($id);

        if ($tgl_awal == '' && $tgl_akhir == '') {
            $queryattendance = $this->attendanceModel
                ->where('user_id', $id);
            $data['periode'] = 'Semua';
        } else {
            $start_date = $tgl_awal;
            $dt = new DateTime($tgl_akhir);
		    $dt->modify('+1 day');
            $end_date = $dt->format('Y-m-d');

            $queryattendance = $this->attendanceModel
                ->where('user_id', $id)
                ->where('created_at >=',$start_date)
                ->where('created_at <=',$end_date);

            if($tgl_awal == '') {
                $tgl_awal = 'Awal';
            }
            if($tgl_akhir == '') {
                $tgl_awal = 'Sekarang';
            }

            $data['periode'] = $tgl_awal.' sd. '.$tgl_akhir;
        }

        $order = $this->request->getPost('order_by');

        if($order == '1') {
            $queryattendance = $queryattendance->orderBy('created_at','ASC');
        } else if($order == '2') {
            $queryattendance = $queryattendance->orderBy('created_at','DESC');
        } else if($order == '3') {
            $queryattendance = $queryattendance->orderBy('status','ASC');
        } else if($order == '4') {
            $queryattendance = $queryattendance->orderBy('status','DESC');
        }

        $attendance = $queryattendance->findAll();

        $data['attendance'] = $attendance;
        $data['user'] = $user;
        $data['data_show'] = $this->request->getPost('data_show');

        
        $html = view('layout/laporan_layout',$data);

        $pdf = new PdfGenerator();
        $pdf->generate($html, 'Laporan Absensi - '.$user['name'].'.pdf', true);
    }

    public function ijin()
    {
        $id = $this->request->getPost('id');
        $user = $this->userModel
            ->find($id);

        if(!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $currentDay = date('l');
        $currentDate = date('Y-m-d');

        $schedule = $this->scheduleModel
            ->where('category_id', $user['category_id'])
            ->where('day', $currentDay)
            ->first();

        if (!$schedule) {
            session()->setFlashdata('error','Ijin Gagal! Tidak ada jadwal hari ini');
            return redirect()->back();
        }

        $existingAttendance = $this->attendanceModel
            ->where('user_id', $id)
            ->like('created_at', $currentDate, 'after')
            ->first();

        if ($existingAttendance) {
            if ($existingAttendance['status'] == "Ijin") {
                session()->setFlashdata('error','Ijin Gagal! Anggota sudah ijin');
                return redirect()->back();
            } else {
                session()->setFlashdata('error','Ijin Gagal! Anggota sudah absen');
                return redirect()->back();
            }
        }
        $data = [
            'user_id' => $id,
            'status'  => 'Ijin',
        ];
    
        if ($this->attendanceModel->insert($data)) {
            session()->setFlashdata('success', 'Pemberian Ijin sukses');
            return redirect()->back();
        } else {
            session()->setFlashdata('error', 'Ijin Gagal!');
            return redirect()->back();
        }
        
    }
    
    public function cancelijin()
    {
        $id = $this->request->getPost('id');
        $user = $this->userModel
            ->find($id);

        if(!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $currentDay = date('l');
        $currentDate = date('Y-m-d');

        $existingAttendance = $this->attendanceModel
            ->where('user_id', $id)
            ->like('created_at', $currentDate, 'after')
            ->first();

        if ($existingAttendance) {
            if ($existingAttendance['status'] == "Ijin") {
                if ($this->attendanceModel->where('id',$existingAttendance['id'])->delete()) {
                    session()->setFlashdata('success','Pembatalan ijin sukses!');
                    return redirect()->back();
                } else {
                    session()->setFlashdata('error','Pembatalan ijin gagal! Something Wrong!');
                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('error','Pembatalan ijin gagal! Anggota sudah absen');
                return redirect()->back();
            }
        } else {
            session()->setFlashdata('error','Pembatalan ijin gagal! Tidak ada ijin hari ini');
            return redirect()->back();
        }
    }

    public function userattendance($id,$date) {
        if ($date == 'all') {
            $attendance = $this->attendanceModel
                ->where('user_id', $id)
                ->orderBy('created_at','DESC')
                ->findAll();
            
        } else {
            $date = explode(":", $date);
            $date[0];
            $start_date = $date[0];
            $dt = new DateTime($date[1]);
		    $dt->modify('+1 day');
            $end_date = $dt->format('Y-m-d');

            $attendance = $this->attendanceModel
                ->where('user_id', $id)
                ->where('created_at >=',$start_date)
                ->where('created_at <=',$end_date)
                ->orderBy('created_at','DESC')
                ->findAll();
        }
        $output = array();
        foreach($attendance as $attendance){
            $data['tanggal'] = date('Y-m-d',strtotime($attendance['created_at']));
            $data['status'] = $attendance['status'];
            $data['id'] = $attendance['id'];

            
            if($attendance['status'] == 'Ijin') {
                $data['waktu'] = "-";
            } else {
                if($attendance['no_limit'] == '1') {
                    $data['waktu'] = date('H:i:s',strtotime($attendance['checkin']));
                } else {
                    if($attendance['checkout'] == null && date('Y-m-d',strtotime($attendance['created_at'])) == date("Y-m-d")) {
                        $data['waktu'] = date('H:i:s',strtotime($attendance['checkin']))." - Belum Pulang";
                    } else if($attendance['checkout'] == null) {
                        $data['waktu'] = date('H:i:s',strtotime($attendance['checkin']))." - Tidak Absen";
                    } else {
                        $data['waktu'] = date('H:i:s',strtotime($attendance['checkin']))." - ".date('H:i:s',strtotime($attendance['checkout']));
                    }
                }
            }
            array_push($output, $data);
        }
        return $this->response->setJSON($output);
    }
}
