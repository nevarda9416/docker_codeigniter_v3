<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

	/**
	 * Constructor
	 * Tải các thư viện cần thiết
	 */
	public function __construct()
	{
		parent::__construct();
		// Tải thư viện session, form_validation và database
		$this->load->database(); // Tải thư viện database để tương tác với cơ sở dữ liệu
	}

	/**
	 * Tao người dùng mới
	 * Hiển thị form tạo người dùng và xử lý việc tạo người dùng mới
	 * @return void
	 */
	public function create()
	{
		// 1) GET: hiển thị form tạo người dùng
		if ($this->input->method() === 'get') {
			return $this->load->view('users/create');
		}

		// 2) POST: xử lý form tạo người dùng
		$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
		$this->form_validation->set_rules('username', 'Tên', 'required|min_length[2]|max_length[50]|callback_username_check');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('pass', 'Mật khẩu', 'required|min_length[6]');
		$this->form_validation->set_rules('confirm_password', 'Xác nhận mật khẩu', 'required|matches[pass]');

		// 3) Kiểm tra tính hợp lệ của form, nếu fail thì hiển thị lại form với thông báo lỗi
		if ($this->form_validation->run() === FALSE) {
			// Nếu form không hợp lệ, hiển thị lại form với thông báo lỗi
			return $this->load->view('users/create');
		}
		// Nếu form hợp lệ, xử lý upload avatar (nếu có)
		if (!empty($_FILES['avatar']['name'])) {
			$config = [
				'upload_path' => FCPATH . 'uploads/avatars', // FCPATH là đường dẫn đến thư mục gốc của ứng dụng
				'allowed_types' => 'jpg|jpeg|png',
				'max_size' => 2048, // 2MB
				'encrypt_name' => TRUE	
			];
			// Tải thư viện upload và cấu hình
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('avatar')) {
				// Lỗi upload ==> hiển thị lại form với thông báo lỗi
				$data['upload_error'] = $this->upload->display_errors('<div class="text-danger">', '</div>');
				return $this->load->view('users/create', $data);
			}
			$uploadData = $this->upload->data();
			// Lưu đường dẫn avatar vào mảng dữ liệu
			$data['avatar'] = $uploadData['file_name']; // Lưu tên file
		}

		// 4) Nếu pass, lưu người dùng vào cơ sở dữ liệu
		$data = [
			'username' => $this->input->post('username', TRUE), // TRUE để tự động escape dữ liệu (xss_clean)
			'email' => $this->input->post('email', TRUE),
			'password' => password_hash($this->input->post('pass'), PASSWORD_BCRYPT), // Mã hóa mật khẩu
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		];

		// 5) Thêm người dùng vào cơ sở dữ liệu
		$this->db->insert('users', $data);

		// 6) Hiển thị thông báo thành công và chuyển hướng về trang tạo người dùng
		$this->session->set_flashdata('ok', 'Tạo người dùng thành công!');
		redirect('users/create');
	}

	/**
	 * Kiểm tra tính hợp lệ của tên người dùng
	 * Kiểm tra xem tên người dùng có hợp lệ không (chỉ chứa chữ cái, số và dấu gạch dưới) và chưa tồn tại trong cơ sở dữ liệu
	 * @param string $username Tên người dùng cần kiểm tra
	 * @return bool TRUE nếu hợp lệ, FALSE nếu không hợp lệ
	 */
	public function username_check($username)
	{
		// Kiểm tra xem tên người dùng có hợp lệ không
		if (!preg_match('/^[a-z0-9_]+$/i', $username)) {
			$this->form_validation->set_message('username_check', 'Tên người dùng chỉ được chứa chữ cái, số và dấu gạch dưới.');
			return FALSE;
		}
		// Kiểm tra xem tên người dùng đã tồn tại trong cơ sở dữ liệu chưa
		$this->db->where('username', $username);
		$query = $this->db->get('users');
		if ($query->num_rows() > 0) {
			$this->form_validation->set_message('username_check', 'Tên người dùng đã tồn tại. Vui lòng chọn tên khác.');
			return FALSE;
		}
		return TRUE;
	}
}
