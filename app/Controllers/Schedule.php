<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ScheduleModel;
use CodeIgniter\HTTP\ResponseInterface;

class Schedule extends BaseController
{
    protected $scheduleModel;

    public function __construct()
    {
        $this->scheduleModel = new ScheduleModel();
    }

    public function check($id): ResponseInterface
    {
        $data = $this->scheduleModel->find($id);
        return response()->setJSON($data);
    }

    public function store()
    {
        if($this->request->getPost('no_limit')) {
            $no_limit = '1';
        } else {
            $no_limit = '0';
        }

        if($this->request->getPost('online')) {
            $online = '1';
        } else {
            $online = '0';
        }

        $input = [
            'category_id'   => $this->request->getPost('category_id'),
            'day'           => $this->request->getPost('day'),
            'no_limit'      => $no_limit,
            'online'        => $online,
            'start_time'    => $this->request->getPost('start_time'),
            'end_time'      => $this->request->getPost('end_time'),
        ];

        // echo $input['category_id'];
        if($this->scheduleModel->insert($input)) {
            return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('error', 'Jadwal gagal ditambahkan.');
        }
    }

    public function editpost()
    {
        if($this->request->getPost('no_limit')) {
            $no_limit = '1';
        } else {
            $no_limit = '0';
        }

        if($this->request->getPost('online')) {
            $online = '1';
        } else {
            $online = '0';
        }
        
        $id = $this->request->getPost('id');

        $input = [
            'no_limit'      => $no_limit,
            'online'        => $online,
            'start_time'    => $this->request->getPost('start_time'),
            'end_time'      => $this->request->getPost('end_time'),
        ];

        // echo $input['category_id'];
        if($this->scheduleModel->update($id,$input)) {
            return redirect()->back()->with('success', 'Jadwal berhasil diubah.');
        } else {
            return redirect()->back()->with('error', 'Jadwal gagal diubah.');
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        
        if($this->scheduleModel->where('id',$id)->delete()) {
            return redirect()->back()->with('success', 'Jadwal berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal Menghapus Jadwal');
        }
    }
}
