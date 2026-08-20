<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Cart_model');
        $this->load->model('Product_model');
        $this->load->helper('url');
    }

    private function _require_ajax()
    {
        if (!$this->input->is_ajax_request() && ENVIRONMENT !== 'testing') {
            show_404();
            return false;
        }
        return true;
    }

    private function _json($payload, $http_code = 200)
    {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    private function _is_admin()
    {
        return $this->username === 'admin';
    }

    /**
     * Admin dashboard: shows what every logged-in user wants to buy.
     */
    public function index()
    {
        $data['username'] = $this->username;
        $this->load->view('cart_dashboard', $data);
    }

    public function dashboard_data()
    {
        if (!$this->_require_ajax()) return;

        $rows = $this->Cart_model->get_all_grouped();

        $out = array();
        foreach ($rows as $row) {
            $out[] = array(
                'id_user'    => $row['id_user'],
                'i_username' => $row['i_username'],
                'i_product'  => $row['i_product'],
                'e_product'  => $row['e_product'],
                'e_category' => $row['e_category'],
                'v_price'    => (float) $row['v_price'],
                'n_stock'    => (int) $row['n_stock'],
                'n_qty'      => (int) $row['n_qty'],
                'dt_updated' => $row['dt_updated'],
            );
        }

        $this->_json(array('status' => true, 'data' => $out));
    }

    /**
     * Current user's own saved cart (used to restore the "barang yang
     * mau dibeli konsumer" table on the product page after a refresh).
     */
    public function my_list()
    {
        if (!$this->_require_ajax()) return;

        $rows = $this->Cart_model->get_by_user($this->user_id);

        $out = array();
        foreach ($rows as $row) {
            $out[] = array(
                'id_product' => $row['id_product'],
                'i_product'  => $row['i_product'],
                'e_product'  => $row['e_product'],
                'e_category' => $row['e_category'],
                'v_price'    => (float) $row['v_price'],
                'n_stock'    => (int) $row['n_stock'],
                'qty'        => (int) $row['n_qty'],
            );
        }

        $this->_json(array('status' => true, 'data' => $out));
    }

    public function save()
    {
        if (!$this->_require_ajax()) return;

        if ($this->_is_admin()) {
            $this->_json(array('status' => false, 'message' => 'Admin tidak dapat membeli produk.'), 403);
            return;
        }

        $id_product = (int) $this->input->post('id_product');
        $qty        = (int) $this->input->post('qty');

        $product = $this->Product_model->get_by_id($id_product);
        if (!$product || (isset($product['f_active']) && $product['f_active'] !== 't')) {
            $this->_json(array('status' => false, 'message' => 'Produk tidak ditemukan.'), 404);
            return;
        }

        $stock = (int) $product['n_stock'];
        if ($qty < 1) $qty = 1;
        if ($qty > $stock) $qty = $stock;

        if ($qty < 1) {
            $this->_json(array('status' => false, 'message' => 'Stok produk habis.'), 422);
            return;
        }

        $this->Cart_model->upsert($this->user_id, $id_product, $qty);
        $this->_json(array('status' => true, 'message' => 'Disimpan.', 'qty' => $qty));
    }

    public function remove()
    {
        if (!$this->_require_ajax()) return;

        if ($this->_is_admin()) {
            $this->_json(array('status' => false, 'message' => 'Admin tidak dapat membeli produk.'), 403);
            return;
        }

        $id_product = (int) $this->input->post('id_product');
        $this->Cart_model->remove($this->user_id, $id_product);
        $this->_json(array('status' => true, 'message' => 'Dihapus.'));
    }
}
