<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Membantu mengaktifkan fungsi base_url() yang kamu pakai di React
        $this->load->helper('url');
    }

    public function index()
    {
        // Memanggil file codingan kamu yang ada di folder views
        $this->load->view('home');
    }

    public function katalog()
    {
        // Perhatikan huruf K kapital, harus sama dengan nama file di folder views
        $this->load->view('katalog');
    }

    public function detail($id = 1)
    {
        $data['product_id'] = $id;
        $this->load->view('detail', $data);
    }

    public function cart()
    {
        $this->load->view('cart');
    }

    public function login()
    {
        $this->load->view('login');
    }

    public function checkout()
    {
        $this->load->view('checkout');
    }
}
