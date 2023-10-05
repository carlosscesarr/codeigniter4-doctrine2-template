<?php



use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;


class SysAdminFilter implements FilterInterface {
    public function before(RequestInterface $request, $arguments = null)
    {
        $user  = session('user');
        if($user['privileges'] != 'sysadmin') {
            return redirect('401');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // TODO: Implement after() method.
    }

}