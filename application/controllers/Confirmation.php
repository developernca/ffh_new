<?php

class Confirmation extends MY_Controller {

    public function __construct() {
        parent::__construct(['html', 'form'], ['form_validation', 'constant', 'KeyGenerator', 'session'], ['account']);
    }

    /**
     * @return boolean true on authentication success, otherwise redirect
     */
    protected function authenticate() {
        $authentication_flag = parent::authenticate();
        if ($authentication_flag === Constant::AUTH_ACTIVATION_REQUIRED) {
            return;
        } else if ($authentication_flag === Constant::AUTH_SESSION_NOT_EXIST) {
            ($this->input->is_ajax_request()) ?
                            exit(json_encode(['flg' => TRUE, 'action' => base_url()])) :
                            redirect(base_url());
            exit();
        } else if ($authentication_flag === Constant::AUTH_ALREADY_LOGIN) {
            ($this->input->is_ajax_request()) ?
                            exit(json_encode(['flg' => TRUE, 'action' => base_url() . 'index.php/home'])) :
                            redirect(base_url() . 'index.php/home');
            exit();
        }
    }

    /**
     * default function.
     */
    public function index() {
        $this->authenticate();
        $this->load_view(Constant::CONFIRMATION_VIEW, [Constant::VDN_SESSION_EMAIL => $this->session->userdata(Constant::SESSION_EMAIL)]);
    }

    /**
     * Account activation. Call when activate button click on page.
     */
    public function activate() {
        $this->authenticate();
        $code = $this->input->post(Constant::NAME_TEXT_ACTIVATION_CODE);
        if (empty(trim($code))) {// return with error if code was blank
            exit(json_encode([
                'flg' => FALSE,
                'msg' => Constant::ERR_BLANK_ACTCODE
            ]));
        } else if (strlen($code) != 6) {// return with error if code was longer than 6 or less than 6
            exit(json_encode([
                'flg' => FALSE,
                'msg' => Constant::ERR_LONGER_ACTCODE
            ]));
        } else {// if no error
            // activate
            $activation_success = $this->account->activate_account(
                    $this->session->userdata(Constant::SESSION_USSID)
                    , $this->session->userdata(Constant::SESSION_EMAIL)
                    , $code);

            if ($activation_success) {// activation success
                exit(json_encode([
                    'flg' => $activation_success,
                    'action' => base_url() . 'index.php/home'
                ]));
            }
            if (!$activation_success) { // code mismatch
                exit(json_encode([
                    'flg' => $activation_success
                    , 'msg' => Constant::ERR_ACTVCODE_MISMATCH
                ]));
            }
            if (is_null($activation_success)) {// database update failure
                exit(json_encode([
                    'flg' => FALSE
                    , 'msg' => Constant::ERR_ACTVCODE_UPDATE
                ]));
            }
        }
    }

    /**
     * Resend activatin code to user eamil.
     */
    public function resend_code() {
        $this->authenticate();
        $updated_code = $this->account->update_activation_code_by_id($this->session->userdata(Constant::SESSION_USSID));
        if (!is_null($updated_code)) {// send activation code to user mail
            $this->send_activation_mail($this->session->userdata(Constant::SESSION_EMAIL), $updated_code);
        }
        $this->load_view(Constant::CONFIRMATION_VIEW, [
            Constant::VDN_SESSION_EMAIL => $this->session->userdata(Constant::SESSION_EMAIL),
            Constant::LINK_PARAM_RESEND_CONCODE => $updated_code
        ]);
    }

    /**
     * Change email.
     */
    public function change_email() {
        $this->authenticate();
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }
        $this->form_validation->set_rules(Constant::NAME_TEXT_RESEND_EMAIL, '', 'required|valid_email', ['required' => Constant::ERR_EMAIL_BLANK, 'valid_email' => Constant::ERR_EMAIL_FORMAT]);
        if (!$this->form_validation->run()) {
            exit(json_encode([
                'flg' => FALSE,
                'msg' => strip_tags(validation_errors())
            ]));
        }
        $post_data = $this->input->post();
        $email = $post_data[Constant::NAME_TEXT_RESEND_EMAIL];
        if ($this->account->is_email_exist($email)) {
            exit(json_encode([
                'flg' => FALSE,
                'msg' => Constant::ERR_SIGNUP_EMAIL_EXIST
            ]));
        }
        $updated_result = $this->account->update_email_by_id($email, $this->session->userdata(Constant::SESSION_USSID));
        if (is_null($updated_result)) {
            exit(json_encode([
                'flg' => FALSE,
                'msg' => 'Unexcepted error occured. Please, refresh page and try again.'
            ]));
        } else {
            $this->send_activation_mail($email, $updated_result);
            $this->session->set_userdata([Constant::SESSION_EMAIL => $email]);
            exit(json_encode([
                'flg' => TRUE,
                'msg' => $email
            ]));
        }
    }

    /**
     * @override
     */
    public function signout() {
        parent::signout();
    }

}
