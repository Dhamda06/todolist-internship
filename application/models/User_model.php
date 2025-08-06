<?php
// application/models/User_model.php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Membuat user baru di database.
     * @param string $username Username pengguna.
     * @param string $password Password yang sudah di-hash.
     * @return bool Hasil dari operasi insert.
     */
    public function create_user($username, $password) {
        $data = array(
            'username' => $username,
            'password' => $password
        );
        return $this->db->insert('users', $data);
    }

    /**
     * Mengambil data user berdasarkan username.
     * @param string $username Username untuk dicari.
     * @return object|null Objek data user jika ditemukan, null jika tidak.
     */
    public function get_user_by_username($username) {
        $this->db->where('username', $username);
        $query = $this->db->get('users');
        return $query->row();
    }
}