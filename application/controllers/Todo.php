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

        // Tambahkan ini: Ambil pengaturan tampilan dari database
        $data['user_settings'] = $this->User_model->get_user_settings($userId);

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
            $this->session->set_flashdata('message', 'Login berhasil! Selamat Datang, ' . $user->username . '!');
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
            // Perbaikan ada di sini: Ganti validation_errors() dengan pesan kustom
            $this->session->set_flashdata('message', 'Terdapat kesalahan saat pengisian data. Mohon periksa kembali input Anda.');
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
        $userId = $this->session->userdata('user_id');
        $data['current_section'] = 'settings';
        $data['user_data'] = $this->User_model->get_user_by_id($userId);
        $data['page_title'] = 'Pengaturan Akun';
        
        // Ambil pengaturan tampilan dari database
        $data['user_settings'] = $this->User_model->get_user_settings($userId);
        
        $this->load->view('todo/index', $data);
    }
    
    /**
     * Endpoint API baru untuk memperbarui pengaturan tampilan pengguna.
     */
    public function update_settings() {
        if (!$this->session->userdata('logged_in')) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['success' => false, 'error' => 'Not authenticated.']));
            return;
        }

        $userId = $this->session->userdata('user_id');
        $data = [
            'theme' => $this->input->post('theme', TRUE),
            'background_type' => $this->input->post('background_type', TRUE),
            'background_value' => $this->input->post('background_value', TRUE),
        ];

        if ($this->User_model->update_user_settings($userId, $data)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['success' => true, 'message' => 'Pengaturan berhasil disimpan.']));
        } else {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['success' => false, 'error' => 'Gagal menyimpan pengaturan.']));
        }
    }

    // --- Endpoint API untuk Settings (Menggunakan JSON response) ---
    public function upload_profile_picture() {
        if (!$this->session->userdata('logged_in')) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['success' => false, 'error' => 'Anda tidak memiliki akses.']));
            return;
        }
        
        $userId = $this->session->userdata('user_id');
        $upload_path = './asset/images/profiles/';
        
        // Pastikan folder upload ada, jika tidak, buat
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0775, TRUE);
        }

        // Konfigurasi library upload CodeIgniter
        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size']      = 2048; // 2MB
        $config['file_name']     = 'profile_' . $userId . '_' . time(); // Nama file unik

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('profile_picture')) {
            // Jika upload gagal, kirim pesan error dari library upload
            $error = $this->upload->display_errors('', '');
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['success' => false, 'error' => strip_tags($error)]));
            return;
        } else {
            // Jika upload berhasil
            $upload_data = $this->upload->data();
            $file_name = $upload_data['file_name'];

            // Ambil data user saat ini untuk memeriksa foto lama
            $user = $this->User_model->get_user_by_id($userId);
            
            // Hapus foto lama jika ada dan bukan foto default
            if ($user && !empty($user->profile_picture) && $user->profile_picture !== 'default_profile.png') {
                $old_file = FCPATH . $upload_path . $user->profile_picture;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            // Perbarui nama file baru di database
            $this->User_model->update_profile_picture($userId, $file_name);
            
            // Perbarui sesi untuk tampilan instan tanpa refresh
            $this->session->set_userdata('profile_picture', $file_name);

            // Kirim respons sukses dalam format JSON
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                    'success' => true,
                    'message' => 'Foto profil berhasil diperbarui!',
                    'file_name' => $file_name
                 ]));
        }
    }

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
            $this->session->set_flashdata('message', 'Gagal menambahkan tugas. Mohon periksa kembali input Anda.');
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
            $this->session->set_flashdata('message', 'Gagal memperbarui tugas. Mohon periksa kembali input Anda.');
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