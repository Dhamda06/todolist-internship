<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Membuat user baru di database.
     * @param string $username Username pengguna.
     * @param string $email Email pengguna.
     * @param string $password Password yang sudah di-hash.
     * @return bool Hasil dari operasi insert.
     */
    public function create_user($username, $email, $password) {
        $data = array(
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'profile_picture' => 'default_profile.png'
        );
        return $this->db->insert('users', $data);
    }

    /**
     * Mengambil data user berdasarkan username atau email.
     * @param string $identifier Username atau email untuk dicari.
     * @return object|null Objek data user jika ditemukan, null jika tidak.
     */
    public function get_user_by_identifier($identifier) {
        $this->db->where('username', $identifier);
        $this->db->or_where('email', $identifier);
        $query = $this->db->get('users');
        return $query->row();
    }

    /**
     * Mengambil data user berdasarkan ID.
     * @param int $id ID pengguna.
     * @return object|null Objek data user jika ditemukan, null jika tidak.
     */
    public function get_user_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('users');
        return $query->row();
    }
    
    /**
     * Memperbarui username dan/atau email pengguna.
     * @param int $id ID pengguna yang akan diperbarui.
     * @param array $data Data yang akan diperbarui.
     * @return bool Hasil dari operasi update.
     */
    public function update_user($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }
    
    /**
     * Memperbarui nama file foto profil pengguna di database.
     * @param int $id ID pengguna yang akan diperbarui.
     * @param string $file_name Nama file foto profil yang baru.
     * @return bool Hasil dari operasi update.
     */
    public function update_profile_picture($id, $file_name) {
        $this->db->where('id', $id);
        return $this->db->update('users', ['profile_picture' => $file_name]);
    }
    
    /**
     * Mengambil pengaturan tampilan dari database.
     * @param int $userId ID pengguna.
     * @return object|null Objek data pengaturan jika ditemukan, null jika tidak.
     */
    public function get_user_settings($userId) {
        $this->db->select('theme, background_type, background_value');
        $this->db->where('id', $userId);
        $query = $this->db->get('users');
        return $query->row();
    }

    /**
     * Memperbarui pengaturan tampilan pengguna di database.
     * @param int $userId ID pengguna.
     * @param array $data Data pengaturan yang akan diperbarui.
     * @return bool Hasil dari operasi update.
     */
    public function update_user_settings($userId, $data) {
        $this->db->where('id', $userId);
        return $this->db->update('users', $data);
    }
}