<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Todo extends CI_Controller {
    public function __construct() {
        parent::__construct();
        // Memuat model, helper, dan library yang dibutuhkan
        $this->load->model('Todo_model');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
        $this->load->library('session'); // BARIS INI DITAMBAHKAN UNTUK MEMPERBAIKI ERROR
    }

    // Method default yang akan dipanggil jika hanya mengakses /todo
    public function index() {
        // Ambil parameter dari URL untuk navigasi antar section (home, tasks, statistics, archived)
        $data['current_section'] = $this->input->get('section', TRUE) ?? 'home'; // TRUE untuk XSS filtering
        
        // Ambil parameter filter, priority, search, dan sort dari URL
        $data['filter'] = $this->input->get('filter', TRUE) ?? '';
        $data['priority'] = $this->input->get('priority', TRUE) ?? '';
        $data['search'] = $this->input->get('search', TRUE) ?? '';
        $data['sort'] = $this->input->get('sort', TRUE) ?? ''; // Parameter untuk pengurutan tugas
        $data['current_stats_view'] = $this->input->get('stats_view', TRUE) ?? 'card'; // Parameter untuk tampilan statistik (kartu/lingkaran)
        $data['current_daily_chart_view'] = $this->input->get('daily_chart_view', TRUE) ?? 'status'; // Default view for daily chart
        $data['current_task_view'] = $this->input->get('task_view', TRUE) ?? 'table'; // Default view for task list

        // --- Muat Data Tugas untuk Setiap Bagian Tampilan ---
        
        // Data untuk halaman "Daftar Tugas" (tasks)
        // Ini adalah semua tugas aktif (is_archived = FALSE) yang difilter, dicari, dan diurutkan.
        // Data ini juga akan digunakan oleh JS untuk statistik di halaman home jika ada.
        $data['todos'] = $this->Todo_model->get_filtered(
            $data['filter'],
            $data['priority'],
            $data['search'],
            FALSE, // is_archived = FALSE (hanya tugas aktif)
            null,
            $data['sort']
        );

        // Data untuk halaman "Home": Tugas Mendekat/Lewat Deadline.
        // Data ini sudah disaring di model agar hanya menampilkan tugas yang relevan untuk home.
        $data['todos_home'] = $this->Todo_model->get_filtered(
            null, null, null, // Tidak ada filter status/priority/search spesifik
            FALSE,             // is_archived = FALSE (hanya tugas aktif)
            'upcoming_past',   // type_filter = 'upcoming_past'
            null               // Pengurutan default model (deadline ASC, priority DESC)
        );

        // Data untuk halaman "Arsip": Tugas yang Diarsipkan.
        $data['archived_todos'] = $this->Todo_model->get_filtered(
            null, null, null, // Tidak ada filter status/priority/search spesifik
            TRUE,             // is_archived = TRUE (hanya tugas yang diarsipkan)
            null,
            $data['sort']    // Opsi pengurutan juga bisa diterapkan di arsip
        );

        // Untuk JavaScript di halaman statistik, kita butuh semua tugas aktif dan yang diarsipkan.
        // all_active_tasks_for_js dan all_archived_tasks_for_js akan diambil dari model
        // tanpa filter tambahan (kecuali is_archived) untuk tujuan penghitungan statistik di JS.
        $data['all_active_tasks_for_js'] = $this->Todo_model->get_filtered(null, null, null, FALSE, null, null);
        $data['all_archived_tasks_for_js'] = $this->Todo_model->get_filtered(null, null, null, TRUE, null, null);

        // Muat view utama (index.php) yang akan menampilkan konten berdasarkan $current_section
        $this->load->view('todo/index', $data);
    }

    /**
     * Menangani penambahan tugas baru.
     * Menerima POST request dari form "Tambah Tugas Baru".
     */
    public function add() {
        // Atur aturan validasi untuk field baru
        $this->form_validation->set_rules('task_title', 'Judul Tugas', 'required|max_length[255]');
        $this->form_validation->set_rules('task_description', 'Deskripsi Tugas', 'max_length[1000]'); // Deskripsi opsional
        $this->form_validation->set_rules('deadline', 'Deadline', 'required');
        $this->form_validation->set_rules('priority', 'Prioritas', 'required|in_list[rendah,sedang,tinggi]');

        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal, simpan pesan error ke flashdata
            $this->session->set_flashdata('error', validation_errors());
        } else {
            // Data yang akan dikirim ke model
            $data = array(
                'title' => $this->input->post('task_title', TRUE), // Mengambil dari input `task_title`
                'description' => $this->input->post('task_description', TRUE), // Mengambil dari input `task_description`
                'deadline' => $this->input->post('deadline', TRUE),
                'priority' => $this->input->post('priority', TRUE)
            );
            $this->Todo_model->add($data); // Panggil method add di model dengan array data
            $this->session->set_flashdata('success', 'Tugas berhasil ditambahkan!');
        }
        
        // Redirect kembali ke halaman Daftar Tugas setelah add
        redirect('todo/index?section=tasks');
    }

    /**
     * Menangani pengeditan tugas.
     * Menerima POST request dari form edit inline.
     * @param int $id ID tugas yang akan diedit.
     */
    public function edit($id) {
        // Atur aturan validasi untuk field baru
        $this->form_validation->set_rules('task_title', 'Judul Tugas', 'required|max_length[255]');
        $this->form_validation->set_rules('task_description', 'Deskripsi Tugas', 'max_length[1000]'); // Deskripsi opsional
        $this->form_validation->set_rules('deadline', 'Deadline', 'required');
        $this->form_validation->set_rules('priority', 'Prioritas', 'required|in_list[rendah,sedang,tinggi]');

        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal, simpan pesan error ke flashdata
            $this->session->set_flashdata('error', validation_errors());
        } else {
            // Data yang akan dikirim ke model
            $data = array(
                'title' => $this->input->post('task_title', TRUE), // Mengambil dari input `task_title`
                'description' => $this->input->post('task_description', TRUE), // Mengambil dari input `task_description`
                'deadline' => $this->input->post('deadline', TRUE),
                'priority' => $this->input->post('priority', TRUE)
            );
            $this->Todo_model->update($id, $data); // Panggil method update di model dengan ID dan array data
            $this->session->set_flashdata('success', 'Tugas berhasil diperbarui!');
        }
        // Redirect kembali ke halaman Daftar Tugas setelah edit
        redirect('todo/index?section=tasks');
    }

    /**
     * Mengatur status tugas (belum, progress, selesai).
     * Akan diakses dari tautan di tabel tugas.
     * @param int $id ID tugas.
     * @param string $status Status baru.
     */
    public function set_status($id, $status) {
        if (in_array($status, ['belum', 'progress', 'selesai'])) {
            $this->Todo_model->set_status($id, $status);
            $this->session->set_flashdata('info', 'Status tugas berhasil diubah!');
        } else {
             $this->session->set_flashdata('error', 'Status tugas tidak valid.');
        }

        // Cek dari mana request datang untuk redirect yang benar
        $referer = $this->input->server('HTTP_REFERER');
        if (strpos($referer, 'section=tasks') !== false) {
            redirect('todo/index?section=tasks'); // Kembali ke halaman Daftar Tugas
        } else if (strpos($referer, 'section=archived') !== false) {
            redirect('todo/index?section=archived'); // Kembali ke halaman Arsip
        } else {
            redirect('todo/index?section=home'); // Default ke home jika tidak jelas
        }
    }

    /**
     * Mengarsipkan tugas (menggantikan fungsi delete lama).
     * Mengubah is_archived menjadi TRUE.
     * Akan diakses dari tombol 'Arsipkan' di halaman tasks atau home.
     * @param int $id ID tugas yang akan diarsipkan.
     */
    public function archive($id) {
        $this->Todo_model->archive_task($id);
        $this->session->set_flashdata('info', 'Tugas berhasil diarsipkan!');
        
        // Cek dari mana request datang untuk redirect yang benar
        $referer = $this->input->server('HTTP_REFERER');
        if (strpos($referer, 'section=tasks') !== false) {
            redirect('todo/index?section=tasks'); // Kembali ke halaman Daftar Tugas
        } else if (strpos($referer, 'section=home') !== false) {
            redirect('todo/index?section=home'); // Kembali ke halaman Home
        } else {
            redirect('todo/index?section=tasks'); // Default ke halaman Daftar Tugas
        }
    }

    /**
     * Mengembalikan tugas dari arsip (unarchive).
     * Mengubah is_archived menjadi FALSE.
     * Akan diakses dari tombol 'Kembalikan' di halaman arsip.
     * @param int $id ID tugas yang akan dikembalikan.
     */
    public function unarchive($id) {
        $this->Todo_model->unarchive_task($id);
        $this->session->set_flashdata('success', 'Tugas berhasil dikembalikan dari arsip!');
        // Setelah di-unarchive, kembali ke halaman daftar tugas yang diarsipkan
        redirect('todo/index?section=archived'); 
    }

    /**
     * Menghapus tugas secara permanen dari database.
     * Akan diakses dari tombol 'Hapus Permanen' di halaman arsip.
     * @param int $id ID tugas yang akan dihapus permanen.
     */
    public function permanent_delete($id) {
        $this->Todo_model->permanent_delete($id);
        $this->session->set_flashdata('danger', 'Tugas berhasil dihapus permanen!');
        // Setelah dihapus permanen, kembali ke halaman daftar tugas yang diarsipkan
        redirect('todo/index?section=archived'); 
    }
}