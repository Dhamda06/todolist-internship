<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Todo extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Todo_model');
        $this->load->model('User_model');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
        $this->load->library('session');
    }

    /**
     * Metode default: Memeriksa status login dan mengarahkan ke dashboard.
     */
    public function index() {
        if (!$this->session->userdata('logged_in')) {
            $this->landing();
            return;
        }

        $this->show_dashboard();
    }
    
    /**
     * Metode untuk menampilkan halaman dashboard utama dengan data tugas.
     */
    private function show_dashboard() {
        // Ambil parameter URL, gunakan nilai default jika tidak ada
        $data['current_section'] = $this->input->get('section', TRUE) ?? 'home';
        $data['filter'] = $this->input->get('filter', TRUE) ?? '';
        $data['priority'] = $this->input->get('priority', TRUE) ?? '';
        $data['search'] = $this->input->get('search', TRUE) ?? '';
        $data['sort'] = $this->input->get('sort', TRUE) ?? '';
        $data['current_stats_view'] = $this->input->get('stats_view', TRUE) ?? 'card';
        $data['current_daily_chart_view'] = $this->input->get('daily_chart_view', TRUE) ?? 'status';
        $data['current_task_view'] = $this->input->get('task_view', TRUE) ?? 'table';
        
        $userId = $this->session->userdata('user_id');

        // Ambil data yang dibutuhkan sesuai dengan section
        $data['todos'] = $this->Todo_model->get_filtered(
            $userId,
            $data['filter'],
            $data['priority'],
            $data['search'],
            FALSE, // is_archived = false
            null,
            $data['sort']
        );

        $data['todos_home'] = $this->Todo_model->get_filtered(
            $userId,
            null, null, null,
            FALSE, // is_archived = false
            'upcoming_past', // filter untuk tugas mendekat
            null
        );

        $data['archived_todos'] = $this->Todo_model->get_filtered(
            $userId,
            null, null, null,
            TRUE, // is_archived = true
            null,
            $data['sort']
        );
        
        // Ambil semua tugas aktif dan diarsipkan untuk statistik di frontend
        $data['all_active_tasks_for_js'] = $this->Todo_model->get_filtered($userId, null, null, null, FALSE, null, null);
        $data['all_archived_tasks_for_js'] = $this->Todo_model->get_filtered($userId, null, null, null, TRUE, null, null);
        
        // Dapatkan data pengguna untuk halaman settings
        $data['user_data'] = $this->User_model->get_user_by_id($userId);

        $this->load->view('todo/index', $data);
    }
    
    // --- Metode untuk Autentikasi Pengguna ---

    public function landing() {
        if ($this->session->userdata('logged_in')) {
            redirect('todo');
        }
        $this->load->view('todo/landing');
    }

    public function login() {
        if ($this->session->userdata('logged_in')) {
            redirect('todo');
        }
        $data['flash_message'] = $this->session->flashdata('message');
        $data['flash_type'] = $this->session->flashdata('type');
        $this->load->view('todo/login', $data);
    }

    public function process_login() {
        $identifier = $this->input->post('email', TRUE);
        $password = $this->input->post('password', TRUE);

        $user = $this->User_model->get_user_by_identifier($identifier);

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
            $this->session->set_flashdata('message', 'Email atau password salah.');
            $this->session->set_flashdata('type', 'danger');
            redirect('todo/login');
        }
    }

    public function register() {
        if ($this->session->userdata('logged_in')) {
            redirect('todo');
        }
        $data['flash_message'] = $this->session->flashdata('message');
        $data['flash_type'] = $this->session->flashdata('type');
        $this->load->view('todo/register', $data);
    }

    public function process_register() {
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|is_unique[users.username]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('passconf', 'Konfirmasi Password', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', validation_errors());
            $this->session->set_flashdata('type', 'danger');
            redirect('todo/register');
        } else {
            $username = $this->input->post('username', TRUE);
            $email = $this->input->post('email', TRUE);
            $password = password_hash($this->input->post('password', TRUE), PASSWORD_DEFAULT);

            if ($this->User_model->create_user($username, $email, $password)) {
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

    public function logout() {
        $this->session->unset_userdata(['user_id', 'username', 'logged_in']);
        $this->session->set_flashdata('message', 'Anda telah berhasil logout.');
        $this->session->set_flashdata('type', 'info');
        redirect('todo/landing');
    }

    // --- Metode baru untuk halaman Settings ---
    public function settings() {
        if (!$this->session->userdata('logged_in')) {
            redirect('todo/login');
        }
        $data['current_section'] = 'settings';
        $data['user_data'] = $this->User_model->get_user_by_id($this->session->userdata('user_id'));
        $data['page_title'] = 'Pengaturan Akun';
        $this->load->view('todo/index', $data);
    }

    // --- Endpoint API untuk Settings (Menggunakan JSON response) ---
    
    /**
     * Endpoint API untuk mengunggah foto profil pengguna.
     */
    public function upload_profile_picture() {
        // Hanya izinkan akses jika sudah login
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated.']);
            return;
        }

        // Pastikan direktori `asset/images/profiles/` sudah ada dan bisa ditulis
        $upload_path = './asset/images/profiles/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size']      = 2048; // 2MB
        
        // Buat nama file unik berdasarkan user_id untuk menghindari duplikasi
        $config['file_name']     = 'profile_' . $this->session->userdata('user_id') . '_' . time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('profile_picture')) {
            // Jika gagal, kirim pesan error
            $error = array('error' => $this->upload->display_errors('', ''));
            echo json_encode(['success' => false, 'error' => strip_tags($error['error'])]);
        } else {
            $upload_data = $this->upload->data();
            $file_name = $upload_data['file_name'];
            
            // Hapus foto profil lama jika ada
            $user = $this->User_model->get_user_by_id($this->session->userdata('user_id'));
            if ($user && !empty($user->profile_picture) && $user->profile_picture !== 'default_profile.png') {
                $old_file = $upload_path . $user->profile_picture;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            // Panggil model untuk update nama file foto profil di database
            $this->User_model->update_profile_picture($this->session->userdata('user_id'), $file_name);
            
            // Perbarui data session
            $this->session->set_userdata('profile_picture', $file_name);

            echo json_encode(['success' => true, 'file_name' => $file_name]);
        }
    }

    /**
     * Endpoint API untuk memperbarui username atau email.
     */
    public function update_profile() {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated.']);
            return;
        }

        $this->form_validation->set_rules('username', 'Username', 'required|min_length[5]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        
        $userId = $this->session->userdata('user_id');
        $new_username = $this->input->post('username', TRUE);
        $new_email = $this->input->post('email', TRUE);

        // Tambahkan validasi unik untuk username dan email, kecuali jika tidak berubah
        $current_user = $this->User_model->get_user_by_id($userId);
        if ($new_username != $current_user->username) {
            $this->form_validation->set_rules('username', 'Username', 'is_unique[users.username]');
        }
        if ($new_email != $current_user->email) {
            $this->form_validation->set_rules('email', 'Email', 'is_unique[users.email]');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'error' => validation_errors()]);
        } else {
            $update_data = [
                'username' => $new_username,
                'email' => $new_email
            ];
            
            $this->User_model->update_user($userId, $update_data);
            
            // Perbarui data session
            $this->session->set_userdata('username', $new_username);
            
            echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui.']);
        }
    }

    // --- Metode Manajemen Tugas ---

    public function add() {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated.']);
            return;
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
                'user_id' => $this->session->userdata('user_id')
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
            echo json_encode(['success' => false, 'error' => 'Not authenticated.']);
            return;
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
            $this->Todo_model->update($id, $data, $this->session->userdata('user_id'));
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