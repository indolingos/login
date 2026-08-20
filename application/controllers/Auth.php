<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('session', 'form_validation'));
        $this->load->model('User_model');
        $this->load->helper('url');
    }

    /**
     * GET /  or  GET index.php/auth
     * Shows the login page, unless already logged in -> straight to product list.
     */
    public function index()
    {
        if ($this->session->userdata('logged_in') === TRUE) {
            redirect(site_url('product'));
            return;
        }

        $this->load->view('login');
    }

    /**
     * POST index.php/auth/login (AJAX)
     */
    public function login()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        // Already logged in, nothing to do.
        if ($this->session->userdata('logged_in') === TRUE) {
            $this->_json(array('status' => true, 'redirect' => site_url('product')));
            return;
        }

        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if (!$this->form_validation->run()) {
            $this->_json(array(
                'status'  => false,
                'message' => strip_tags(validation_errors()),
            ), 422);
            return;
        }

        $username = trim($this->input->post('username'));
        $password = (string) $this->input->post('password');

        $user = $this->User_model->get_by_username($username);

        if (!$user
            || (isset($user['f_active']) && $user['f_active'] !== 't')
            || !password_verify($password, $user['c_password'])
        ) {
            $this->_json(array(
                'status'  => false,
                'message' => 'Username atau password salah.',
            ), 401);
            return;
        }

        // Prevent session fixation: swap the session id, keep the data.
        $this->session->sess_regenerate(TRUE);

        $this->session->set_userdata(array(
            'logged_in'  => TRUE,
            'user_id'    => $user['id_user'],
            'username'   => $user['i_username'],
            'login_time' => time(),
        ));

        $this->_json(array(
            'status'   => true,
            'message'  => 'Login berhasil.',
            'redirect' => site_url('product'),
        ));
    }

    /**
     * GET/POST index.php/auth/logout
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect(site_url());
    }

    private function _json($payload, $http_code = 200)
    {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}
