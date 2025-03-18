<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    public function index()
    {
        return view('pages/auth/login');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $where = [
            'username' => $username,
            'can_login' => '1'
        ];

        $user = $this->userModel
            ->select('users.*, categories.overseer as overseer')
            ->join('categories', 'categories.id = users.category_id')
            ->where($where)
            ->first();

        

        if ($user && password_verify($password, $user['password'])) {
            if($user['active'] == '0') {
                return redirect()->back()->with('error', 'anda dinonaktifkan');
            }

            if ($user['category_id'] == '1') {
                $role = 'admin';
            } else if ($user['overseer'] == '1') {
                $role = 'pengawas';
            } else {
                $role = 'user';
            }

            session()->set([
                'user_id'  => $user['id'],
                'user_name'=> $user['name'],
                'username' => $user['username'],
                'user_role'=> $role,
                'is_logged_in' => true,
            ]);

            return redirect()->to('/dashboard');
        } else {
            return redirect()->back()->with('error', 'username atau password salah');
        }
    }

    public function profile()
    {
        $data['title'] = "Profile";
        $data['subtitle'] = "Profile";

        $id = session('user_id');
        $data['user'] = $this->userModel
            ->select('users.*, categories.name as category_name, categories.overseer as overseer')
            ->join('categories', 'categories.id = users.category_id', 'left')
            ->find($id);

        return view('pages/auth/profile',$data);
    }

    public function changepass()
    {
        $passwordlama = false;
        $id = session('user_id');
        $user = $this->userModel
            ->find($id);
        
        $old_password = $this->request->getPost('old_password');

        if(password_verify($old_password, $user['password'])) {
            $passwordlama = true;
        }

        $rules = [
            'new_password' => [
                    'rules' => 'required|min_length[8]|max_length[30]|no_space',
                    'errors'=> [
                        'required' => 'Harus diisi.',
                        'min_length' => 'Harus memiliki minimal 8 karakter.',
                        'max_length' => 'Tidak boleh lebih dari 30 karakter.',
                    ]
                ]
            ];

        if (!$this->validate($rules)) {
            if (!$passwordlama) {
                 session()->setFlashdata('erroldpassword','Password lama tidak cocok!');
            }
            session()->setFlashdata('errnewpassword',$this->validator->getError('new_password'));
            session()->setFlashdata('error','Gagal Menyimpan Data!');
            return redirect()->back()->withInput();
        }

        if (!$passwordlama) {
            session()->setFlashdata('erroldpassword','Password lama tidak cocok!');
            session()->setFlashdata('error','Gagal Menyimpan Data!');
            return redirect()->back()->withInput();
        }

        $data = [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT)
        ];

        if($this->userModel->update($id,$data)) {
            return redirect()->back()->with('success', 'Password Berhasil Diubah.');
        } else {
            return redirect()->back()->with('error', 'Gagal Menyimpan Data!');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
