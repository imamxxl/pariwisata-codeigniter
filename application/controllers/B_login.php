<?php

class B_login extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->view('backend/login/index');
    }

    public function login()
    {
        $this->form_validation->set_rules('username','Username','required');
		$this->form_validation->set_rules('password','Password','required');
        
        if($this->form_validation->run() != false){
            $username = $_POST['username'];
            $password = $_POST['password'];
			$user = $this->db->query("SELECT * FROM tb_user WHERE username = '$username' AND status = 1")->row_array();
            if(!isset($user))
            {
                $this->session->set_flashdata(['pesan','Data tidak ditemukan','type' => 'error']);
			    redirect('b_login');
            }

            if($user['username'] == $username){
                if(password_verify($password,$user['password_hash'])){
                    $level = $user['level'];
                    $id_user = $user['id_user'];
                    if($level == 1)
                    {
                        $data = array(
                        'id_user' => $user['id_user'],
                        'level' => $user['level'],
                        'nama' => 'ADMINISTRATOR',
                        'foto' => 'assets/src/images/user.png'
                        );
                    }elseif($level == 2){
                        $users = $this->db->query("SELECT * FROM tb_operator WHERE id_user = '$id_user'")->row_array();
                        $data = array(
                        'id_user' => $user['id_user'],
                        'level' => $user['level'],
                        'nama' => $users['nama'],
                        'foto' => $users['foto']
                        );
                    }elseif($level == 3){
                        $users = $this->db->query("SELECT * FROM tb_pengelola WHERE id_user = '$id_user'")->row_array();
                        $data = array(
                        'id_user' => $user['id_user'],
                        'level' => $user['level'],
                        'nama' => $users['nama'],
                        'foto' => $users['foto']
                        );
                    }
                    $this->session->set_userdata($data);
                    redirect('dashboard');
                }else{
                    $this->session->set_flashdata(['pesan' =>'Password Salah.','type' => 'error']);
			        redirect('b_login');
                }
            }else{
                $this->session->set_flashdata(['pesan' => 'Username Salah.','type' => 'error']);
			    redirect('b_login');
            }
		}else{
            $this->session->set_flashdata(['pesan' => 'Silahkan Periksa Kembali','type' => 'error']);
			redirect('b_login');
		}
    }

    public function logout()
    {
        $this->session->unset_userdata('id_user');
        $this->session->set_flashdata(['pesan' => 'Anda Telah Keluar.','type' => 'success']);
        redirect('b_login');
    }
}