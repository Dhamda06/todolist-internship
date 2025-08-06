<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Todo_model extends CI_Model {
    private $table = 'todos'; // Nama tabel tugas Anda di database

    public function __construct() {
        parent::__construct();
        $this->load->database(); // Memuat library database CodeIgniter
    }

    /**
     * Mengambil tugas dari database dengan berbagai opsi filter, pencarian, dan pengurutan.
     *
     * @param int $user_id ID pengguna yang sedang login.
     * @param string|null $status Filter berdasarkan status ('belum', 'progress', 'selesai').
     * @param string|null $priority Filter berdasarkan prioritas ('rendah', 'sedang', 'tinggi').
     * @param string|null $search Kata kunci pencarian di kolom 'title' dan 'description'.
     * @param bool $is_archived Filter berdasarkan status arsip (TRUE/FALSE). Default FALSE (tugas aktif).
     * @param string|null $type_filter Filter khusus: 'upcoming_past' untuk tugas mendekat/lewat deadline di halaman home.
     * @param string|null $sort Opsi pengurutan (misal: 'title_asc', 'deadline_desc', 'priority_high').
     * @return array Hasil query sebagai array objek tugas.
     */
    public function get_filtered($user_id, $status = null, $priority = null, $search = null, $is_archived = FALSE, $type_filter = null, $sort = null) {
        $this->db->select('id, title, description, deadline, status, priority, is_archived, created_at, updated_at');
        $this->db->from($this->table);

        // Filter utama: HANYA TUGAS MILIK USER YANG SEDANG LOGIN
        $this->db->where('user_id', $user_id);
        
        // Filter berdasarkan status arsip
        $this->db->where('is_archived', $is_archived);

        // Terapkan filter status jika ada
        if ($status) {
            $this->db->where('status', $status);
        }
        // Terapkan filter prioritas jika ada
        if ($priority) {
            $this->db->where('priority', $priority);
        }
        // Terapkan pencarian jika ada kata kunci (cari di title DAN description)
        if ($search) {
            $this->db->group_start();
            $this->db->like('title', $search);
            $this->db->or_like('description', $search);
            $this->db->group_end();
        }

        // Filter khusus untuk halaman home (tugas mendekat/lewat deadline)
        if ($type_filter === 'upcoming_past') {
            $current_time = date('Y-m-d H:i:s');
            $this->db->where_in('status', ['belum', 'progress']);
            // Gunakan `group_start` untuk mengelompokkan kondisi OR dengan benar
            $this->db->group_start();
            $this->db->where('deadline <=', date('Y-m-d H:i:s', strtotime('+7 days', strtotime($current_time))));
            $this->db->or_where('deadline <=', $current_time);
            $this->db->group_end();
            
            // Urutkan berdasarkan deadline terdekat dan limit
            $this->db->order_by('deadline', 'asc');
            $this->db->limit(6); 
        }

        // Logika Pengurutan Dinamis
        switch ($sort) {
            case 'title_asc':
                $this->db->order_by('title', 'ASC');
                break;
            case 'title_desc':
                $this->db->order_by('title', 'DESC');
                break;
            case 'deadline_asc':
                $this->db->order_by('deadline', 'ASC');
                break;
            case 'deadline_desc':
                $this->db->order_by('deadline', 'DESC');
                break;
            case 'priority_high':
                $this->db->order_by("FIELD(priority, 'tinggi', 'sedang', 'rendah')", 'ASC', FALSE);
                break;
            case 'priority_low':
                $this->db->order_by("FIELD(priority, 'rendah', 'sedang', 'tinggi')", 'ASC', FALSE);
                break;
            default:
                $this->db->order_by('deadline', 'asc');
                $this->db->order_by("FIELD(priority, 'tinggi', 'sedang', 'rendah')", 'ASC', FALSE);
                break;
        }
        
        return $this->db->get()->result();
    }

    /**
     * Menambahkan tugas baru ke database.
     *
     * @param array $data Array asosiatif berisi 'title', 'description', 'deadline', 'priority', dan 'user_id'.
     * @return bool TRUE jika berhasil, FALSE jika gagal.
     */
    public function add($data) {
        $insert_data = [
            'title' => $data['title'],
            'description' => $data['description'],
            'deadline' => $data['deadline'],
            'priority' => $data['priority'],
            'status' => 'belum',
            'is_archived' => FALSE,
            'user_id' => $data['user_id'] // Kolom user_id diisi dari data controller
        ];
        return $this->db->insert($this->table, $insert_data);
    }

    /**
     * Memperbarui detail tugas yang ada.
     *
     * @param int $id ID tugas yang akan diperbarui.
     * @param array $data Array asosiatif berisi detail tugas yang diperbarui.
     * @param int $user_id ID pengguna untuk validasi kepemilikan tugas.
     * @return bool TRUE jika berhasil, FALSE jika gagal.
     */
    public function update($id, $data, $user_id) {
        $update_data = [
            'title' => $data['title'],
            'description' => $data['description'],
            'deadline' => $data['deadline'],
            'priority' => $data['priority']
        ];
        // Pastikan hanya tugas milik user_id yang bisa diperbarui
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        return $this->db->update($this->table, $update_data);
    }

    /**
     * Mengatur status tugas (belum, progress, selesai).
     *
     * @param int $id ID tugas.
     * @param string $status Status baru ('belum', 'progress', 'selesai').
     * @param int $user_id ID pengguna untuk validasi kepemilikan tugas.
     * @return bool TRUE jika berhasil, FALSE jika gagal.
     */
    public function set_status($id, $status, $user_id) {
        // Pastikan hanya tugas milik user_id yang bisa diperbarui
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        return $this->db->update($this->table, ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Mengarsipkan tugas (soft delete).
     *
     * @param int $id ID tugas yang akan diarsipkan.
     * @param int $user_id ID pengguna untuk validasi kepemilikan tugas.
     * @return bool TRUE jika berhasil, FALSE jika gagal.
     */
    public function archive_task($id, $user_id) {
        // Pastikan hanya tugas milik user_id yang bisa diarsipkan
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        return $this->db->update($this->table, ['is_archived' => TRUE, 'archived_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Mengembalikan tugas dari arsip.
     *
     * @param int $id ID tugas yang akan dikembalikan.
     * @param int $user_id ID pengguna untuk validasi kepemilikan tugas.
     * @return bool TRUE jika berhasil, FALSE jika gagal.
     */
    public function unarchive_task($id, $user_id) {
        // Pastikan hanya tugas milik user_id yang bisa dikembalikan
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        return $this->db->update($this->table, ['is_archived' => FALSE, 'archived_at' => NULL]);
    }

    /**
     * Menghapus tugas secara permanen dari database.
     *
     * @param int $id ID tugas yang akan dihapus permanen.
     * @param int $user_id ID pengguna untuk validasi kepemilikan tugas.
     * @return bool TRUE jika berhasil, FALSE jika gagal.
     */
    public function permanent_delete($id, $user_id) {
        // Pastikan hanya tugas milik user_id yang bisa dihapus permanen
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        return $this->db->delete($this->table);
    }
}