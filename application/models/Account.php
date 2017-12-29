<?php

class Account extends CI_Model {

    private $current_account_id;
    private $current_account_name;

    public function __construct() {
        parent::__construct();
        $this->current_account_id = $this->session->userdata(Constant::SESSION_USSID);
        $this->current_account_name = $this->session->userdata(Constant::SESSION_CURRENTUSER_NAME);
    }

    /**
     * Save data to accounts table.
     *
     * @param type $data sign up form data
     * @return array return activation code and current inserted id if registration success, null otherwise
     */
    public function register($data) {
        // generate user id
        $usr_id = null;
        do {
            $usr_id = KeyGenerator::getAlphaNumString(10, true, true);
            $query = $this->db->get_where(Constant::TABLE_ACCOUNTS, [Constant::TABLE_ACCOUNTS_COLUMN_ID => $usr_id]);
            $result = $query->result();
        } while (!empty($result));
        // create current user folder
        $user_dir = FCPATH . DIRECTORY_SEPARATOR . 'usr' . DIRECTORY_SEPARATOR; // users base directory
        $current_user_dir = $user_dir . $usr_id; // current user directory
        mkdir($current_user_dir);
        // copy .htaccess file from usr folder to prevent directory access of specific user folder
        copy($user_dir . DIRECTORY_SEPARATOR . '.htaccess', $current_user_dir . DIRECTORY_SEPARATOR . '.htaccess');
        // generate activation code
        $activation_code = $this->generate_unique_actvcode();
        $hashed_password = password_hash($data[Constant::NAME_PASS_SIGNUP_FORM_PASSWORD], PASSWORD_DEFAULT);
        $usr_name = explode('@', $data[Constant::NAME_TEXT_SIGNUP_FORM_EMAIL]);
        $values = [
            Constant::TABLE_ACCOUNTS_COLUMN_ID => $usr_id
            , Constant::TABLE_ACCOUNTS_COLUMN_EMAIL => $data[Constant::NAME_TEXT_SIGNUP_FORM_EMAIL]
            , Constant::TABLE_ACCOUNTS_COLUMN_NAME => $usr_name[0]
            , Constant::TABLE_ACCOUNTS_COLUMN_PASSWORD => $hashed_password
            , Constant::TABLE_ACCOUNTS_COLUMN_CREATEDTIME => $data[Constant::NAME_HIDDEN_SIGNUP_TIME]
            , Constant::TABLE_ACCOUNTS_COLUMN_NUMBEROF_USER => NULL
            , Constant::TABLE_ACCOUNTS_COLUMN_IS_ACTIVATED => FALSE
            , Constant::TABLE_ACCOUNTS_COLUMN_ACTIVATION_CODE => $activation_code];
        $insert_success = $this->db->insert(Constant::TABLE_ACCOUNTS, $values);
        if ($insert_success) {
            return ['activation_code' => $activation_code, 'id' => $usr_id, 'name' => $usr_name[0]];
        } else {
            return null;
        }
    }

    /**
     * Test whether email already exist.
     *
     * @param type $email sign up form email
     * @return boolean true if email exist, false otherwise
     */
    public function is_email_exist($email) {
        $this->db->select(Constant::TABLE_ACCOUNTS_COLUMN_EMAIL);
        $query = $this->db->get_where(Constant::TABLE_ACCOUNTS, [Constant::TABLE_ACCOUNTS_COLUMN_EMAIL => $email]);
        $result = $query->result();
        return !empty($result);
    }

    /**
     * Update password by email in case of forgetting password.
     * 
     * @param type $email account email
     * @return mixed new password on update success, otherwise false
     */
    public function update_password_by_email($email) {
        $generated_password = KeyGenerator::getAlphaNumString(8); // generate 8 characters password
        $hashed_password = password_hash($generated_password, PASSWORD_DEFAULT);
        if ($hashed_password !== FALSE) {
            $result = $this->db->update(Constant::TABLE_ACCOUNTS, [
                Constant::TABLE_ACCOUNTS_COLUMN_PASSWORD => $hashed_password], [
                Constant::TABLE_ACCOUNTS_COLUMN_EMAIL => $email
            ]);
            return ($result) ? $generated_password : FALSE;
        } else {
            return FALSE;
        }
    }

    /**
     * Check whether current user activated his/her
     * account.
     *
     * @param type $cussid string current user session id
     * @param type $email string  current user email
     * @return boolean true if current account is activated, false otherwise
     */
    public function is_activated($cussid, $email) {
        $query = $this->db->get_where(Constant::TABLE_ACCOUNTS, [Constant::TABLE_ACCOUNTS_COLUMN_ID => $cussid, Constant::TABLE_ACCOUNTS_COLUMN_EMAIL => $email, Constant::TABLE_ACCOUNTS_COLUMN_IS_ACTIVATED => TRUE]);
        $result = $query->result();
        return empty($result) ? FALSE : TRUE;
    }

    /**
     * Activate current account, if id and code are correct.
     *
     * @param string $id account id
     * @param string $code activation code
     * @return mixed
     */
    public function activate_account($id, $email, $code) {
        $result = $this->db->update(Constant::TABLE_ACCOUNTS, [
            Constant::TABLE_ACCOUNTS_COLUMN_IS_ACTIVATED => TRUE], [
            Constant::TABLE_ACCOUNTS_COLUMN_ID => $id,
            Constant::TABLE_ACCOUNTS_COLUMN_ACTIVATION_CODE => $code
        ]);
        if ($result) {
            return $this->is_activated($id, $email);
        } else {
            return null;
        }
    }

    /**
     * Check whether sign in form email and password match with existed data in database.
     *
     * @param string $email submitted sign in form email
     * @param string $password submitted sign in form password
     * @return mixed return null if email and password did not match, otherwise return account _id, name
     */
    public function is_signin_correct($email, $password) {
        $this->db->select(Constant::TABLE_ACCOUNTS_COLUMN_PASSWORD);
        $query = $this->db->get_where(Constant::TABLE_ACCOUNTS, [Constant::TABLE_ACCOUNTS_COLUMN_EMAIL => $email]);
        $result = $query->result_array();
        if (count($result) == 1) { // password matching
            return password_verify($password, $result[0][Constant::TABLE_ACCOUNTS_COLUMN_PASSWORD]) ? $this->get_acc_by_email($email, [Constant::TABLE_ACCOUNTS_COLUMN_ID, Constant::TABLE_ACCOUNTS_COLUMN_NAME]) : NULL;
        } else { // email did not match
            return NULL;
        }
    }

    /**
     * Update account activation code by id.
     *
     * @param type $id id to search
     * @return mixed new activation code on success, NULL on failure
     */
    public function update_activation_code_by_id($id) {
        $activation_code = $this->generate_unique_actvcode();
        $update_success = $this->db->update(Constant::TABLE_ACCOUNTS, [
            Constant::TABLE_ACCOUNTS_COLUMN_ACTIVATION_CODE => $activation_code
            ], [
            Constant::TABLE_ACCOUNTS_COLUMN_ID => $id
        ]);
        return $update_success ? $activation_code : NULL;
    }

    /**
     * Update email and activation code of the current user.
     *
     * @param type $email email to update
     * @param type $acc_id account id
     * @return mixed return activation code or NULL on failure
     */
    public function update_email_by_id($email, $acc_id) {
        // splited email from @ sign to use as name
        $splited_email = explode("@", $email);
        $activation_code = $this->generate_unique_actvcode();
        $update_success = $this->db->update(Constant::TABLE_ACCOUNTS, [
            Constant::TABLE_ACCOUNTS_COLUMN_EMAIL => $email,
            Constant::TABLE_ACCOUNTS_COLUMN_ACTIVATION_CODE => $activation_code,
            Constant::TABLE_ACCOUNTS_COLUMN_NAME => $splited_email[0]
            ], [
            Constant::TABLE_ACCOUNTS_COLUMN_ID => $acc_id
        ]);
        return $update_success ? $activation_code : NULL;
    }

    /**
     *
     * @param String $id account id
     * @param Array $value data to update, form data
     * @return mixed true on success , false on failure, NULL on no change
     */
    public function update_account_by_id($id, $value) {
        $name = $value[Constant::NAME_TEXT_CURRENT_NAME];
        $updated_count = 0;
        if ($name !== $this->current_account_name) {
            $this->db->set(Constant::TABLE_ACCOUNTS_COLUMN_NAME, $value[Constant::NAME_TEXT_CURRENT_NAME]);
            ++$updated_count;
        }
        if (isset($value[Constant::NAME_CHECKBOX_PASSCHANGE]) && !empty($value[Constant::NAME_CHECKBOX_PASSCHANGE])) {
            $this->db->set(Constant::TABLE_ACCOUNTS_COLUMN_PASSWORD, password_hash($value[Constant::NAME_PASS_NEW_PASSWORD], PASSWORD_DEFAULT));
            ++$updated_count;
        }
        if ($updated_count === 0) {
            return NULL;
        } else {
            $this->db->where(Constant::TABLE_ACCOUNTS_COLUMN_ID, $id);
            $update_success = $this->db->update(Constant::TABLE_ACCOUNTS);
            if ($update_success) {
                $this->session->set_userdata([Constant::SESSION_CURRENTUSER_NAME => $value[Constant::NAME_TEXT_CURRENT_NAME]]);
            }
            return $update_success;
        }
    }

    /**
     * Check whether current password is correct or not.
     * This function is used in case of account update.
     *
     * @param type $password current form password
     * @return mixed TRUE if password match, FALSE if password did not match, NULL on failure
     */
    public function is_current_password_match($password) {
        $result = $this->db->get_where(Constant::TABLE_ACCOUNTS, [Constant::TABLE_ACCOUNTS_COLUMN_ID => $this->current_account_id])->result_array();
        if (count($result) === 1) {
            return password_verify($password, $result[0][Constant::TABLE_ACCOUNTS_COLUMN_PASSWORD]);
        } else {
            return NULL;
        }
    }

    /**
     * Get user data by email.
     *
     * @param type $email email to check
     * @param array $column columns to retrieve
     * @return array result set array
     */
    private function get_acc_by_email($email, array $column = null) {
        $this->db->select(implode(',', $column));
        $query = $this->db->get_where(Constant::TABLE_ACCOUNTS, [Constant::TABLE_ACCOUNTS_COLUMN_EMAIL => $email]);
        $result = $query->result_array();
        return $result[0];
    }

    /**
     * Generate unique activation code in case of register and
     * resending activation code.
     *
     * @return String unique activation code
     */
    private function generate_unique_actvcode() {
        $activation_code = '';
        do {
            $activation_code = KeyGenerator::getAlphaNumString(6);
            $query = $this->db->get_where(Constant::TABLE_ACCOUNTS, [Constant::TABLE_ACCOUNTS_COLUMN_ACTIVATION_CODE => $activation_code]);
            $result = $query->result();
        } while (!empty($result));
        return $activation_code;
    }

}
