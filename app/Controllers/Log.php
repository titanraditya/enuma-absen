<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\UserModel;
use App\Models\ScheduleModel;
use CodeIgniter\HTTP\ResponseInterface;

class Log extends BaseController
{
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->attendanceModel = new AttendanceModel();
        $this->scheduleModel = new ScheduleModel();
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
                    COUNT(CASE WHEN status = 'Hadir (Online)' THEN 1 END) AS count_online, 
                    COALESCE(MAX(CASE WHEN DATE(created_at) = CURDATE() THEN status END),'Tidak Hadir') AS status_today")
            ->where('user_id', $id)
            ->groupBy('user_id')
            ->orderBy('user_id')
            ->first();

        return view('pages/log/list',$data);
    }

    public function getData()
    {
        $id = session('user_id');

        $currentDate = date('Y-m-d');
        $currentDay = date('l');
        $currentTime = strtotime(date('H:i:s'));

        $user = $this->userModel
            ->select('id, name, category_id')
            ->where('id', $id)
            ->first();

        $categoryId = $user['category_id'];

        $currentDay = date('l');
        $schedule = $this->scheduleModel
            ->where('category_id', $categoryId)
            ->where('online', '1')
            ->where('day', $currentDay)
            ->first();

        if (!$schedule) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tidak ada jadwal untuk kamu hari ini.',
            ])->setStatusCode(200);
        }

        $existingAttendance = $this->attendanceModel
            ->select('id, activity, description, status, user_id, image')
            ->where('user_id', $id)
            ->like('created_at', $currentDate, 'after')
            ->first();

        if(!$existingAttendance) {
            $schedule = $this->scheduleModel
                ->where('category_id', $user['category_id'])
                ->where('online', '1')
                ->where('day', $currentDay)
                ->first();

            if ($schedule) {
                $startTime = strtotime($schedule['start_time']);
                $endTime = strtotime($schedule['end_time']);

                if($schedule['no_limit'] != 1) {
                    if ($startTime < $currentTime AND $currentTime < $endTime) {
                        $output['status'] = 'online';
                        $output['activity'] = '';
                        $output['desctiption'] = '';
                        $output['image'] = '';
                        $output['message'] = '<span class="text-muted">Jam <b>MAKSIMAL</b> untuk absen hari ini jam <b>'.substr($schedule['end_time'], 0, -3).'</b></span>';
                    } else {
                        $output = [
                            'status' => 'error',
                            'message' => 'Anda <b>TIDAK</b> berada di waktu absen yaitu antara jam <b>'.substr($schedule['start_time'], 0, -3).' - '.substr($schedule['end_time'], 0, -3).'</b>',
                        ];
                    }
                } else {
                    $output['status'] = 'online';
                    $output['activity'] = '';
                    $output['desctiption'] = '';
                    $output['image'] = '';
                    $output['message'] = '';
                }
            } else {
                $output = [
                    'status' => 'error',
                    'message' => 'Anda belum absen atau mendapatkan ijin hari ini'
                ];
            }
            return $this->response->setJSON($output);
        } else {
            $output = $existingAttendance;
            if ($existingAttendance['status'] == "Hadir (Online)") {
                $schedule = $this->scheduleModel
                    ->where('category_id', $user['category_id'])
                    ->where('online', '1')
                    ->where('day', $currentDay)
                    ->first();

                if ($schedule['no_limit'] != 1) {
                    $startTime = strtotime($schedule['start_time']);
                    $endTime = strtotime($schedule['end_time']);
                    if ($startTime < $currentTime AND $currentTime < $endTime) {
                        $output['status'] = 'online';
                        $output['message'] = '<span class="text-muted">Jam <b>MAKSIMAL</b> untuk absen hari ini jam <b>'.substr($schedule['end_time'], 0, -3).'</b></span>';
                    } else {
                        $output = [
                            'status' => 'error',
                            'message' => 'Anda <b>TIDAK</b> berada di waktu absen yaitu antara jam <b>'.substr($schedule['start_time'], 0, -3).' - '.substr($schedule['end_time'], 0, -3).'</b>',
                        ];
                    }
                } else {
                    $output['status'] = 'online';
                }
            } else {
                $output['status'] = 'success';
                $output['message'] = '';
            }
            return $this->response->setJSON($output);
        }
    }

    public function getDataId($id)
    {
        $currentDate = date('Y-m-d');

        $existing = $this->attendanceModel
            ->select('id, activity, description, created_at, image, status, user_id')
            ->where('id', $id)
            ->first();

        $output = [
            'id' => $existing['id'],
            'user_id' => $existing['user_id'],
            'status_id' => $existing['status'],
            'activity' => $existing['activity'],
            'description' => $existing['description'],
            'image' => $existing['image'],
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
            ->select('id, activity, description, image')
            ->where('user_id', $id)
            ->like('created_at', $currentDate, 'after')
            ->first();

        $data = [
            'activity' => $this->request->getPost('activity'),
            'description' => $this->request->getPost('description'),
        ];

        if($todayAttendance) {
            if ($this->request->getFile('image') && $this->request->getFile('image')->isValid()) {
                $oldimage = $todayAttendance['image'];
                $image = $this->request->getFile('image');
                $originalName = $image->getRandomName();
                $tempPath = 'images/data/temp/' . $originalName;
                $jpgName = pathinfo($originalName, PATHINFO_FILENAME) . '.jpg';
                $jpgPath = 'images/data/'.$id.'/' . $jpgName;
                $data['image'] = $jpgName;
                $imagePath = 'images/data/temp/';
                $image->move($imagePath, $originalName);
                $this->convertAnyToJpg($tempPath, $jpgPath);
                unlink($tempPath);
                if ($this->attendanceModel->update($todayAttendance['id'], $data)) {
                    $path = "images/data/".$id.'/';
                    if (file_exists($path.$oldimage)) {
                        unlink($path.$oldimage);
                    }
                    return redirect()->to('log')->with('success', 'Log berhasil disimpan.');
                } else {
                    return redirect()->back()->with('error', 'Log gagal disimpan. db gagal');
                }
            } else {
                if ($this->attendanceModel->update($todayAttendance['id'], $data)) {
                    return redirect()->to('log')->with('success', 'Log berhasil disimpan.');
                } else {
                    return redirect()->back()->with('error', 'Log gagal disimpan. db gagal');
                }
            }
        } else {
            $currentDate = date('Y-m-d');
            $currentDay = date('l');
            $currentTime = strtotime(date('H:i:s'));

            $user = $this->userModel
                ->select('id, name, category_id')
                ->where('id', $id)
                ->first();

            $schedule = $this->scheduleModel
                ->where('category_id', $user['category_id'])
                ->where('online', '1')
                ->where('day', $currentDay)
                ->first();

            $data['user_id'] = $id;
            $data['status'] = 'Hadir (Online)';
            $data['no_limit'] = '1';
            $data['checkin'] = date('Y-m-d H:i:s');

            if ($schedule) {
                $startTime = strtotime($schedule['start_time']);
                $endTime = strtotime($schedule['end_time']);

                if($schedule['no_limit'] != 1) {
                    if ($startTime < $currentTime AND $currentTime < $endTime) {
                        if($this->request->getFile('image') && $this->request->getFile('image')->isValid()) {
                            $image = $this->request->getFile('image');
                            $originalName = $image->getRandomName();
                            $tempPath = 'images/data/temp/' . $originalName;
                            $jpgName = pathinfo($originalName, PATHINFO_FILENAME) . '.jpg';
                            $jpgPath = 'images/data/'.$id.'/' . $jpgName;
                            $data['image'] = $jpgName;
                            if ($this->attendanceModel->insert($data)) {
                                $imagePath = 'images/data/'.$id.'/';
                                if (!is_dir($imagePath)) {
                                    mkdir($imagePath, 0777, true);
                                }
                                $imagePath = 'images/data/temp/';
                                $image->move($imagePath, $originalName);
                                $this->convertAnyToJpg($tempPath, $jpgPath);
                                unlink($tempPath);
                                return redirect()->to('log')->with('success', 'Log berhasil disimpan.');
                            } else {
                                return redirect()->back()->with('error', 'Log gagal disimpan. db gagal');
                            }
                        } else {
                            return redirect()->back()->with('error', 'Log gagal disimpan.'.$file->getErrorString());
                        }
                    } else {
                        return redirect()->back()->with('error', 'Gagal menyimpan Log. Tidak sesuai waktu');
                    }
                } else {
                    if($this->request->getFile('image') && $this->request->getFile('image')->isValid()) {
                        $image = $this->request->getFile('image');
                        $originalName = $image->getRandomName();
                        $tempPath = 'images/data/temp/' . $originalName;
                        $jpgName = pathinfo($originalName, PATHINFO_FILENAME) . '.jpg';
                        $jpgPath = 'images/data/'.$id.'/' . $jpgName;
                        $data['image'] = $jpgName;
                        if ($this->attendanceModel->insert($data)) {
                            $imagePath = 'images/data/'.$id.'/';
                            if (!is_dir($imagePath)) {
                                mkdir($imagePath, 0777, true);
                            }
                            $imagePath = 'images/data/temp/';
                            $image->move($imagePath, $originalName);
                            $this->convertAnyToJpg($tempPath, $jpgPath);
                            unlink($tempPath);
                            return redirect()->to('log')->with('success', 'Log berhasil disimpan.');
                        } else {
                            return redirect()->back()->with('error', 'Log gagal disimpan. 4');
                        }
                    } else {
                        return redirect()->back()->with('error', 'Log gagal disimpan.'.$file->getErrorString());
                    }
                }
            } else {
                return redirect()->back()->with('error', 'Tidak ada jadwal untuk kamu hari ini.');
            }
        }

    }
    private function convertAnyToJpg($sourcePath, $targetPath, $quality = 50)
    {
        $info = getimagesize($sourcePath);
        if (!$info) return false;

        $mime = $info['mime'];
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false; // Format tidak didukung
        }

        // Buat canvas baru & hilangkan transparansi (isi putih)
        $width = imagesx($image);
        $height = imagesy($image);
        $bg = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($bg, 255, 255, 255);
        imagefill($bg, 0, 0, $white);
        imagecopy($bg, $image, 0, 0, 0, 0, $width, $height);

        // Simpan jadi JPG
        imagejpeg($bg, $targetPath, $quality);

        imagedestroy($image);
        imagedestroy($bg);

        return true;
    }
}
