<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Todo extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Todo_model');
        $this->load->model('User_model'); // Tambahkan pemanggilan model baru
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
        $this->load->library('session');
    }

    // Metode utama, hanya bisa diakses jika sudah login
    public function index() {
        // Pengecekan apakah pengguna sudah login
        if (!$this->session->userdata('logged_in')) {
            // Jika belum, arahkan ke halaman login
            redirect('todo/login');
        }

        // Ambil parameter dari URL untuk navigasi antar section (home, tasks, statistics, archived)
        $data['current_section'] = $this->input->get('section', TRUE) ?? 'home';
        $data['filter'] = $this->input->get('filter', TRUE) ?? '';
        $data['priority'] = $this->input->get('priority', TRUE) ?? '';
        $data['search'] = $this->input->get('search', TRUE) ?? '';
        $data['sort'] = $this->input->get('sort', TRUE) ?? '';
        $data['current_stats_view'] = $this->input->get('stats_view', TRUE) ?? 'card';
        $data['current_daily_chart_view'] = $this->input->get('daily_chart_view', TRUE) ?? 'status';
        $data['current_task_view'] = $this->input->get('task_view', TRUE) ?? 'table';
        $data['flash_message'] = $this->session->flashdata('message') ?? '';
        $data['flash_type'] = $this->session->flashdata('type') ?? 'info';
        
        // Ambil user ID dari session
        $userId = $this->session->userdata('user_id');

        // Muat data tugas berdasarkan user ID yang login
        $data['todos'] = $this->Todo_model->get_filtered(
            $userId,
            $data['filter'],
            $data['priority'],
            $data['search'],
            FALSE, // is_archived = FALSE (hanya tugas aktif)
            null,
            $data['sort']
        );

        $data['todos_home'] = $this->Todo_model->get_filtered(
            $userId,
            null, null, null,
            FALSE,
            'upcoming_past',
            null
        );

        $data['archived_todos'] = $this->Todo_model->get_filtered(
            $userId,
            null, null, null,
            TRUE, // is_archived = TRUE (hanya tugas yang diarsipkan)
            null,
            $data['sort']
        );
        
        $data['all_active_tasks_for_js'] = $this->Todo_model->get_filtered($userId, null, null, null, FALSE, null, null);
        $data['all_archived_tasks_for_js'] = $this->Todo_model->get_filtered($userId, null, null, null, TRUE, null, null);

        $this->load->view('todo/index', $data);
    }
    
    // --- Metode Baru untuk Autentikasi Pengguna ---

    /**
     * Menampilkan halaman landing (halaman utama sebelum login).
     */
    public function landing() {
        if ($this->session->userdata('logged_in')) {
            redirect('todo');
        }
        $this->load->view('todo/landing');
    }

    /**
     * Menampilkan form login.
     */
    public function login() {
        if ($this->session->userdata('logged_in')) {
            redirect('todo');
        }
        // Load view login dengan pesan flashdata jika ada
        $data['flash_message'] = $this->session->flashdata('message');
        $data['flash_type'] = $this->session->flashdata('type');
        $this->load->view('todo/login', $data);
    }

    /**
     * Memproses data login dari form.
     */
    public function process_login() {
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);

        $user = $this->User_model->get_user_by_username($username);

        if ($user && password_verify($password, $user->password)) {
            $user_data = array(
                'user_id' => $user->id,
                'username' => $user->username,
                'logged_in' => TRUE
            );
            $this->session->set_userdata($user_data);
            $this->session->set_flashdata('message', 'Login berhasil! Selamat datang, ' . $user->username . '!');
            $this->session->set_flashdata('type', 'success');
            redirect('todo');
        } else {
            $this->session->set_flashdata('message', 'Username atau password salah.');
            $this->session->set_flashdata('type', 'danger');
            redirect('todo/login');
        }
    }

    /**
     * Menampilkan form pendaftaran.
     */
    public function register() {
        if ($this->session->userdata('logged_in')) {
            redirect('todo');
        }
        // Load view register dengan pesan flashdata jika ada
        $data['flash_message'] = $this->session->flashdata('message');
        $data['flash_type'] = $this->session->flashdata('type');
        $this->load->view('todo/register', $data);
    }

    /**
     * Memproses data pendaftaran dari form.
     */
    public function process_register() {
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|is_unique[users.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('passconf', 'Konfirmasi Password', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', validation_errors());
            $this->session->set_flashdata('type', 'danger');
            redirect('todo/register');
        } else {
            $username = $this->input->post('username', TRUE);
            $password = password_hash($this->input->post('password', TRUE), PASSWORD_DEFAULT);

            if ($this->User_model->create_user($username, $password)) {
                $this->session->set_flashdata('message', 'Pendaftaran berhasil! Silakan login.');
                $this->session->set_flashdata('type', 'success');
                redirect('todo/login');
            } else {
                $this->session->set_flashdata('message', 'Terjadi kesalahan saat pendaftaran.');
                $this->session->set_flashdata('type', 'danger');
                redirect('todo/register');
            }
        }
    }

    /**
     * Menghapus sesi pengguna dan mengarahkan ke landing page.
     */
    public function logout() {
        $this->session->unset_userdata(['user_id', 'username', 'logged_in']);
        $this->session->set_flashdata('message', 'Anda telah berhasil logout.');
        $this->session->set_flashdata('type', 'info');
        redirect('todo/landing');
    }

    // --- Perubahan pada Metode Manajemen Tugas ---

    // Tambahkan user ID pada setiap operasi CRUD
    public function add() {
        if (!$this->session->userdata('logged_in')) {
            redirect('todo/login');
        }
        $this->form_validation->set_rules('task_title', 'Judul Tugas', 'required|max_length[255]');
        $this->form_validation->set_rules('task_description', 'Deskripsi Tugas', 'max_length[1000]');
        $this->form_validation->set_rules('deadline', 'Deadline', 'required');
        $this->form_validation->set_rules('priority', 'Prioritas', 'required|in_list[rendah,sedang,tinggi]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', validation_errors());
            $this->session->set_flashdata('type', 'danger');
        } else {
            $data = array(
                'title' => $this->input->post('task_title', TRUE),
                'description' => $this->input->post('task_description', TRUE),
                'deadline' => $this->input->post('deadline', TRUE),
                'priority' => $this->input->post('priority', TRUE),
                'user_id' => $this->session->userdata('user_id') // Tambahkan user ID
            );
            $this->Todo_model->add($data);
            $this->session->set_flashdata('message', 'Tugas berhasil ditambahkan!');
            $this->session->set_flashdata('type', 'success');
        }
        
        $current_task_view = $this->input->get('task_view', TRUE) ?? 'table';
        redirect('todo/index?section=tasks&task_view=' . $current_task_view);
    }
    
    public function edit($id) {
        if (!$this->session->userdata('logged_in')) {
            redirect('todo/login');
        }
        $this->form_validation->set_rules('task_title', 'Judul Tugas', 'required|max_length[255]');
        $this->form_validation->set_rules('task_description', 'Deskripsi Tugas', 'max_length[1000]');
        $this->form_validation->set_rules('deadline', 'Deadline', 'required');
        $this->form_validation->set_rules('priority', 'Prioritas', 'required|in_list[rendah,sedang,tinggi]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', validation_errors());
            $this->session->set_flashdata('type', 'danger');
        } else {
            $data = array(
                'title' => $this->input->post('task_title', TRUE),
                'description' => $this->input->post('task_description', TRUE),
                'deadline' => $this->input->post('deadline', TRUE),
                'priority' => $this->input->post('priority', TRUE)
            );
            $this->Todo_model->update($id, $data, $this->session->userdata('user_id')); // Perbarui model agar menerima user ID
            $this->session->set_flashdata('message', 'Tugas berhasil diperbarui!');
            $this->session->set_flashdata('type', 'success');
        }
        
        $current_task_view = $this->input->get('task_view', TRUE) ?? 'table';
        redirect('todo/index?section=tasks&task_view=' . $current_task_view);
    }

    public function set_status($id, $status) {
        if (!$this->session->userdata('logged_in')) {
            redirect('todo/login');
        }
        if (in_array($status, ['belum', 'progress', 'selesai'])) {
            $this->Todo_model->set_status($id, $status, $this->session->userdata('user_id'));
            $this->session->set_flashdata('message', 'Status tugas berhasil diubah!');
            $this->session->set_flashdata('type', 'info');
        } else {
            $this->session->set_flashdata('message', 'Status tugas tidak valid.');
            $this->session->set_flashdata('type', 'danger');
        }

        $current_section = $this->input->get('section') ?? 'home';
        $current_task_view = $this->input->get('task_view') ?? 'table';
        redirect('todo/index?section=' . $current_section . '&task_view=' . $current_task_view);
    }

    public function archive($id) {
        if (!$this->session->userdata('logged_in')) {
            redirect('todo/login');
        }
        $this->Todo_model->archive_task($id, $this->session->userdata('user_id'));
        $this->session->set_flashdata('message', 'Tugas berhasil diarsipkan!');
        $this->session->set_flashdata('type', 'warning');
        
        $current_section = $this->input->get('section') ?? 'home';
        $current_task_view = $this->input->get('task_view') ?? 'table';
        redirect('todo/index?section=' . $current_section . '&task_view=' . $current_task_view);
    }

    public function unarchive($id) {
        if (!$this->session->userdata('logged_in')) {
            redirect('todo/login');
        }
        $this->Todo_model->unarchive_task($id, $this->session->userdata('user_id'));
        $this->session->set_flashdata('message', 'Tugas berhasil dikembalikan dari arsip!');
        $this->session->set_flashdata('type', 'success');
        redirect('todo/index?section=archived'); 
    }

    public function permanent_delete($id) {
        if (!$this->session->userdata('logged_in')) {
            redirect('todo/login');
        }
        $this->Todo_model->permanent_delete($id, $this->session->userdata('user_id'));
        $this->session->set_flashdata('message', 'Tugas berhasil dihapus permanen!');
        $this->session->set_flashdata('type', 'danger');
        redirect('todo/index?section=archived'); 
    }
}