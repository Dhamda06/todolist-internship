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
     * @param string|null $status Filter berdasarkan status ('belum', 'progress', 'selesai').
     * @param string|null $priority Filter berdasarkan prioritas ('rendah', 'sedang', 'tinggi').
     * @param string|null $search Kata kunci pencarian di kolom 'title' dan 'description'.
     * @param bool $is_archived Filter berdasarkan status arsip (TRUE/FALSE). Default FALSE (tugas aktif).
     * @param string|null $type_filter Filter khusus: 'upcoming_past' untuk tugas mendekat/lewat deadline di halaman home.
     * @param string|null $sort Opsi pengurutan (misal: 'title_asc', 'deadline_desc', 'priority_high').
     * @return array Hasil query sebagai array objek tugas.
     */
    public function get_filtered($status = null, $priority = null, $search = null, $is_archived = FALSE, $type_filter = null, $sort = null) {
        // Pilih kolom 'title' dan 'description'
        $this->db->select('id, title, description, deadline, status, priority, is_archived');
        $this->db->from($this->table); // Ganti dengan $this->table

        $this->db->where('is_archived', $is_archived); // Selalu filter berdasarkan status arsip

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
            // Menampilkan tugas yang deadline-nya sudah lewat ATAU dalam 7 hari ke depan, dan statusnya belum 'selesai'.
            // Batasi hasil untuk performa di halaman home.
            $this->db->where_in('status', ['belum', 'progress']); // Hanya tugas yang belum/progress
            $this->db->where('deadline <=', date('Y-m-d H:i:s', strtotime('+7 days', strtotime($current_time)))); // Dalam 7 hari ke depan
            $this->db->or_where('deadline <=', $current_time); // Atau sudah lewat deadline
            
            // Limit result for home section to show only a few important tasks
            $this->db->limit(6); 
        }

        // Logika Pengurutan Dinamis
        switch ($sort) {
            case 'title_asc': // Ganti 'task_asc' menjadi 'title_asc'
                $this->db->order_by('title', 'ASC');
                break;
            case 'title_desc': // Ganti 'task_desc' menjadi 'title_desc'
                $this->db->order_by('title', 'DESC');
                break;
            case 'deadline_asc':
                $this->db->order_by('deadline', 'ASC');
                break;
            case 'deadline_desc':
                $this->db->order_by('deadline', 'DESC');
                break;
            case 'priority_high':
                // Urutkan prioritas: Tinggi, Sedang, Rendah (secara menurun)
                $this->db->order_by("FIELD(priority, 'tinggi', 'sedang', 'rendah')", 'ASC', FALSE);
                break;
            case 'priority_low':
                // Urutkan prioritas: Rendah, Sedang, Tinggi (secara menaik)
                $this->db->order_by("FIELD(priority, 'rendah', 'sedang', 'tinggi')", 'ASC', FALSE);
                break;
            default:
                // Urutan default jika tidak ada opsi pengurutan yang dipilih atau tidak valid
                $this->db->order_by('deadline', 'asc'); // Urutkan default berdasarkan deadline terdekat
                $this->db->order_by("FIELD(priority, 'tinggi', 'sedang', 'rendah')", 'ASC', FALSE); // Lalu berdasarkan prioritas
                break;
        }
        
        return $this->db->get()->result(); // Eksekusi query dan kembalikan hasilnya
    }

    /**
     * Menambahkan tugas baru ke database.
     *
     * @param array $data Array asosiatif berisi 'title', 'description', 'deadline', 'priority'.
     * @return bool TRUE jika berhasil, FALSE jika gagal.
     */
    public function add($data) {
        $insert_data = [
            'title' => $data['title'],
            'description' => $data['description'],
            'deadline' => $data['deadline'],
            'priority' => $data['priority'],
            'status' => 'belum', // Status default untuk tugas baru
            'is_archived' => FALSE // Tugas baru tidak diarsipkan secara default
        ];
        return $this->db->insert($this->table, $insert_data);
    }

    /**
     * Memperbarui detail tugas yang ada.
     *
     * @param int $id ID tugas yang akan diperbarui.
     * @param array $data Array asosiatif berisi 'title', 'description', 'deadline', 'priority'.
     * @return bool TRUE jika berhasil, FALSE jika gagal.
     */
    public function update($id, $data) {
        $update_data = [
            'title' => $data['title'],
            'description' => $data['description'],
            'deadline' => $data['deadline'],
            'priority' => $data['priority']
        ];
        return $this->db->where('id', $id)->update($this->table, $update_data);
    }

    /**
     * Mengatur status tugas (belum, progress, selesai).
     *
     * @param int $id ID tugas.
     * @param string $status Status baru ('belum', 'progress', 'selesai').
     * @return bool TRUE jika berhasil, FALSE jika gagal.
     */
    public function set_status($id, $status) {
        return $this->db->where('id', $id)->update($this->table, ['status' => $status]);
    }

    /**
     * Mengarsipkan tugas (soft delete) dengan mengubah status 'is_archived' menjadi TRUE.
     *
     * @param int $id ID tugas yang akan diarsipkan.
     * @return bool TRUE jika berhasil, FALSE jika gagal.
     */
    public function archive_task($id) {
        return $this->db->where('id', $id)->update($this->table, ['is_archived' => TRUE]);
    }

    /**
     * Mengembalikan tugas dari arsip (unarchive) dengan mengubah status 'is_archived' menjadi FALSE.
     *
     * @param int $id ID tugas yang akan dikembalikan.
     * @return bool TRUE jika berhasil, FALSE jika gagal.
     */
    public function unarchive_task($id) {
        return $this->db->where('id', $id)->update($this->table, ['is_archived' => FALSE]);
    }

    /**
     * Menghapus tugas secara permanen dari database.
     *
     * @param int $id ID tugas yang akan dihapus permanen.
     * @return bool TRUE jika berhasil, FALSE jika gagal.
     */
    public function permanent_delete($id) {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * Mendapatkan semua tugas (aktif dan diarsipkan) untuk perhitungan statistik.
     *
     * @return array Hasil query sebagai array objek tugas.
     */
    public function get_all_todos_for_stats() {
        $this->db->select('id, status, priority, is_archived'); // Pastikan mengambil 'is_archived'
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->result();
    }
}