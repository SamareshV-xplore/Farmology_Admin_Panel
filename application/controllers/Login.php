<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('login_model');
        
    }
	
	public function index()
	{
		$page_data = array();

		// check login or not 
		if($this->common_model->user_login_check())
		{
			redirect(base_url('dashboard'));
		}
		else
		{
			// allow to access this page
		}

		$previous_username = $this->input->cookie('admin_username', TRUE);
		$previous_password = $this->input->cookie('admin_password', TRUE);
		$previous_remember_me = $this->input->cookie('admin_remember_me', TRUE);

		$cookie_data = array("previous_username" => $previous_username, "previous_password" => $previous_password, "previous_remember_me" => $previous_remember_me);
		$page_data['cookie_data'] = $cookie_data; 

		$this->load->view('login_view', $page_data);
	}

	public function login_submit()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$remember_me = $this->input->post('remember_me');

		if($remember_me == '1')
        {
            $name   = 'admin_remember_me';
            $value  = '1';
            $expire = time()+2592000;
            $path  = '/';
            $secure = TRUE;
            setcookie($name,$value,$expire,$path); 

            $name   = 'admin_username';
            $value  = $username;
            $expire = time()+2592000;
            $path  = '/';
            $secure = TRUE;
            setcookie($name,$value,$expire,$path); 

            $name   = 'admin_password';
            $value  = $password;
            $expire = time()+2592000;
            $path  = '/';
            $secure = TRUE;
            setcookie($name,$value,$expire,$path); 
        }
        else
        {
            delete_cookie("admin_remember_me");
            delete_cookie("admin_username");            
            delete_cookie("admin_password");

        }	

        $login_data = $this->login_model->user_login($username, $password);
        echo json_encode($login_data);
	}

}
