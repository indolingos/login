<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

    private function _is_admin()
    {
        return $this->username === 'admin';
    }

    public function index()
    {
        if (!$this->_is_admin()) {
            redirect(site_url('product'));
            return;
        }

        $data['username'] = $this->username;
        $this->load->view('home', $data);
    }
}
