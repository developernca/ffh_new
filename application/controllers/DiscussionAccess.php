<?php

/**
 *
 *
 * @author Nyein Chan Aung <developernca@gmail.com>
 */
class DiscussionAccess extends MY_Controller {

    public function __construct() {
        parent::__construct(['date', 'html', 'form', 'file'], ['constant', 'session', 'KeyGenerator', 'table'], ['account', 'post', 'discussion']);
    }

    /**
     * Authenticate user request
     *
     * @override authenticate()
     */
    protected function authenticate() {
        // Discussion should be call only via ajax request
        // If not so (unpermitted request), redirect to home page
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
            exit();
        }
        $authentication_flag = parent::authenticate();
        if ($authentication_flag === Constant::AUTH_ACTIVATION_REQUIRED) {
            exit(json_encode(['flg' => FALSE, 'action' => base_url() . 'index.php/confirmation']));
        } else if ($authentication_flag === Constant::AUTH_SESSION_NOT_EXIST) {
            exit(json_encode(['flg' => FALSE, 'action' => base_url()]));
        } else if ($authentication_flag === Constant::AUTH_ALREADY_LOGIN) {
            return;
        }
    }
    
    /**
     * Submit/post/insert discussion.
     */
    public function submit() {
        $this->authenticate();
        $discussions_inserted = $this->discussion->insert_discussion($this->input->post());
        if (!is_null($discussions_inserted)) {
            exit(json_encode([
                'flg' => TRUE,
                'msg' => $discussions_inserted
            ]));
        } else {
            exit(json_encode([
                'flg' => FALSE,
            ]));
        }
    }

    /**
     * Get discussion of user clicked post.
     *
     * @param string $post_id post id
     */
    public function get($post_id) {
        $this->authenticate();
        $discussion_list = $this->discussion->get_diss_by_postid($post_id);
        if (!is_null($discussion_list)) {
            exit(json_encode([
                'flg' => TRUE,
                'msg' => $discussion_list,
                'cu' => $this->session->userdata(Constant::SESSION_USSID)
            ]));
        } else {
            exit(json_encode([
                'flg' => FALSE
            ]));
        }
    }

    /**
     * Get all unseen discussions.
     * Use in notification.
     */
    public function get_unseen() {
        $this->authenticate();
        $result = $this->discussion->get_unseen_discussions();
        if (!is_null($result)) {
            exit(json_encode([
                'flg' => TRUE,
                'msg' => $result
            ]));
        } else {
            exit(json_encode([
                'flg' => FALSE
            ]));
        }
    }

    /**
     * Get unseen discussions count of current post, which is
     * the post that is showing on each/post/{pid} page.
     *
     * @param string $pid post id
     */
    public function currentpost_unseen_count($pid) {
        $this->authenticate();
        $diss_count = $this->discussion->get_unseen_discussions_count_by_postid($pid);
        if ($diss_count > 0) {
            exit(json_encode([
                'flg' => TRUE,
                'msg' => $diss_count
            ]));
        } else {
            exit(json_encode([
                'flg' => FALSE
            ]));
        }
    }

    /**
     * Edit a post.
     */
    public function edit() {
        $this->authenticate();
        $result = $this->discussion->update_disscussion_by_id($this->input->post());
        if (!is_null($result)) {
            exit(json_encode([
                'flg' => TRUE,
                'msg' => $result
            ]));
        } else {
            exit(json_encode(['flg' => false]));
        }
    }

    /**
     * Discussion delete request.
     *
     * @param type $dss_id discussion id to delete
     */
    public function delete($diss_id) {
        $this->authenticate();
        if ($this->discussion->delete_discussion_by_id($diss_id)) { // successfully deleted
            exit(json_encode([
                'flg' => TRUE
            ]));
        } else {
            exit(json_encode([
                'flg' => FALSE
            ]));
        }
    }

}
