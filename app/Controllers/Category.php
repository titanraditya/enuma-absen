<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\UserModel;
use App\Models\ScheduleModel;
use App\Models\SubcategoryOverseerModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Category extends BaseController
{
    protected $categoryModel;

    public function __construct() 
    {
        $this->categoryModel = new CategoryModel();
        $this->userModel = new UserModel();
        $this->scheduleModel = new ScheduleModel();
        $this->schedulModel = new ScheduleModel();
        $this->subcategoryOverseerModel = new SubcategoryOverseerModel();
    }

    public function index()
    {
        $data['title'] = "Daftar Grup";
        $data['subtitle'] = "Grup";

        $id = session('user_id');

        if(session('user_role') == 'admin') {
            $data['category'] = $this->categoryModel
                ->select("categories.*, COUNT(DISTINCT u.id) as total, GROUP_CONCAT(DISTINCT p.name ORDER BY p.name SEPARATOR ', ') as pengawas_names", false)
                ->join('users u','categories.id = u.category_id','left')
                ->join('subcategory_overseer so','so.category_id = categories.id','left')
                ->join('users p','p.id = so.user_id','left')
                ->where('categories.id !=', '1')
                ->orderBy('categories.name', 'asc')
                ->groupBy('categories.id')
                ->findAll();
        } else {
            $data['category'] = $this->subcategoryOverseerModel
                ->select('categories.*,count(u.id) as total')
                ->join('categories','subcategory_overseer.category_id = categories.id')
                ->join('users u','categories.id = u.category_id','left')
                ->orderBy('categories.name', 'asc')
                ->groupBy('categories.id')
                ->where('subcategory_overseer.user_id',$id)
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

        $pengawas = $this->subcategoryOverseerModel
            ->select('subcategory_overseer.*, users.id as user_id, users.name as name')
            ->join('users','users.id = subcategory_overseer.user_id')
            ->where('subcategory_overseer.category_id',$id)
            ->orderBy('users.name', 'asc')
            ->findAll();

        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        } else {
            if($id == "1") {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

        $user = $this->userModel
            ->select("users.*, COALESCE(attendances.status, 'Tidak Hadir') as status")
            ->where('category_id',$id)
            ->join('attendances', 'users.id = attendances.user_id AND DATE(attendances.checkin) = CURDATE()', 'Left')
            ->orderBy('name','asc')
            ->findAll();

        $jadwal = $this->scheduleModel
            ->where('category_id',$id)
            ->findAll();

        $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    
        usort($jadwal, function ($a, $b) use ($dayOrder) {
            return array_search($a['day'], $dayOrder) - array_search($b['day'], $dayOrder);
        });

        $data_pengawas = $this->userModel
            ->select('users.name as name, users.id as id')
            ->join('categories', 'categories.id = users.category_id', 'left')
            ->where('categories.overseer', '1')
            ->orderBy('users.name','ASC')
            ->findAll();

        // Menghapus elemen di $array1 yang ada di $array2
        foreach ($pengawas as $item2) {
            foreach ($data_pengawas as $key => $item1) {
                if ($item1['id'] === $item2['user_id']) {
                    unset($data_pengawas[$key]); // Menghapus item yang sama
                }
            }
        }

        // Reindex array setelah elemen dihapus
        $data_pengawas = array_values($data_pengawas);

        $data['pengawas'] = $data_pengawas;
        $data['reg_pengawas'] = $pengawas;

        $data['title'] =  'Detail Grup - '.$category['name'];
        $data['subtitle'] = "Grup";
        $data['category'] = $category;
        $data['user'] = $user;
        $data['jadwal'] = $jadwal;

        return view('pages/category/show',$data);
    }

    public function logs($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category || $id == '1') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->ensureCategoryAccess($id);

        $db = \Config\Database::connect();
        $logs = $db->query("
            SELECT u.id AS member_id, u.name AS member_name,
                   COALESCE(t.status, 'Tidak Hadir') AS status_today,
                   a.*
            FROM users u
            LEFT JOIN attendances t ON t.user_id = u.id AND DATE(t.checkin) = CURDATE()
            LEFT JOIN attendances a ON a.id = (
                SELECT a2.id FROM attendances a2
                WHERE a2.user_id = u.id
                ORDER BY a2.checkin DESC, a2.id DESC
                LIMIT 1
            )
            WHERE u.category_id = ?
            ORDER BY (a.checkin IS NULL) ASC, a.checkin DESC, u.name ASC
        ", [$id])->getResultArray();

        $data['title'] = 'Log Anggota - '.$category['name'];
        $data['subtitle'] = "Grup";
        $data['category'] = $category;
        $data['logs'] = $logs;

        return view('pages/category/logs',$data);
    }

    private function ensureCategoryAccess($id)
    {
        if (session('user_role') == 'admin') {
            return;
        }

        $access = $this->subcategoryOverseerModel
            ->where('user_id', session('user_id'))
            ->where('category_id', $id)
            ->first();

        if (!$access) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
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

        $id = uniqid();

        $data = [
            'id' => $id,
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),            
        ];

        if($this->request->getPost('overseer')) {
            $data['overseer'] = '1';
        } else {
            $data['overseer'] = '0';
        }

        $data['pengawas_id'] = null;
        if($this->categoryModel->insert($data)) {
            return redirect()->to('category/detail/'.$id)->with('success', 'Grup berhasil ditambahkan.');
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

    public function addpengawas()
    {
        if (session('user_role') != 'admin') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $id = $this->request->getPost('category_id');
        $pengawas = $this->request->getPost('pengawas');

        $data = [];
        foreach ($pengawas as $pengawas) {
            array_push($data,[
                'user_id' => $pengawas,
                'category_id' => $id
            ]);
        }
        if ($this->subcategoryOverseerModel->insertBatch($data)) {
            return redirect()->back()->with('success', 'Pengawas berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('error', 'Pengawas gagal ditambahkan.');
        }
    }

    public function deletepengawas()
    {
        if (session('user_role') != 'admin') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $id = $this->request->getPost('id');

        if($this->subcategoryOverseerModel->where('id',$id)->delete()) {
            return redirect()->back()->with('success', 'Pengawas berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal Menghapus Pengawas');
        }
    }

    public function membertemplate()
    {
        if (session('user_role') != 'admin') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Anggota');
        $sheet->fromArray(['Nama', 'Username', 'Password'], null, 'A1');
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(32);
        $sheet->getColumnDimension('B')->setWidth(24);
        $sheet->getColumnDimension('C')->setWidth(24);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Template Impor Anggota.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function memberimport()
    {
        if (session('user_role') != 'admin') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $categoryId = $this->request->getPost('category_id');
        $category = $this->categoryModel->find($categoryId);
        if (!$category || $categoryId == '1') {
            return redirect()->back()->with('error', 'Grup tidak ditemukan.');
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File belum dipilih atau gagal diunggah.');
        }
        if (strtolower($file->getClientExtension()) !== 'xlsx') {
            return redirect()->back()->with('error', 'Format file harus .xlsx. Gunakan template yang disediakan.');
        }

        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $rows = $reader->load($file->getTempName())->getActiveSheet()->toArray();
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'File tidak dapat dibaca. Pastikan file .xlsx valid.');
        }

        array_shift($rows); // baris 1 = judul kolom template

        $members = [];
        $errors = [];
        $usernameLines = [];

        foreach ($rows as $i => $row) {
            $line = $i + 2;
            $name = trim((string) ($row[0] ?? ''));
            $username = trim((string) ($row[1] ?? ''));
            $password = trim((string) ($row[2] ?? ''));

            if ($name === '' && $username === '' && $password === '') {
                continue;
            }

            if ($name === '') {
                $errors[] = "Baris {$line}: Nama harus diisi.";
            } elseif (mb_strlen($name) < 3 || mb_strlen($name) > 100) {
                $errors[] = "Baris {$line}: Nama harus 3-100 karakter.";
            }

            if ($username === '' && $password !== '') {
                $errors[] = "Baris {$line}: Password diisi tetapi Username kosong.";
            }

            if ($username !== '') {
                if ($password === '') {
                    $errors[] = "Baris {$line}: Username diisi tetapi Password kosong.";
                }
                if (strlen($username) < 8 || strlen($username) > 30) {
                    $errors[] = "Baris {$line}: Username harus 8-30 karakter.";
                }
                if (strpos($username, ' ') !== false) {
                    $errors[] = "Baris {$line}: Username tidak boleh mengandung spasi.";
                }
                if ($password !== '' && (strlen($password) < 8 || strlen($password) > 30)) {
                    $errors[] = "Baris {$line}: Password harus 8-30 karakter.";
                }
                if (strpos($password, ' ') !== false) {
                    $errors[] = "Baris {$line}: Password tidak boleh mengandung spasi.";
                }
                if (isset($usernameLines[$username])) {
                    $errors[] = "Baris {$line}: Username '{$username}' duplikat dengan baris {$usernameLines[$username]}.";
                } else {
                    $usernameLines[$username] = $line;
                }
            }

            $members[] = [
                'name' => $name,
                'username' => $username,
                'password' => $password,
            ];
        }

        if (!$members) {
            return redirect()->back()->with('error', 'File tidak berisi data anggota.');
        }
        if (count($members) > 500) {
            return redirect()->back()->with('error', 'Maksimal 500 anggota per impor.');
        }

        if ($usernameLines) {
            $taken = $this->userModel->whereIn('username', array_keys($usernameLines))->findColumn('username') ?? [];
            foreach ($taken as $username) {
                $errors[] = "Baris {$usernameLines[$username]}: Username '{$username}' sudah digunakan.";
            }
        }

        if ($errors) {
            return redirect()->back()
                ->with('error', 'Impor dibatalkan, tidak ada data yang disimpan.')
                ->with('import_errors', $errors);
        }

        helper('uuidv4');
        $records = [];
        foreach ($members as $member) {
            $canLogin = $member['username'] !== '';
            $records[] = [
                'id' => uuidv4(),
                'name' => $member['name'],
                'category_id' => $categoryId,
                'active' => '1',
                'can_login' => $canLogin ? '1' : '0',
                'username' => $canLogin ? $member['username'] : null,
                'password' => $canLogin ? password_hash($member['password'], PASSWORD_DEFAULT) : null,
            ];
        }

        $db = \Config\Database::connect();
        $db->transStart();
        $this->userModel->insertBatch($records);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan data. Impor dibatalkan.');
        }

        return redirect()->back()->with('success', count($records).' anggota berhasil diimpor.');
    }
}
