<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ScheduleModel;
use App\Models\AttendanceModel;
use CodeIgniter\HTTP\ResponseInterface;

class Api extends BaseController
{
    protected $userModel;
    protected $scheduleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->scheduleModel = new ScheduleModel();
        $this->attendanceModel = new AttendanceModel();
    }

    public function user($id): ResponseInterface
    {
        $user = $this->userModel
            ->select('users.id as id, users.id_fingerprint as id_fingerprint, users.name as name, categories.name as category_name')
            ->join('categories', 'categories.id = users.category_id')
            ->where('users.id_fingerprint', $id)
            ->first();
        $data = [
            'users' => $user
        ];
        return response()->setJSON($data);
    }

    public function storeAttendance()
    {
        try {
            $jsonData = $this->request->getJSON();
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid JSON data',
            ])->setStatusCode(400);
        }
        

        if (!$jsonData || !isset($jsonData->user_id)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid JSON data',
            ])->setStatusCode(400);
        }

        $fingerprintId = $jsonData->user_id;
        $user = $this->userModel
            ->select('id, name, category_id')
            ->where('id_fingerprint', $fingerprintId)
            ->first();

        if (!$user) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'User tidak ditemukan!',
            ])->setStatusCode(404);
        }

        $userId = $user['id'];
        $categoryId = $user['category_id'];

        $currentDay = date('l');
        $schedule = $this->scheduleModel
            ->where('category_id', $categoryId)
            ->where('day', $currentDay)
            ->first();

        // check schedule exist
        if (!$schedule) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tidak ada jadwal untuk kamu hari ini.',
            ])->setStatusCode(403);
        }

        $currentDate = date('Y-m-d');
        $tomorrowDate = date('Y-m-d',strtotime($currentDate . "+1 days"));

        //if schedule no limit
        if ($schedule['no_limit'] == '1') {
            $existingAttendance = $this->attendanceModel
                ->where('user_id', $userId)
                ->like('checkin', $currentDate, 'after')
                ->first();
        
            if ($existingAttendance) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Kamu sudah absen hari ini.',
                ])->setStatusCode(403);
            }

            $data = [
                'user_id' => $userId,
                'status'  => 'Hadir',
                'no_limit'=> '1',
                'checkin' => date('Y-m-d H:i:s'),
                'checkout' => null,
            ];
        
            if ($this->attendanceModel->insert($data)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Attendance check-in recorded successfully (restricted role)',
                    'data'    => $data,
                    'user'    => $user,
                ])->setStatusCode(201);
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Failed to record check-in for restricted role',
                ])->setStatusCode(500);
            }
        } else {
            // if no_limit false
            $currentTime = strtotime(date('H:i:s'));
            $startTime = strtotime($schedule['start_time']);
            $endTime = strtotime($schedule['end_time']);

            $todayAttendance = $this->attendanceModel
                ->where('user_id', $userId)
                ->where('created_at >=', $currentDate)
                ->where('created_at <=', $tomorrowDate)
                ->first();

            //jika sudah absen
            if($todayAttendance) {
                if($todayAttendance['status'] == "online") {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Kamu sudah absen hari ini!',
                    ])->setStatusCode(403);
                }
                //check belum 15menit
                $checkin = strtotime($todayAttendance['checkin']);
                $selisihlebih = $currentTime - $checkin;
                if ($selisihlebih <= 15 * 60) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Tunggu 15 menit untuk absen pulang',
                    ])->setStatusCode(403);
                }
                //check belum checkout
                if($todayAttendance['checkout'] == null && $todayAttendance['no_limit'] == '0') {
                    //check waktu pulang
                    if ($currentTime < $endTime) {
                        $data = [
                            'checkout' => date('Y-m-d H:i:s'),
                            'pulcep' => '1',
                        ];
                        if ($this->attendanceModel->update($todayAttendance['id'], $data)) {
                            $data['status'] = "departed";
                            return $this->response->setJSON([
                                'status'  => 'success',
                                'message' => 'Attendance checkout recorded successfully',
                                'data'    => $data,
                                'user'    => $user,
                            ])->setStatusCode(200);
                        } else {
                            return $this->response->setJSON([
                                'status'  => 'error',
                                'message' => 'Failed to record checkout',
                            ])->setStatusCode(500);
                        }
                        // return $this->response->setJSON([
                        //     'status'  => 'error',
                        //     'message' => 'Belum waktunya kamu pulang!',
                        // ])->setStatusCode(403);
                    }

                    //lembur
                    $selisihlembur = $currentTime - $endTime;
                    if ($selisihlembur >= 60 * 60) {
                        $data = [
                            'checkout' => date('Y-m-d H:i:s'),
                            'lembur' => '1',
                        ];
                        if ($this->attendanceModel->update($todayAttendance['id'], $data)) {
                            $data['status'] = "departed";
                            return $this->response->setJSON([
                                'status'  => 'success',
                                'message' => 'Attendance checkout recorded successfully',
                                'data'    => $data,
                                'user'    => $user,
                            ])->setStatusCode(200);
                        } else {
                            return $this->response->setJSON([
                                'status'  => 'error',
                                'message' => 'Failed to record checkout',
                            ])->setStatusCode(500);
                        }
                    }

                    $data = [
                        'checkout' => date('Y-m-d H:i:s'),
                    ];

                    if ($this->attendanceModel->update($todayAttendance['id'], $data)) {
                        $data['status'] = "departed";
                        return $this->response->setJSON([
                            'status'  => 'success',
                            'message' => 'Attendance checkout recorded successfully',
                            'data'    => $data,
                            'user'    => $user,
                        ])->setStatusCode(200);
                    } else {
                        return $this->response->setJSON([
                            'status'  => 'error',
                            'message' => 'Failed to record checkout',
                        ])->setStatusCode(500);
                    }
                } else {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Kamu sudah absen hari ini.',
                    ])->setStatusCode(500);
                }
            //jika belum
            } else {
                //check waktu masuk
                if ($currentTime <= $startTime) {
                    $data = [
                        'user_id' => $userId,
                        'status'  => 'Hadir',
                        'no_limit'=> '0',
                        'checkin' => date('Y-m-d H:i:s'),
                    ];

                    if ($this->attendanceModel->insert($data)) {
                        return $this->response->setJSON([
                            'status'  => 'success',
                            'message' => 'Attendance check-in recorded successfully',
                            'data'    => $data,
                            'user'    => $user,
                        ])->setStatusCode(201);
                    } else {
                        return $this->response->setJSON([
                            'status'  => 'error',
                            'message' => 'Failed to record check-in',
                        ])->setStatusCode(500);
                    }
                } else {
                    //check waktu pulang
                    if ($currentTime >= $endTime) {
                        return $this->response->setJSON([
                            'status'  => 'error',
                            'message' => 'Sudah waktunya pulang!',
                        ])->setStatusCode(403);
                    }
                    $data = [
                        'user_id' => $userId,
                        'status'  => 'Hadir',
                        'no_limit'=> '0',
                        'terlambat' => '1',
                        'checkin' => date('Y-m-d H:i:s'),
                    ];

                    if ($this->attendanceModel->insert($data)) {
                        return $this->response->setJSON([
                            'status'  => 'success',
                            'message' => 'Attendance check-in recorded successfully',
                            'data'    => $data,
                            'user'    => $user,
                        ])->setStatusCode(201);
                    } else {
                        return $this->response->setJSON([
                            'status'  => 'error',
                            'message' => 'Failed to record check-in',
                        ])->setStatusCode(500);
                    }
                    // return $this->response->setJSON([
                    //     'status'  => 'error',
                    //     'message' => 'Kamu Terlambat! Hubungi pengawas kamu untuk melakukan absen',
                    // ])->setStatusCode(403);
                }
            }

        }


    }

}
