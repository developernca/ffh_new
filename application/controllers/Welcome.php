<?php

class Welcome extends MY_Controller {

	/**
	 * constructor, call parent constructor
	 */
	public function __construct() {
		parent::__construct(['html', 'form'], ['form_validation', 'KeyGenerator', 'session'], ['account']);
	}

	protected function authenticate() {
		$authentication_flag = parent::authenticate();
		if ($authentication_flag == Constant::AUTH_SESSION_NOT_EXIST) {
			return;
		} else if ($authentication_flag == Constant::AUTH_ACTIVATION_REQUIRED) {
			($this->input->is_ajax_request()) ?
							exit(json_encode(['flg' => 0, 'action' => base_url() . '/index.php/confirmation'])) :
							redirect(base_url() . 'index.php/confirmation/');
		} else if ($authentication_flag == Constant::AUTH_ALREADY_LOGIN) {
			($this->input->is_ajax_request()) ?
							exit(json_encode(['flg' => 0, 'action' => base_url() . '/index.php/home'])) :
							redirect(base_url() . 'index.php/home/');
		}
	}

	/**
	 * default function.
	 */
	public function index() {
		$this->authenticate();
		$view_data = [
			Constant::VDN_TITLE_KEY => Constant::WELCOME_TITLE,
			Constant::VDN_META_DESC_KEY => Constant::WELCOME_DESCRIPTION_META];
		$this->load_view(Constant::WELCOME_VIEW, $view_data);
	}

	/**
	 * Function for sign up request.
	 */
	public function signup() {
		$this->authenticate();
		$validation_err_msg = $this->signup_validation();
		if (is_null($validation_err_msg) && !is_null($result = $this->account->register($this->input->post()))) {
			// set session data
			$email = $this->input->post(Constant::NAME_TEXT_SIGNUP_FORM_EMAIL);
			$this->session->set_userdata([
				Constant::SESSION_USSID => $result['id']
				, Constant::SESSION_EMAIL => $email
				, Constant::SESSION_CURRENTUSER_NAME => $result['name']
			]);
			$this->send_activation_mail($email, $result['activation_code']);
			exit(json_encode([
				'flg' => 0
			]));
		} else if (!is_null($validation_err_msg)) {
			exit(json_encode([
				'flg' => -1,
				'msg' => strip_tags($validation_err_msg)
			]));
		}
	}

	/**
	 * Function for sign in request
	 */
	public function signin() {
		$this->authenticate();
		$validation_err_msg = $this->signin_validation(); // validate sign in form
		header('Access-Control-Allow-Origin: *');
		if (is_null($validation_err_msg)) { // validation success
			// Check email and password in database
			$signin_success = $this->account->is_signin_correct($this->input->post(Constant::NAME_TEXT_SIGNIN_FORM_EMAIL), $this->input->post(Constant::NAME_PASS_SIGNIN_FORM_PASSWORD));
			if (!is_null($signin_success)) {
				// set session data
				$this->session->set_userdata([
					Constant::SESSION_USSID => $signin_success[Constant::TABLE_ACCOUNTS_COLUMN_ID]
					, Constant::SESSION_CURRENTUSER_NAME => $signin_success[Constant::TABLE_ACCOUNTS_COLUMN_NAME]
					, Constant::SESSION_EMAIL => $this->input->post(Constant::NAME_TEXT_SIGNIN_FORM_EMAIL)
				]);
				exit(json_encode(['flg' => 0, 'action' => base_url() . 'index.php/home']));
			} else {
				exit(json_encode(['flg' => -1, 'msg' => 'Incorrect email or password.']));
			}
		} else if (!is_null($validation_err_msg)) { // validation fail
			exit(json_encode([
				'flg' => -1
				, 'msg' => strip_tags($validation_err_msg)
			]));
		}
	}

	/**
	 * Function for password forget request.
	 */
	public function forget() {
		// User can only reset password when the request is ajax and user is not already login yet
		if ($this->input->is_ajax_request() && is_null($this->session->userdata(Constant::SESSION_USSID))) {
			$validation_err_msg = $this->forget_validation();
			if (!is_null($validation_err_msg)) {// validation error occured
				exit(json_encode([
					'flg' => FALSE,
					'msg' => strip_tags($validation_err_msg)
				]));
			}
			// no errors
			$email = $this->input->post('email');
			$updated_password = $this->account->update_password_by_email($email);
			if ($updated_password) {
				// send mail with generated password to user
				$message = sprintf(Constant::PASSRESET_MAIL_BODY, $updated_password);
				mail($email, "New password", $message, Constant::MAIL_HEADER, Constant::MAIL_SENDER);
				exit(json_encode([
					'flg' => TRUE,
					'msg' => 'A new password was sent to your email',
					'pass' => $updated_password
				]));
			} else {
				exit(json_encode([
					'flg' => FALSE,
					'msg' => 'Unexpected error occured. Try again!'
				]));
			}
		}
	}

	/**
	 * Check for sign up form data.
	 * @return mixed If validation failed, return error message, null on success
	 */
	private function signup_validation() {

		// validate email
		$this->form_validation->set_rules(Constant::NAME_TEXT_SIGNUP_FORM_EMAIL, '', 'required|valid_email', ['required' => Constant::ERR_EMAIL_BLANK, 'valid_email' => Constant::ERR_EMAIL_FORMAT]);
		if (!$this->form_validation->run()) {
			return validation_errors();
		}

		// validate duplicate email
		if ($this->account->is_email_exist($this->input->post(Constant::NAME_TEXT_SIGNUP_FORM_EMAIL))) {
			return Constant::ERR_SIGNUP_EMAIL_EXIST;
		}

		// validate password match
		$first_password = $this->input->post(Constant::NAME_PASS_SIGNUP_FORM_PASSWORD);
		$confirm_password = $this->input->post(Constant::NAME_PASS_SIGNUP_FORM_REPASSWORD);

		if (strcmp($first_password, $confirm_password)) {
			return Constant::ERR_SIGNUP_PASS_MISMATCH;
		}

		if (empty($first_password) || empty($confirm_password)) {
			return Constant::ERR_PASSWORD_BLANK;
		}

		// validate password length
		$this->form_validation->set_rules(Constant::NAME_PASS_SIGNUP_FORM_PASSWORD, '', 'min_length[6]', ['min_length' => Constant::ERR_PASSWORD_LENGTH]);
		if (!$this->form_validation->run()) {
			return validation_errors();
		}
		return null; // no errors
	}

	/**
	 * Check for sign in form data.
	 * @return mixed If validation failed, return error message, null on success
	 */
	private function signin_validation() {
		// validate email
		$this->form_validation->set_rules(Constant::NAME_TEXT_SIGNIN_FORM_EMAIL, '', 'required|valid_email', ['required' => Constant::ERR_EMAIL_BLANK, 'valid_email' => Constant::ERR_EMAIL_FORMAT]);
		if (!$this->form_validation->run()) {
			return validation_errors();
		}

		// check password blank and length
		$password = $this->input->post(Constant::NAME_PASS_SIGNIN_FORM_PASSWORD);
		if (empty($password)) {
			return Constant::ERR_PASSWORD_BLANK;
		}
		if (strlen($password) < 6) {
			return Constant::ERR_PASSWORD_LENGTH;
		}

		return NULL; // no errors
	}

	private function forget_validation() {
		// validate email
		$this->form_validation->set_rules('email', '', 'required|valid_email', ['required' => Constant::ERR_EMAIL_BLANK, 'valid_email' => Constant::ERR_EMAIL_FORMAT]);
		if (!$this->form_validation->run()) {
			return validation_errors();
		}
		if (!$this->account->is_email_exist($this->input->post('email'))) {
			return Constant::ERR_EMAIL_NOT_EXIST;
		}
		return NULL;
	}

}
