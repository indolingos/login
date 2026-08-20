<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cart_model
 *
 * Stores what each logged-in user wants to buy (trx_cart), so the
 * "barang yang mau dibeli konsumer" list survives a page refresh and
 * can be reviewed later from the admin dashboard.
 */
class Cart_model extends CI_Model {

    /**
     * Get the cart items belonging to a single user, joined with the
     * current product data (name, price, stock, category) so the
     * numbers shown are always accurate to Product Datas.
     */
    public function get_by_user($id_user)
    {
        $this->db->select('trx_cart.id_cart, trx_cart.id_product, trx_cart.n_qty, trx_cart.dt_updated,
                            mst_product.i_product, mst_product.e_product, mst_product.v_price, mst_product.n_stock,
                            mst_category.e_category');
        $this->db->from('trx_cart');
        $this->db->join('mst_product', 'mst_product.id_product = trx_cart.id_product');
        $this->db->join('mst_category', 'mst_category.id_category = mst_product.id_category', 'left');
        $this->db->where('trx_cart.id_user', $id_user);
        $this->db->order_by('trx_cart.id_cart', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * All users' cart items at once, for the admin dashboard.
     */
    public function get_all_grouped()
    {
        $this->db->select('trx_cart.id_cart, trx_cart.id_product, trx_cart.n_qty, trx_cart.dt_updated,
                            mst_user.id_user, mst_user.i_username,
                            mst_product.i_product, mst_product.e_product, mst_product.v_price, mst_product.n_stock,
                            mst_category.e_category');
        $this->db->from('trx_cart');
        $this->db->join('mst_user', 'mst_user.id_user = trx_cart.id_user');
        $this->db->join('mst_product', 'mst_product.id_product = trx_cart.id_product');
        $this->db->join('mst_category', 'mst_category.id_category = mst_product.id_category', 'left');
        $this->db->order_by('mst_user.i_username', 'ASC');
        $this->db->order_by('trx_cart.id_cart', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Add or update the quantity of a product in a user's cart.
     * One row per (user, product) — adding an already-present product
     * just updates n_qty instead of duplicating the row.
     */
    public function upsert($id_user, $id_product, $qty)
    {
        $this->db->where('id_user', $id_user);
        $this->db->where('id_product', $id_product);
        $existing = $this->db->get('trx_cart')->row_array();

        if ($existing) {
            $this->db->where('id_cart', $existing['id_cart']);
            return $this->db->update('trx_cart', array(
                'n_qty'      => $qty,
                'dt_updated' => date('Y-m-d H:i:s'),
            ));
        }

        return $this->db->insert('trx_cart', array(
            'id_user'    => $id_user,
            'id_product' => $id_product,
            'n_qty'      => $qty,
            'dt_created' => date('Y-m-d H:i:s'),
            'dt_updated' => date('Y-m-d H:i:s'),
        ));
    }

    public function remove($id_user, $id_product)
    {
        $this->db->where('id_user', $id_user);
        $this->db->where('id_product', $id_product);
        return $this->db->delete('trx_cart');
    }
}
