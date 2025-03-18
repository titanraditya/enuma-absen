<?php

namespace App\Controllers;

use App\Models\UserModel;

class Admin extends BaseController
{
    protected $userModel;
    protected $rules;

    public function __construct() 
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data['title'] = "Daftar Admin";
        $data['subtitle'] = "Admin";

        $data['admin'] = $this->userModel->getAllAdmin();

        return view('pages/admin/list',$data);
    }

    public function add()
    {
        $data['title'] = "Tambah Admin";
        $data['subtitle'] = "Admin";

        return view('pages/admin/add',$data);
    }

    public function edit($id)
    {
        $data['title'] = "Ubah Data Admin";
        $data['subtitle'] = "Admin";

        $admin = $this->userModel->find($id);

        if (!$admin) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        } else {
            if($admin['category_id'] != "1") {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

        $data['admin'] = $admin;

        return view('pages/admin/edit',$data);
    }

    public function addpost()
    {
        $rules = $this->userModel->admin_insert_valid();
        if (!$this->validate($rules)) {
            session()->setFlashdata('errname',$this->validator->getError('name'));
            session()->setFlashdata('errusername',$this->validator->getError('username'));
            session()->setFlashdata('errpassword',$this->validator->getError('password'));
            if ($this->validator->listErrors()) {
                session()->setFlashdata('error','Gagal Menyimpan Data!');
            }
            return redirect()->back()->withInput();
        }
        helper('uuidv4');
        $data = [
            'id' => uuidv4(),
            'name' => $this->request->getPost('name'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'category_id' => '1',
            'can_login' => '1',
            'active' => '1',
        ];

        if($this->userModel->insert($data)) {
            return redirect()->to('admin')->with('success', 'Admin berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('error', 'Gagal Menyimpan Data!');
        }

    }

    public function editpost($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if($this->request->getPost('username') == $user['username']) {
            $rules = $this->userModel->admin_update_valid();
        } else {
            $rules = $this->userModel->admin_update_valid_with_username();
        }

        if (!$this->validate($rules)) {
            session()->setFlashdata('errname',$this->validator->getError('name'));
            session()->setFlashdata('errusername',$this->validator->getError('username'));
            session()->setFlashdata('errpassword',$this->validator->getError('password'));
            if ($this->validator->listErrors()) {
                session()->setFlashdata('error','Gagal Menyimpan Data!');
            }
            return redirect()->back()->withInput();
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'username' => $this->request->getPost('username'),
        ];

        if ($password = $this->request->getPost('password')) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if($this->userModel->update($id,$data)) {
            return redirect()->back()->with('success', 'Data Berhasil Diubah.');
        } else {
            return redirect()->back()->with('error', 'Gagal Menyimpan Data!');
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        
        if($this->userModel->where('id',$id)->delete()) {
            return redirect()->to('admin')->with('success', 'Admin berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal Menghapus Admin!');
        }
    }
}