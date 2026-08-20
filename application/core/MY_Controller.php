<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth_Controller
 *
 * Any controller that requires a logged-in user should extend this
 * class instead of CI_Controller. If there is no valid (non-expired)
 * login session, the request is redirected to the login page
 * (index.php) before the target controller method ever runs.
 */
class MY_Controller extends CI_Controller {

    protected $user_id;
    protected $username;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');

        if ($this->session->userdata('logged_in') !== TRUE) {
            // Not logged in (or session expired) -> send back to login page.
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array(
                        'status'   => false,
                        'message'  => 'Session berakhir, silakan login kembali.',
                        'redirect' => site_url(),
                    )));
                exit;
            }

            redirect(site_url());
        }

        $this->user_id  = $this->session->userdata('user_id');
        $this->username = $this->session->userdata('username');
    }
}
