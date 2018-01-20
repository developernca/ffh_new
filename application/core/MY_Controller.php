<?php

/**
 * Base class for all other controllers in this
 * application. Every controller should extend this
 * controller and not directly extend CI_Controller.
 *
 * @author Nyein Chan Aung<developernca@gmail.com>
 */
class MY_Controller extends CI_Controller {

	/**
	 * Initialize parent constructor and
	 * load helpers and libraries if not null.
	 * auto load libraries : user_agent, constant
	 * auto load helpers : url
	 *
	 * @param array $helper_name_list helpers
	 * @param array $library_name_list libraries
	 * @param array $model_name_list models
	 * @param string $db database name
	 */
	public function __construct($helper_name_list = null, $library_name_list = null, $model_name_list = null, $db = null) {
		parent::__construct();

		if (!empty($helper_name_list) && !is_null($helper_name_list)) {
			$this->load->helper($helper_name_list);
		}
		if (!empty($library_name_list) && !is_null($library_name_list)) {
			$this->load->library($library_name_list);
		}
		if (!empty($model_name_list) && !is_null($model_name_list)) {
			$this->load->model($model_name_list);
		}
		if (is_null($db)) {
			// if db is null load database with default configuration
			$this->load->database();
		} else if (!is_null($db)) {
			// load database with custom config
			$this->load->database(Constant::get_db_config($db));
		}
	}

	/**
	 * authenticate every request.
	 */
	protected function authenticate() {
		$session_id = $this->session->userdata(Constant::SESSION_USSID); //get_cookie(Constant::COOKIE_USSID);
		if (!is_null($session_id)) {
			if ($this->account->is_activated($this->session->userdata(Constant::SESSION_USSID), $this->session->userdata(Constant::SESSION_EMAIL))) {
				return Constant::AUTH_ALREADY_LOGIN;
			} else if (!$this->account->is_activated($this->session->userdata(Constant::SESSION_USSID), $this->session->userdata(Constant::SESSION_EMAIL))) {
				return Constant::AUTH_ACTIVATION_REQUIRED;
			}
		} else {
			$this->session->unset_userdata(Constant::SESSION_USSID);
			return Constant::AUTH_SESSION_NOT_EXIST;
		}
	}

	/**
	 * load view
	 * @param string $name name of view to load
	 * @param array $view_data data to use in view and should only be non nested key-value array[optional]
	 */
	protected function load_view($name, $view_data = null) {
		$data['view'] = $name;
		$data['is_mobile'] = $this->agent->is_mobile();
		if (!is_null($view_data)) {
			foreach ($view_data as $key => $value) {
				$data[$key] = $value;
			}
		}
		$this->load->view('main_view', $data);
	}

	/**
	 * Sign out
	 */
	protected function signout() {
		$this->session->sess_destroy();
		redirect(base_url());
		exit();
	}

	protected function send_activation_mail($to, $code) {
		$message = sprintf(Constant::ACTIVATION_MAIL_BODY, $code);
		$mail_sent = mail($to, Constant::ACTIVATION_MAIL_SUBJECT, $message, Constant::MAIL_HEADER, Constant::MAIL_SENDER);
	}

	/**
	 * Check whether client request is post or not.
	 * This method use only UPPER CASE.
	 * 
	 * @return boolean return true request is POST, otherwise false
	 */
	protected function is_post() {
		$http_method = $this->input->method(TRUE);
		return $http_method === 'POST';
	}

}
