<?php

class MyPost extends MY_Controller {

	public function __construct() {

		parent::__construct(['date', 'html', 'form', 'file'], ['form_validation', 'Constant', 'session', 'KeyGenerator', 'table', 'pagination'], ['account', 'post']);
	}

	protected function authenticate() {

		$authentication_flag = parent::authenticate();

		if ($authentication_flag === Constant::AUTH_ACTIVATION_REQUIRED) {

			($this->input->is_ajax_request()) ?
							exit(json_encode(['flg' => TRUE, 'action' => base_url() . 'index.php/confirmation'])) :
							redirect(base_url() . 'index.php/confirmation');

			exit();
		} else if ($authentication_flag === Constant::AUTH_SESSION_NOT_EXIST) {

			($this->input->is_ajax_request()) ?
							exit(json_encode(['flg' => TRUE, 'action' => base_url()])) :
							redirect(base_url());

			exit();
		} else if ($authentication_flag === Constant::AUTH_ALREADY_LOGIN) {

			return;
		}
	}

	/**

	 * Default index function for this controller

	 *

	 */
	public function index() {

		$this->authenticate();

		// get row count for pagination (count posts of current user)

		$row_count = $this->post->count_post_by_user($this->session->userdata(Constant::SESSION_USSID));

		// pagination configuration array

		$config['base_url'] = base_url() . 'index.php/mypost/index/';

		$config['total_rows'] = $row_count;

		$config['per_page'] = 10;

		$config['uri_segment'] = 3;

		// initialize pagination

		$this->pagination->initialize($config);

		$start = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

		// get all posts of current user

		$currentuser_post_list = $this->post->get_post_by_user($this->session->userdata(Constant::SESSION_USSID), 10, $start);

		// get and sort post type array

		$post_type = Constant::POST_TYPE_OPTIONS_ARR;

		sort($post_type);

		// load view

		$this->load_view(
				Constant::MY_POST_VIEW, [
			Constant::VDN_POST_TYPES_OPTIONS => $post_type,
			Constant::VDN_CURRENTUSER_POST_LISTS => $currentuser_post_list,
			Constant::VDN_TOTAL_POSTS_COUNT => count($currentuser_post_list),
			Constant::VDN_PAGINATION_LINK => $this->pagination->create_links(),
			Constant::VDN_TITLE_KEY => Constant::MYPOST_TITLE,
			Constant::VDN_META_DESC_KEY => Constant::MYPOST_DESCRIPTION_META
		]);
	}

	/**

	 * When user submit a post, validate post and if there is no input error,

	 * save to database, otherwise show error to user.

	 */
	public function submit() {

		$this->authenticate();

		if (!$this->input->is_ajax_request()) {

			redirect(base_url() + "index.php/home");
		}

		$validation_err_msg = $this->validate_post();

		if (!is_null($validation_err_msg)) {// Errors
			exit(json_encode([
				'flg' => FALSE,
				'msg' => strip_tags($validation_err_msg)
			]));
		}

		// get currently inserted post

		$inserted_post = $this->post->insert_post($this->input->post());

		if (!is_null($inserted_post)) {

			exit(json_encode([
				'flg' => TRUE,
				'msg' => $inserted_post
			]));
		} else {

			exit(json_encode([
				'flg' => FALSE,
				'msg' => 'Unexpectable error occured.'
			]));
		}
	}

	/**

	 * When user edit a post, validate post and if there is no input error,

	 * update in database, otherwise show error to user.

	 */
	public function edit() {

		$this->authenticate();

		if (!$this->input->is_ajax_request()) {

			redirect(base_url() + "index.php/home");
		}

		$validation_err_msg = $this->validate_post();

		if (!is_null($validation_err_msg)) {// Errors
			exit(json_encode([
				'flg' => FALSE,
				'msg' => strip_tags($validation_err_msg)
			]));
		}

		// get currently updated post

		$updated_post = $this->post->update_post($this->input->post());

		if (!is_null($updated_post)) {

			exit(json_encode([
				'flg' => TRUE,
				'msg' => $updated_post
			]));
		} else {

			exit(json_encode([
				'flg' => FALSE,
				'msg' => 'Unexpectable error occured.'
			]));
		}
	}

	/**

	 * Delete post request. The function call can only be

	 * ajax request. If not so, redirect to home page.

	 *

	 * @param String $post_id Post id to delete

	 */
	public function delete($post_id) {

		if (!$this->input->is_ajax_request()) {

			redirect(base_url() + "index.php/home");

			exit();
		} else {

			$this->authenticate();

			exit(json_encode([
				'flg' => $this->post->delete_post_by_id($post_id)
			]));
		}
	}

	/**

	 * validate input form on post creation and updating.

	 *

	 * @return mixed string when error occur, null on success

	 */
	private function validate_post() {

		// Title [cannot be blank]

		$this->form_validation->set_rules(Constant::NAME_TEXT_POST_TITLE, '', 'required|max_length[500]', ['required' => 'Title is required. Please enter title.', 'max_length' => 'Title cannot have more than 500 characters.']);

		if (!$this->form_validation->run()) {

			return validation_errors();
		}

		// Content [cannot be blank]

		$this->form_validation->set_rules(Constant::NAME_TEXT_POST_CONTENT, '', 'required', ['required' => 'Post content cannot be blank. Please enter content.']);

		if (!$this->form_validation->run()) {

			return validation_errors();
		}

		// Contact [email format if exist]

		$this->form_validation->set_rules(Constant::NAME_TEXT_CONTACT_EMAIL, '', 'valid_email|max_length[200]', ['valid_email' => Constant::ERR_EMAIL_FORMAT, 'max_length' => Constant::ERR_EMAIL_EXCEED_LENGTH]);

		if (!$this->form_validation->run()) {

			return validation_errors();
		}

		// Contact [phone number format if exist]

		$this->form_validation->set_rules(Constant::NAME_TEXT_CONTACT_PHONE, '', 'max_length[30]|regex_match[/^[\+]{0,1}[0-9]{5,}$/]', ['max_length' => 'Phone number cannot exceed 30 digits', 'regex_match' => 'Invalid number format']);

		if (!$this->form_validation->run()) {

			return validation_errors();
		}

		return null; // No errors, all clear.
	}

}
