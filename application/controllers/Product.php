<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Product_model');
        $this->load->helper('url');
        $this->load->library('form_validation');
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

    private function _stock_status($stock)
    {
        $stock = (int) $stock;
        if ($stock == 0)   return 'Habis';
        if ($stock <= 20)  return 'Menipis';
        if ($stock <= 100) return 'Cukup';
        return 'Banyak';
    }

    public function index()
    {
        $data['categories'] = $this->Product_model->get_categories();
        $this->load->view('product_list', $data);
    }

    public function list_data()
    {
        if (!$this->_require_ajax()) return;

        $search = trim((string) $this->input->get_post('search'));
        $rows   = $this->Product_model->get_all($search !== '' ? $search : null, true);

        $out = array();
        foreach ($rows as $row) {
            $out[] = array(
                'id_product'  => $row['id_product'],
                'i_product'   => $row['i_product'],
                'e_product'   => $row['e_product'],
                'id_category' => $row['id_category'],
                'e_category'  => $row['e_category'],
                'v_price'     => (float) $row['v_price'],
                'n_stock'     => (int) $row['n_stock'],
                'status'      => $this->_stock_status($row['n_stock']),
                'f_active'    => $row['f_active'],
            );
        }

        $this->_json(array('status' => true, 'data' => $out));
    }

    public function get($id)
    {
        if (!$this->_require_ajax()) return;

        $product = $this->Product_model->get_by_id($id);
        if (!$product) {
            $this->_json(array('status' => false, 'message' => 'Produk tidak ditemukan.'), 404);
            return;
        }

        $this->_json(array('status' => true, 'data' => $product));
    }

    public function save()
    {
        if (!$this->_require_ajax()) return;

        $id_product = trim((string) $this->input->post('id_product'));
        $is_edit    = ($id_product !== '');

        $this->form_validation->set_rules('e_product', 'Nama Produk', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('id_category', 'Kategori', 'required|integer');
        $this->form_validation->set_rules('v_price', 'Harga', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('n_stock', 'Stock', 'required|numeric|greater_than_equal_to[0]');
        if (!$is_edit) {
            $this->form_validation->set_rules('i_product', 'Kode Produk', 'required|trim|max_length[20]');
        }

        if (!$this->form_validation->run()) {
            $this->_json(array(
                'status'  => false,
                'message' => strip_tags(validation_errors()),
            ), 422);
            return;
        }

        $i_product = trim($this->input->post('i_product'));

        if (!$is_edit) {
            $existing_code = $this->Product_model->find_by_code($i_product);
            if ($existing_code) {
                $is_active = (isset($existing_code['f_active']) && $existing_code['f_active'] === 't');
                $this->_json(array(
                    'status'  => false,
                    'message' => $is_active
                        ? 'Kode product sudah digunakan'
                        : 'Kode product sudah digunakan (Deactivated)',
                ), 422);
                return;
            }
        }

        $data = array(
            'e_product'   => $this->input->post('e_product'),
            'id_category' => (int) $this->input->post('id_category'),
            'v_price'     => (float) $this->input->post('v_price'),
            'n_stock'     => (int) $this->input->post('n_stock'),
        );

        if ($is_edit) {
            $existing = $this->Product_model->get_by_id($id_product);
            if (!$existing) {
                $this->_json(array('status' => false, 'message' => 'Produk tidak ditemukan.'), 404);
                return;
            }
            if (isset($existing['f_active']) && $existing['f_active'] !== 't') {
                $this->_json(array('status' => false, 'message' => 'Produk sudah dinonaktifkan (Deactivated), tidak bisa diedit.'), 422);
                return;
            }
            $this->Product_model->update($id_product, $data);
            $this->_json(array('status' => true, 'message' => 'Produk berhasil diperbarui.'));
        } else {
            $data['i_product'] = $i_product;
            $data['f_active']  = 't';
            try {
                $this->Product_model->insert($data);
            } catch (Exception $e) {
                $existing_code = $this->Product_model->find_by_code($i_product);
                $is_active = ($existing_code && isset($existing_code['f_active']) && $existing_code['f_active'] === 't');
                $this->_json(array(
                    'status'  => false,
                    'message' => $is_active
                        ? 'Kode product sudah digunakan'
                        : 'Kode product sudah digunakan (Deactivated)',
                ), 422);
                return;
            }
            $this->_json(array('status' => true, 'message' => 'Produk berhasil ditambahkan.'));
        }
    }

    public function delete($id)
    {
        if (!$this->_require_ajax()) return;

        $existing = $this->Product_model->get_by_id($id);
        if (!$existing) {
            $this->_json(array('status' => false, 'message' => 'Produk tidak ditemukan.'), 404);
            return;
        }
        if (isset($existing['f_active']) && $existing['f_active'] !== 't') {
            $this->_json(array('status' => false, 'message' => 'Produk sudah dinonaktifkan (Deactivated).'), 422);
            return;
        }

        $this->Product_model->delete($id);
        $this->_json(array('status' => true, 'message' => 'Produk berhasil dihapus.'));
    }

    public function restore($id)
    {
        if (!$this->_require_ajax()) return;

        $existing = $this->Product_model->get_by_id($id);
        if (!$existing) {
            $this->_json(array('status' => false, 'message' => 'Produk tidak ditemukan.'), 404);
            return;
        }
        if (isset($existing['f_active']) && $existing['f_active'] === 't') {
            $this->_json(array('status' => false, 'message' => 'Produk sudah aktif.'), 422);
            return;
        }

        $this->Product_model->restore($id);
        $this->_json(array('status' => true, 'message' => 'Produk berhasil diaktifkan kembali.'));
    }

    public function check_kode()
    {
        if (!$this->_require_ajax()) return;

        $kode       = trim((string) $this->input->post('i_product'));
        $exclude_id = $this->input->post('id_product');
        $exists     = false;
        $is_active  = false;

        if ($kode !== '') {
            $existing_code = $this->Product_model->find_by_code($kode, $exclude_id ?: null);
            $exists    = (bool) $existing_code;
            $is_active = ($existing_code && isset($existing_code['f_active']) && $existing_code['f_active'] === 't');
        }

        $this->_json(array('exists' => $exists, 'is_active' => $is_active));
    }
}