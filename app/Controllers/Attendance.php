<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\UserModel;
use App\Models\ScheduleModel;
use App\Libraries\FpdfReport;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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

    public function addlogs()
    {
        $id = $this->request->getPost('id');
        $date = $this->request->getPost('date');
        $time = $this->request->getPost('time');
        $status = $this->request->getPost('status');
        $activity = $this->request->getPost('activity');
        $description = $this->request->getPost('description');

        $user = $this->userModel
            ->find($id);

        if(!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $existingAttendance = $this->attendanceModel
            ->where('user_id', $id)
            ->like('checkin', $date, 'after')
            ->first();
    
        if ($existingAttendance) {
            session()->setFlashdata('error', 'Log Sudah Dibuat!');
            return redirect()->back();
        }

        $data = [
            'user_id' => $id,
            'checkin'  => $date." ".$time,
            'status' => $status,
            'activity' => $activity,
            'description' => $description,
            'no_limit' => "1",
        ];

        if ($this->attendanceModel->insert($data)) {
            session()->setFlashdata('success', 'Penambahan Log Berhasil!');
            return redirect()->back();
        } else {
            session()->setFlashdata('error', 'Penambahan Log Gagal!');
            return redirect()->back();
        }
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
            $queryattendance = $this->attendanceModel
                ->where('user_id', $id);
            $periode = 'Semua';
        } else {
            $start_date = $tgl_awal;
            $dt = new DateTime($tgl_akhir);
		    $dt->modify('+1 day');
            $end_date = $dt->format('Y-m-d');

            $queryattendance = $this->attendanceModel
                ->where('user_id', $id)
                ->where('checkin >=',$start_date)
                ->where('checkin <=',$end_date);

            if($tgl_awal == '') {
                $tgl_awal = 'Awal';
            }
            if($tgl_akhir == '') {
                $tgl_akhir = 'Sekarang';
            }

            $periode = $tgl_awal.' sd. '.$tgl_akhir;
        }

        $order = $this->request->getPost('order_by');

        if($order == '1') {
            $queryattendance = $queryattendance->orderBy('checkin','ASC');
        } else if($order == '2') {
            $queryattendance = $queryattendance->orderBy('checkin','DESC');
        } else if($order == '3') {
            $queryattendance = $queryattendance->orderBy('status','ASC');
        } else if($order == '4') {
            $queryattendance = $queryattendance->orderBy('status','DESC');
        }

        $attendance = $queryattendance->findAll();

        $data_show = $this->request->getPost('data_show');
        
        if ($data_show == '1') {
            $title = "Laporan Absensi dan Log";
        } else if ($data_show == '2') {
            $title = "Laporan Absensi";
        } else if ($data_show == '3') {
            $title = "Laporan Log";
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $spreadsheet->getProperties()
            ->setCreator("Enuma Technology")
            ->setLastModifiedBy("Enuma Technology")
            ->setSubject($title)
            ->setTitle($title."-".$user['name'])
            ->setCategory("Laporan");

        $sheet->setTitle($title);
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Enuma Technology');
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Nama');
        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->setCellValue('B4', ': '.$user['name']);
        $sheet->mergeCells('B4:H4');

        $sheet->setCellValue('A5', 'Grup');
        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->setCellValue('B5', ': '.$user['category_name']);
        $sheet->mergeCells('B5:E5');

        $sheet->setCellValue('A6', 'Waktu Cetak');
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->setCellValue('B6', ': '.date('d-m-Y'));
        $sheet->mergeCells('B6:E6');

        $sheet->setCellValue('A7', 'Periode');
        $sheet->getStyle('A7')->getFont()->setBold(true);
        $sheet->setCellValue('B7', ': '.$periode);
        $sheet->mergeCells('B7:E7');

        // Isi data ke dalam spreadsheet
        $sheet->setCellValue('A9', 'Tanggal');
        $sheet->getStyle('A9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A9')->getFont()->setBold(true);
        if($data_show == '1') {
            $sheet->setCellValue('B9', 'Status');
            $sheet->setCellValue('C9', 'Waktu');
            $sheet->setCellValue('D9', 'Aktivitas');
            $sheet->setCellValue('E9', 'Deskripsi');
            $sheet->getStyle('B9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B9')->getFont()->setBold(true);
            $sheet->getStyle('C9')->getFont()->setBold(true);
            $sheet->getStyle('D9')->getFont()->setBold(true);
            $sheet->getStyle('E9')->getFont()->setBold(true);
            $sheet->getStyle("A9:E9")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            $sheet->getStyle("A9:E9")->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('f2f2f2'));
        } else if($data_show == '2') {
            $sheet->setCellValue('B9', 'Status');
            $sheet->setCellValue('C9', 'Waktu');
            $sheet->getStyle('B9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B9')->getFont()->setBold(true);
            $sheet->getStyle('C9')->getFont()->setBold(true);
            $sheet->getStyle("A9:C9")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            $sheet->getStyle("A9:C9")->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('f2f2f2'));
        } else if($data_show == '3') {
            $sheet->setCellValue('B9', 'Aktivitas');
            $sheet->setCellValue('C9', 'Deskripsi');
            $sheet->getStyle('B9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B9')->getFont()->setBold(true);
            $sheet->getStyle('C9')->getFont()->setBold(true);
            $sheet->getStyle("A9:C9")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            $sheet->getStyle("A9:C9")->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('f2f2f2'));
        }

        $row = 10;
        foreach ($attendance as $attendance) {
            $sheet->setCellValue("A$row", date('Y-m-d',strtotime($attendance['checkin'])));
            if($data_show == '1') {
                $sheet->setCellValue("B$row", $attendance['status']);
    
                if($attendance['status'] == 'Ijin') {
                    $waktu = "-";
                } else {
                    if($attendance['no_limit'] == '1') {
                        $waktu = date('H:i:s',strtotime($attendance['checkin']));
                    } else {
                        if($attendance['checkout'] == null && date('Y-m-d',strtotime($attendance['checkin'])) == date("Y-m-d")) {
                            $waktu = date('H:i:s',strtotime($attendance['checkin']))." - Belum Pulang";
                        } else if($attendance['checkout'] == null) {
                            $waktu = date('H:i:s',strtotime($attendance['checkin']))." - Tidak Absen";
                        } else {
                            $waktu = date('H:i:s',strtotime($attendance['checkin']))." - ".date('H:i:s',strtotime($attendance['checkout']));
                        }
                    }
                }
                $sheet->setCellValue("C$row", $waktu);
                $sheet->setCellValue("D$row", $attendance['activity']);
                $sheet->setCellValue("E$row", $attendance['description']);
                $sheet->getStyle("A$row:E$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            } else if($data_show == '2') {
                $sheet->setCellValue("B$row", $attendance['status']);
    
                if($attendance['status'] == 'Ijin') {
                    $waktu = "-";
                } else {
                    if($attendance['no_limit'] == '1') {
                        $waktu = date('H:i:s',strtotime($attendance['checkin']));
                    } else {
                        if($attendance['checkout'] == null && date('Y-m-d',strtotime($attendance['checkin'])) == date("Y-m-d")) {
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
            } else if($data_show == '3') {
                $sheet->setCellValue("B$row", $attendance['activity']);
                $sheet->setCellValue("C$row", $attendance['description']);
                $sheet->getStyle("A$row:C$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            }
            $row++;
        }

        foreach (range('A','E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title." - ".$user['name'].' ('.date('d-m-Y').').xlsx"');
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
                ->where('checkin >=',$start_date)
                ->where('checkin <=',$end_date);

            if($tgl_awal == '') {
                $tgl_awal = 'Awal';
            }
            if($tgl_akhir == '') {
                $tgl_akhir = 'Sekarang';
            }

            $data['periode'] = $tgl_awal.' sd. '.$tgl_akhir;
        }

        $order = $this->request->getPost('order_by');

        if($order == '1') {
            $queryattendance = $queryattendance->orderBy('checkin','ASC');
        } else if($order == '2') {
            $queryattendance = $queryattendance->orderBy('checkin','DESC');
        } else if($order == '3') {
            $queryattendance = $queryattendance->orderBy('status','ASC');
        } else if($order == '4') {
            $queryattendance = $queryattendance->orderBy('status','DESC');
        }

        $attendance = $queryattendance->findAll();

        $data['attendance'] = $attendance;
        $data['user'] = $user;
        $data['data_show'] = $this->request->getPost('data_show');

        if($data['data_show'] == '1') {
            $title = "Laporan Absensi dan Log";
        } else if($data['data_show'] == '2') {
            $title = "Laporan Absensi";
        } else if($data['data_show'] == '3') {
            $title = "Laporan Log";
        }

        $data['title'] = $title;

        $pdf = new FpdfReport();
        $pdf->generate($data, $title.' - '.$user['name'].' ('.date('d-m-Y').').pdf');
        exit();
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
            ->like('checkin', $currentDate, 'after')
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
            'checkin' => date('Y-m-d H:i:s'),
        ];

        if ($this->attendanceModel->insert($data)) {
            session()->setFlashdata('success', 'Pemberian Ijin sukses');
            return redirect()->back();
        } else {
            session()->setFlashdata('error', 'Ijin Gagal!');
            return redirect()->back();
        }
        
    }

    public function deletelog()
    {
        $id = $this->request->getPost('id');
        $attd = $this->attendanceModel
            ->find($id);

        if(!$attd) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->attendanceModel->delete($id)) {
            session()->setFlashdata('success', 'Hapus Log Berhasil!');
            return redirect()->back();
        } else {
            session()->setFlashdata('error', 'Hapus Log Gagal!');
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
            ->like('checkin', $currentDate, 'after')
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
                ->orderBy('checkin','DESC')
                ->findAll();
            
        } else {
            $date = explode(":", $date);
            $date[0];
            $start_date = $date[0];
            if($date[1] == "sekarang") {
                $dt = new DateTime(date('Y-m-d'));
            } else {
                $dt = new DateTime($date[1]);
            }
		    $dt->modify('+1 day');
            $end_date = $dt->format('Y-m-d');

            $attendance = $this->attendanceModel
                ->where('user_id', $id)
                ->where('checkin >=',$start_date)
                ->where('checkin <=',$end_date)
                ->orderBy('checkin','DESC')
                ->findAll();
        }
        $output = array();
        foreach($attendance as $attendance){
            $data['tanggal'] = date('Y-m-d',strtotime($attendance['checkin']));
            $data['status'] = $attendance['status'];
            $keterangan = [];

            if ($attendance['terlambat'] == '1') {
                $keterangan[] = "Terlambat";
            }
            if ($attendance['pulcep'] == '1') {
                $keterangan[] = "Pulang Cepat";
            }
            if ($attendance['lembur'] == '1') {
                $keterangan[] = "Lembur";
            }

            $data['keterangan'] = !empty($keterangan) ? implode(", ", $keterangan) : "-";
            $data['id'] = $attendance['id'];

            
            if($attendance['status'] == 'Ijin') {
                $data['waktu'] = "-";
            } else {
                if($attendance['no_limit'] == '1') {
                    $data['waktu'] = date('H:i:s',strtotime($attendance['checkin']));
                } else {
                    if($attendance['checkout'] == null && date('Y-m-d',strtotime($attendance['checkin'])) == date("Y-m-d")) {
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
