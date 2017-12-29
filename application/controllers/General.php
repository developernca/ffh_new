<?php

class General extends MY_Controller {

	private $current_usr_name;
	private $current_usr_email;
	private $current_usr_id;
	private $form_data;

	public function __construct() {
		parent::__construct(['date', 'html', 'form', 'file'], ['form_validation', 'constant', 'session', 'KeyGenerator', 'table'], ['account', 'post', 'discussion']);
		$this->current_usr_name = $this->session->userdata(Constant::SESSION_CURRENTUSER_NAME);
		$this->current_usr_email = $this->session->userdata(Constant::SESSION_EMAIL);
		$this->current_usr_id = $this->session->userdata(Constant::SESSION_USSID);
	}

	protected function authenticate() {
		$authentication_flag = parent::authenticate();
		if ($authentication_flag === Constant::AUTH_ACTIVATION_REQUIRED) {
			redirect(base_url() . 'index.php/confirmation');
			exit();
		} else if ($authentication_flag === Constant::AUTH_SESSION_NOT_EXIST) {
			redirect(base_url());
			exit();
		} else if ($authentication_flag === Constant::AUTH_ALREADY_LOGIN) {
			return;
		}
	}

	public function index() {
		$this->authenticate();
		$this->load_view(Constant::GENERAL_VIEW, [
			Constant::VDN_CURRENT_USRNAME => $this->current_usr_name,
			Constant::VDN_CURRENT_USREMAIL => $this->current_usr_email,
			Constant::VDN_ACCFORM_ERRMSG => NULL,
			Constant::VDN_TITLE_KEY => Constant::GENERAL_TITLE,
			Constant::VDN_META_DESC_KEY => Constant::GENERAL_DESCRIPTION_META
		]);
	}

	public function account() {
		$this->authenticate();
		$this->form_data = $this->input->post();
		// Submit account form submit button
		if (isset($this->form_data[Constant::NAME_SUBMIT_GENERAL_ACCOUNT])) {
			$validation_errors = $this->accform_validate();
			if (is_null($validation_errors)) { // no validation errors
				$update_success = $this->account->update_account_by_id($this->current_usr_id, $this->form_data);
				(is_null($update_success)) ? redirect(base_url() . "index.php/general") : redirect(base_url() . "index.php/home/");
			} else { // validation errors occured
				$this->load_view(Constant::GENERAL_VIEW, [
					Constant::VDN_CURRENT_USRNAME => $this->form_data[Constant::NAME_TEXT_CURRENT_NAME],
					Constant::VDN_CURRENT_USREMAIL => $this->current_usr_email,
					Constant::VDN_ACCFORM_ERRMSG => $validation_errors
				]);
			}
		} else {
			redirect(base_url() . 'index.php/general/');
		}
	}

	private function accform_validate() {
		// validate name
		$this->form_validation->set_rules(Constant::NAME_TEXT_CURRENT_NAME, '', 'required|min_length[3]|max_length[200]', ['required' => Constant::ERR_NAME_BLANK, 'min_length' => Constant::ERR_NAME_MINLENGTH, 'max_length' => Constant::ERR_NAME_MAXLENGTH]);
		if (!$this->form_validation->run()) {
			return validation_errors();
		}
		// password validation is run only when password change check is checked
		if (isset($this->form_data[Constant::NAME_CHECKBOX_PASSCHANGE])) {
			// Current password blank error
			$this->form_validation->set_rules(Constant::NAME_PASS_CURRENT_PASSWORD, '', 'required', ['required' => Constant::ERR_PASSWORD_BLANK]);
			if (!$this->form_validation->run()) {
				return validation_errors();
			}
			// current password not match
			if (!$this->account->is_current_password_match($this->form_data[Constant::NAME_PASS_CURRENT_PASSWORD])) {
				return Constant::ERR_PASS_MISMATCH_OLD;
			}
			// password min length
			$this->form_validation->set_rules(Constant::NAME_PASS_NEW_PASSWORD, '', 'required|min_length[5]', ['required' => Constant::ERR_PASSWORD_BLANK, 'min_length' => Constant::ERR_PASSWORD_LENGTH]);
			if (!$this->form_validation->run()) {
				return validation_errors();
			}
			// Password match
			if ($this->form_data[Constant::NAME_PASS_NEW_PASSWORD] !== $this->form_data[Constant::NAME_PASS_NEW_REPASSWORD]) {
				return Constant::ERR_SIGNUP_PASS_MISMATCH;
			}
		}

		return NULL;
	}

}
