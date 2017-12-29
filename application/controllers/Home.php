<?php

class Home extends MY_Controller {

	private $current_user_base_path;

	public function __construct() {
		parent::__construct(['html', 'form'], ['constant', 'session', 'pagination', 'table'], ['account', 'post', 'discussion']);
		$this->current_user_base_path = FCPATH . DIRECTORY_SEPARATOR . 'usr' . DIRECTORY_SEPARATOR . $this->session->userdata(Constant::SESSION_USSID) . DIRECTORY_SEPARATOR; // ffh/usr/{usr_id}/
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
	 * default function
	 */
	public function index() {
		$this->authenticate();
		// get row count for pagination (count all posts)
		$row_count = $this->post->count_all_posts();
		// pagination configuration array
		$config['base_url'] = base_url() . 'index.php/Home/index/';
		$config['total_rows'] = $row_count;
		$config['per_page'] = 10;
		$config['uri_segment'] = 3;
		// initialize pagination
		$this->pagination->initialize($config);
		$start = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		// get all posts
		$all_post = $this->post->get_all_posts(10, $start);
		// get and sort post type array
		$post_type = Constant::POST_TYPE_OPTIONS_ARR;
		sort($post_type);
		$this->load_view(
				Constant::HOME_VIEW, [
			Constant::VDN_ALL_POSTS => $all_post,
			Constant::VDN_POST_TYPES_OPTIONS => $post_type,
			Constant::VDN_TOTAL_POSTS_COUNT => $row_count,
			Constant::VDN_PAGINATION_LINK => $this->pagination->create_links(),
			Constant::VDN_TITLE_KEY => Constant::HOME_TITLE,
			Constant::VDN_META_DESC_KEY => Constant::HOME_DESCRIPTION_META
		]);
	}

	/**
	 * Search post.
	 */
	public function search() {
		$this->authenticate();
		// get and sort post type array
		$post_type = Constant::POST_TYPE_OPTIONS_ARR;
		sort($post_type);
		// pagination configuration
		$config['base_url'] = base_url() . 'index.php/Home/psearch';
		$config['per_page'] = 10;
		$config['reuse_query_string'] = TRUE;
		$get_form_data = $this->input->get();
		$sliced_array_data = NULL;
		if (!$this->uri->segment(3) && !empty($get_form_data)) {// first click on search
			// get match post
			$matched_post = $this->post->search_post($get_form_data);
			if (!is_null($matched_post)) {
				// serialize data to file
				file_put_contents($this->current_user_base_path . 'post_search_temp', serialize($matched_post));
				$post_count = count($matched_post);
				$config['total_rows'] = $post_count;
				$this->pagination->initialize($config);
				$sliced_array_data = array_slice($matched_post, 0, 10);
			}
		}
		// load view
		$this->load_view(Constant::HOME_VIEW, [
			Constant::VDN_ALL_POSTS => $sliced_array_data,
			Constant::VDN_TOTAL_POSTS_COUNT => count($matched_post),
			Constant::VDN_POST_TYPES_OPTIONS => $post_type,
			Constant::VDN_IS_SEARCH => TRUE,
			Constant::VDN_SEARCHED_SELECT => $get_form_data[Constant::NAME_SELECT_POST_TYPE],
			Constant::VDN_SEARCHED_KEY => $get_form_data[Constant::NAME_TEXT_SEARCH_KEY],
			Constant::VDN_PAGINATION_LINK => $this->pagination->create_links(),
			Constant::VDN_TITLE_KEY => Constant::HOME_TITLE,
			Constant::VDN_META_DESC_KEY => Constant::HOME_DESCRIPTION_META
		]);
	}

	/**
	 *  pagination search
	 */
	public function psearch() {
		$this->authenticate();
		// get and sort post type array
		$post_type = Constant::POST_TYPE_OPTIONS_ARR;
		sort($post_type);
		// pagination configuration
		$config['base_url'] = base_url() . 'index.php/Home/psearch';
		$config['per_page'] = 10;
		$config['reuse_query_string'] = TRUE;
		$get_form_data = $this->input->get();
		// get data
		$file_contents = file_get_contents($this->current_user_base_path . 'post_search_temp');
		if ($file_contents === FALSE) { // if some failures occur in file read process, redirect to home, not show any warning or exception
			redirect(base_url() . 'index.php/Home/');
			exit();
		}
		$post_data = unserialize($file_contents);
		$config['total_rows'] = count($post_data);
		$this->pagination->initialize($config);
		$offset = $this->uri->segment(3);
		$this->load_view(Constant::HOME_VIEW, [
			Constant::VDN_ALL_POSTS => array_slice($post_data, is_null($offset) ? 0 : $offset, 10),
			Constant::VDN_TOTAL_POSTS_COUNT => count($post_data),
			Constant::VDN_POST_TYPES_OPTIONS => $post_type,
			Constant::VDN_IS_SEARCH => TRUE,
			Constant::VDN_SEARCHED_SELECT => $get_form_data[Constant::NAME_SELECT_POST_TYPE],
			Constant::VDN_SEARCHED_KEY => $get_form_data[Constant::NAME_TEXT_SEARCH_KEY],
			Constant::VDN_PAGINATION_LINK => $this->pagination->create_links(),
			Constant::VDN_TITLE_KEY => Constant::HOME_TITLE,
			Constant::VDN_META_DESC_KEY => Constant::HOME_DESCRIPTION_META
		]);
	}

	// @override
	public function signout() {
		$this->authenticate();
		parent::signout();
	}

}
