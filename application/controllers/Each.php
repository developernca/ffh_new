<?php

/**
 * @author Nyein Chan Aung<developernca@gmail.com>
 */
class Each extends MY_Controller {

    public function __construct() {
        parent::__construct(['date', 'html', 'form', 'file'], ['form_validation', 'constant', 'session', 'KeyGenerator', 'table', 'pagination'], ['account', 'post', 'discussion']);
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
     * Get each post by given id.
     * When fetch post, all related discussions to this post will be fetched.
     * The main purpose of the function is to satisfy request that come by
     * clicking discussions notification links of bell icon in page. This
     * request should not be ajax.
     * 
     * @param string $pid post id
     */
    public function post($pid) {
        $this->authenticate();
        // get post
        $post = $this->post->get_post_by_id($pid);
        if (is_null($post)) {
            redirect(base_url());
        }
        // get discussions
        $discussion_list = $this->discussion->get_diss_by_postid($pid);
        // get and sort post type array
        $post_type = Constant::POST_TYPE_OPTIONS_ARR;
        sort($post_type);
        // load view
        $this->load_view(Constant::EACH_POST_VIEW, [
            Constant::VDN_EACH_POST => $post,
            Constant::VDN_DISCUSSION_LIST_EACH => $discussion_list,
            Constant::VDN_POST_TYPES_OPTIONS => $post_type
        ]);
    }

}
