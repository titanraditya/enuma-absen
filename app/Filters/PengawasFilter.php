<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PengawasFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('is_logged_in')) {
            if(session('user_role') != "pengawas") {
                return redirect()->to('dashboard');
            }
        } else {
            return redirect()->to('/');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
