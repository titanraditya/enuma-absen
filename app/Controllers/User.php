<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\AttendanceModel;
use App\Models\CategoryModel;

class User extends BaseController
{
    protected $userModel;
    protected $categoryModel;
    protected $attendanceModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->attendanceModel = new AttendanceModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $data['title'] = "Daftar Anggota";
        $data['subtitle'] = "Anggota";

        $data['user'] = $this->userModel
            ->join('categories', 'categories.id = users.category_id', 'left')
            ->select('users.*, categories.name as category_name')
            ->where('categories.id !=', '1')
            ->orderBy('ISNULL(users.id_fingerprint)', 'ASC')
            ->orderBy('users.id_fingerprint','ASC')
            ->findAll();
        
        return view('pages/user/list',$data);
    }

    public function detail($id)
    {
        $user = $this->userModel
            ->select('users.*, categories.name as category_name')
            ->join('categories','categories.id = users.category_id','left')
            ->find($id);

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        } else {
            if($user['category_id'] == "1") {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

        $data['title'] = "Detail Anggota";
        $data['subtitle'] = "Grup - Detail Anggota";

        $data['user'] = $user;
        $data['attendance'] = $this->attendanceModel
            ->where('user_id', $id)
            ->findAll();

        $data['attendancecount'] = $this->attendanceModel
            ->select("user_id,
                    COUNT(CASE WHEN status = 'Hadir' THEN 1 END) AS count_hadir, 
                    COUNT(CASE WHEN status = 'Ijin' THEN 1 END) AS count_ijin, 
                    COUNT(CASE WHEN status = 'Hadir (Online)' THEN 1 END) AS count_online, 
                    COALESCE(MAX(CASE WHEN DATE(created_at) = CURDATE() THEN status END),'Tidak Hadir') AS status_today")
            ->where('user_id', $id)
            ->groupBy('user_id')
            ->orderBy('user_id')
            ->first();

        return view('pages/user/show',$data);
    }

    public function add()
    {
        $data['title'] = "Tambah Anggota";
        $data['subtitle'] = "Anggota";

        $data['category'] = $this->categoryModel
            ->where('id !=','1')
            ->orderBy('name','ASC')
            ->findAll();
        
        return view('pages/user/add',$data);
    }

    public function edit($id)
    {
        $data['title'] = "Edit Anggota";
        $data['subtitle'] = "Anggota";

        $data['category'] = $this->categoryModel
            ->where('id !=','1')
            ->orderBy('name','ASC')
            ->findAll();
        
        $user = $this->userModel
            ->find($id);
        
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        } else {
            if($user['category_id'] == "1") {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

        $data['user'] = $user;

        return view('pages/user/edit',$data);
    }

    public function addpost()
    {
        if ($this->request->getPost('can_login')) {
            $rules = $this->userModel->user_insert_valid_login();
            $can_login = '1';
        } else {
            $rules = $this->userModel->user_insert_valid();
            $can_login = '0';
        }

        if (!$this->validate($rules)) {
            session()->setFlashdata('errname',$this->validator->getError('name'));
            session()->setFlashdata('erridfinger',$this->validator->getError('id_fingerprint'));
            session()->setFlashdata('errusername',$this->validator->getError('username'));
            session()->setFlashdata('errpassword',$this->validator->getError('password'));
            session()->setFlashdata('error','Gagal Menyimpan Data!');
            return redirect()->back()->withInput();
        }

        helper('uuidv4');
        $data = [
            'id' => uuidv4(),
            'name' => $this->request->getPost('name'),
            'category_id' => $this->request->getPost('category'),
            'active' => $this->request->getPost('status'),
            'can_login' => $can_login,
        ];

        if($this->request->getPost('id_fingerprint')) {
            $data['id_fingerprint'] = $this->request->getPost('id_fingerprint');
        }

        if($this->request->getPost('can_login')) {
            $data['username'] = $this->request->getPost('username');
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        if($this->userModel->insert($data)) {
            return redirect()->to('user')->with('success', 'User berhasil ditambahkan.');
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

        $rules = $this->userModel->rulesuser_update();

        if ($user['can_login']) {
            if($this->request->getPost('can_login')) {
                if ($user['username'] == $this->request->getPost('username')) {
                    $rules['username'] = $this->userModel->rulesuser_usernamenotunique();
                } else {
                    $rules['username'] = $this->userModel->rulesuser_usernameunique();
                }
                $rules['password'] = $this->userModel->rulesuser_passpermitempty();
            }
        } else {
            if($this->request->getPost('can_login')) {
                $rules['username'] = $this->userModel->rulesuser_usernameunique();
                $rules['password'] = $this->userModel->rulesuser_passrequire();
            }
        }

        if($user['id_fingerprint'] == $this->request->getPost('id_fingerprint')) {
            $rules['id_fingerprint'] = $this->userModel->rulesuser_idfingernotunique();
        } else {
            $rules['id_fingerprint'] = $this->userModel->rulesuser_idfingerunique();
        }

        if (!$this->validate($rules)) {
            session()->setFlashdata('errname',$this->validator->getError('name'));
            session()->setFlashdata('erridfinger',$this->validator->getError('id_fingerprint'));
            session()->setFlashdata('errusername',$this->validator->getError('username'));
            session()->setFlashdata('errpassword',$this->validator->getError('password'));
            session()->setFlashdata('error','Gagal Menyimpan Data!');
            return redirect()->back()->withInput();
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'category_id' => $this->request->getPost('category'),
            'active' => $this->request->getPost('status'),
        ];
        

        if ($this->request->getPost('can_login')) {
            $data['can_login'] = '1';
            $data['username'] = $this->request->getPost('username');
            if ($this->request->getPost('password')) {
                $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            }
        } else {
            $data['can_login'] = '0';
            $data['username'] = '';
            $data['password'] = '';
        }

        if($this->request->getPost('id_fingerprint')) {
            $data['id_fingerprint'] = $this->request->getPost('id_fingerprint');
        } else {
            $data['id_fingerprint'] = null;
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
            $path = "images/data/".$id;
            $this->deleteFolder($path);
            return redirect()->to('user')->with('success', 'User berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal Menghapus User!');
        }
    }

    function deleteFolder($folderPath)
    {
        if (!is_dir($folderPath)) {
            return false;
        }

        $files = array_diff(scandir($folderPath), ['.', '..']);

        foreach ($files as $file) {
            $fullPath = $folderPath . DIRECTORY_SEPARATOR . $file;
            if (is_dir($fullPath)) {
                deleteFolder($fullPath); // recursive hapus subfolder
            } else {
                unlink($fullPath); // hapus file
            }
        }

        return rmdir($folderPath); // hapus folder setelah kosong
    }

}
