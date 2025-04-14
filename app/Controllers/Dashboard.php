<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\UserModel;
use App\Models\AttendanceModel;
use App\Models\SubcategoryOverseerModel;

class Dashboard extends BaseController
{
    public function __construct() 
    {
        $this->categoryModel = new CategoryModel();
        $this->userModel = new UserModel();
        $this->attendanceModel = new AttendanceModel();
        $this->subcategoryOverseerModel = new SubcategoryOverseerModel();
    }

    public function index()
    {
        $data['title'] = "Dashboard";
        $data['subtitle'] = "Dashboard";
        $id = session('user_id');
        if (session('user_role') == 'admin') {
            $data['usercount'] = $this->userModel
                ->select("COUNT(CASE WHEN active = '1' THEN 1 END) AS count_active, COUNT(*) AS count_all")
                ->where('category_id !=','1')
                ->first();
    
            $data['categorycount'] = $this->categoryModel
                ->select('COUNT(id) as total')
                ->where('id !=', '1')
                ->first();
    
            return view('pages/dashboard_admin',$data);
        } else if(session('user_role') == 'pengawas') {
            $data['user'] = $this->userModel
                ->select("users.*, categories.name as category_name")
                ->join('categories', 'categories.id = users.category_id')
                ->where('users.id',$id)
                ->first();
            
            $data['categorycount'] = $this->subcategoryOverseerModel
                ->select('COUNT(id) as total')
                ->where('user_id',$id)
                ->first();

            return view('pages/dashboard_overseer',$data);
        } else {
            $data['user'] = $this->userModel
                ->select("users.*, categories.name as category_name")
                ->join('categories', 'categories.id = users.category_id')
                ->where('users.id',$id)
                ->first();

            $data['attendancecount'] = $this->attendanceModel
                ->select("user_id,
                        COUNT(CASE WHEN status = 'Hadir' THEN 1 END) AS hadir_count, 
                        COUNT(CASE WHEN status = 'Ijin' THEN 1 END) AS ijin_count, 
                        COALESCE(MAX(CASE WHEN DATE(created_at) = CURDATE() THEN status END),'Tidak Hadir') AS status_today")
                ->where('user_id', $id)
                ->groupBy('user_id')
                ->first();

            return view('pages/dashboard_user',$data);
        }

    }
}
