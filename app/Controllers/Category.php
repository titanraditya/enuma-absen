<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\UserModel;
use App\Models\ScheduleModel;

class Category extends BaseController
{
    protected $categoryModel;

    public function __construct() 
    {
        $this->categoryModel = new CategoryModel();
        $this->userModel = new UserModel();
        $this->scheduleModel = new ScheduleModel();
    }

    public function index()
    {
        $data['title'] = "Daftar Grup";
        $data['subtitle'] = "Grup";

        $id = session('user_id');

        if(session('user_role') == 'admin') {
            $data['category'] = $this->categoryModel
                ->select('categories.*, count(u.id) as total, a.name as pengawas_name')
                ->join('users u','categories.id = u.category_id','left')
                ->join('users a','categories.pengawas_id = a.id','left')
                ->where('categories.id !=', '1')
                ->orderBy('categories.name', 'asc')
                ->groupBy('categories.id')
                ->findAll();
        } else {
            $data['category'] = $this->categoryModel
                ->select('categories.*, count(u.id) as total, a.name as pengawas_name')
                ->join('users u','categories.id = u.category_id','left')
                ->join('users a','categories.pengawas_id = a.id','left')
                ->where('categories.pengawas_id', $id)
                ->orderBy('categories.name', 'asc')
                ->groupBy('categories.id')
                ->findAll();
        }

        return view('pages/category/list',$data);
    }

    public function detail($id)
    {
        $category = $this->categoryModel
            ->select('categories.* ,users.name as pengawas_name')
            ->join('users','users.id = categories.pengawas_id','left')
            ->find($id);

        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        } else {
            if($id == "1") {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

        $user = $this->userModel
            ->where('category_id',$id)
            // ->orderBy('name','asc')
            ->findAll();

        $jadwal = $this->scheduleModel
            ->where('category_id',$id)
            ->findAll();

        $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    
        usort($jadwal, function ($a, $b) use ($dayOrder) {
            return array_search($a['day'], $dayOrder) - array_search($b['day'], $dayOrder);
        });

        $data['title'] =  'Detail Grup - '.$category['name'];
        $data['subtitle'] = "Grup";
        $data['category'] = $category;
        $data['user'] = $user;
        $data['jadwal'] = $jadwal;
        $data['jadwal1'] = $jadwal;

        return view('pages/category/show',$data);
    }

    public function add()
    {
        $data['title'] = "Tambah Grup";
        $data['subtitle'] = "Grup";

        $data['pengawas'] = $this->userModel
            ->select('users.name as name, users.id as id')
            ->join('categories', 'categories.id = users.category_id', 'left')
            ->where('categories.overseer', '1')
            ->orderBy('users.name','ASC')
            ->findAll();

        return view('pages/category/add',$data);
    }

    public function edit($id)
    {
        $data['category'] = $this->categoryModel
            ->find($id);
        
        if (!$data['category']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['title'] = 'Ubah Grup';
        $data['subtitle'] = 'Grup';

        $data['pengawas'] = $this->userModel
            ->select('users.name as name, users.id as id')
            ->join('categories', 'categories.id = users.category_id', 'left')
            ->where('categories.overseer', '1')
            ->orderBy('users.name','ASC')
            ->findAll();

        return view('pages/category/edit',$data);
    }

    public function addpost()
    {
        $validation_rules = [
            'name' => [
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'Nama harus diisi.',
                    'min_length' => 'Nama harus memiliki minimal 3 karakter.',
                    'max_length' => 'Nama tidak boleh lebih dari 50 karakter.',
                ]
            ],
            'description' => [
                'rules' => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required' => 'Deskripsi harus diisi.',
                    'min_length' => 'Deskripsi harus memiliki minimal 3 karakter.',
                    'max_length' => 'Deskripsi tidak boleh lebih dari 255 karakter.',
                ]
            ]    
        ];

        if (!$this->validate($validation_rules)) {
            session()->setFlashdata('errname',$this->validator->getError('name'));
            session()->setFlashdata('errdesc',$this->validator->getError('description'));
            if ($this->validator->listErrors()) {
                session()->setFlashdata('error','Gagal Menyimpan Data!');
            }
            return redirect()->back()->withInput();
        }

        $data = [
            'id' => uniqid(),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),            
        ];

        if($this->request->getPost('overseer')) {
            $data['overseer'] = '1';
            $data['pengawas_id'] = null;
        } else {
            $data['overseer'] = '0';
            $data['pengawas_id'] = $this->request->getPost('pengawas');
        }

        if($this->categoryModel->insert($data)) {
            return redirect()->to('category')->with('success', 'Grup berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('error', 'Gagal Menyimpan Data!');
        }
        
    }

    public function editpost($id)
    {
        $category = $this->categoryModel
            ->find($id);

         if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $validation_rules = [
            'name' => [
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'Nama harus diisi.',
                    'min_length' => 'Nama harus memiliki minimal 3 karakter.',
                    'max_length' => 'Nama tidak boleh lebih dari 50 karakter.',
                ]
            ],
            'description' => [
                'rules' => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required' => 'Deskripsi harus diisi.',
                    'min_length' => 'Deskripsi harus memiliki minimal 3 karakter.',
                    'max_length' => 'Deskripsi tidak boleh lebih dari 255 karakter.',
                ]
            ]    
        ];

        if (!$this->validate($validation_rules)) {
            session()->setFlashdata('errname',$this->validator->getError('name'));
            session()->setFlashdata('errdesc',$this->validator->getError('description'));
            if ($this->validator->listErrors()) {
                session()->setFlashdata('error','Gagal Menyimpan Data!');
            }
            return redirect()->back()->withInput();
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),            
        ];

        if($this->request->getPost('overseer')) {
            $data['overseer'] = '1';
            $data['pengawas_id'] = null;
        } else {
            $data['overseer'] = '0';
            $data['pengawas_id'] = $this->request->getPost('pengawas');
        }

        if($this->categoryModel->update($id, $data)) {
            return redirect()->to('category/detail/'.$id)->with('success', 'Grup berhasil diubah.');
        } else {
            return redirect()->back()->with('error', 'Gagal Menyimpan Data!');
        }

    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        
        if($this->categoryModel->where('id',$id)->delete()) {
            return redirect()->to('category')->with('success', 'Grup berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal Menghapus Grup!');
        }
    }
}
