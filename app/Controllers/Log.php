<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Log extends BaseController
{
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->attendanceModel = new AttendanceModel();
    }

    public function index()
    {
        $data['title'] = "Log";
        $data['subtitle'] = "Log";
        $id = session('user_id');

        $data['user'] = $this->userModel
            ->select('users.*, categories.name as category_name')
            ->join('categories','categories.id = users.category_id','left')
            ->find($id);

        $data['attendancecount'] = $this->attendanceModel
            ->select("user_id,
                    COUNT(CASE WHEN status = 'Hadir' THEN 1 END) AS count_hadir, 
                    COUNT(CASE WHEN status = 'Ijin' THEN 1 END) AS count_ijin, 
                    COALESCE(MAX(CASE WHEN DATE(created_at) = CURDATE() THEN status END),'Tidak Hadir') AS status_today")
            ->where('user_id', $id)
            ->groupBy('user_id')
            ->first();

        return view('pages/log/list',$data);
    }

    public function getData()
    {
        $id = session('user_id');

        $currentDate = date('Y-m-d');

        $existingAttendance = $this->attendanceModel
            ->select('id, activity, description')
            ->where('user_id', $id)
            ->like('created_at', $currentDate, 'after')
            ->first();

        if(!$existingAttendance) {
            $output = [
                'status' => 'error',
                'message' => 'Anda belum absen atau mendapatkan ijin hari ini'
            ];
            return $this->response->setJSON($output);
        } else {
            $output = $existingAttendance;
            $output['status'] = 'success';
            return $this->response->setJSON($output);
        }
    }

    public function getDataId($id)
    {
        $currentDate = date('Y-m-d');

        $existing = $this->attendanceModel
            ->select('id, activity, description, created_at')
            ->where('id', $id)
            ->first();

        $output = [
            'id' => $existing['id'],
            'activity' => $existing['activity'],
            'description' => $existing['description'],
            'date' => date('Y-m-d',strtotime($existing['created_at'])),
            'status' => 'success',
        ];
        return $this->response->setJSON($output);
    }

    public function store()
    {
        $id = session('user_id');
        $currentDate = date('Y-m-d');

        $todayAttendance = $this->attendanceModel
            ->select('id, activity, description')
            ->where('user_id', $id)
            ->like('created_at', $currentDate, 'after')
            ->first();

        $data = [
            'activity' => $this->request->getPost('activity'),
            'description' => $this->request->getPost('description'),
        ];

        if ($this->attendanceModel->update($todayAttendance['id'], $data)) {
            return redirect()->back()->with('success', 'Log berhasil disimpan.');
        } else {
            return redirect()->back()->with('error', 'Log gagal disimpan.');
        }
    }
}
