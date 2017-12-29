<?php

class Discussion extends CI_Model {

    private $posted_user;
    private $base_path;

    public function __construct() {
        parent::__construct();
        $this->posted_user = $this->session->userdata(Constant::SESSION_USSID);
        $this->base_path = FCPATH . DIRECTORY_SEPARATOR . 'usr' . DIRECTORY_SEPARATOR . $this->posted_user . DIRECTORY_SEPARATOR; // ffh/usr/usr_id/
    }

    /**
     * Get discussions by post id
     *
     * @param string $post_id post id
     * @return mixed result set array or null on failure
     */
    public function get_diss_by_postid($post_id) {
        $this->db->select('accounts.name ,discussions.*');
        $this->db->join(Constant::TABLE_ACCOUNTS, 'accounts._id = discussions.discussed_by');
        $query = $this->db->get_where(Constant::TABLE_DISCUSSIONS, [Constant::TABLE_DISCUSSION_COLUMN_POST_ID => $post_id]);
        $result = $query->result_array();
        if (is_null($result) || empty($result)) {
            return NULL;
        } else {
            // set file path with file contents
            $resultLength = count($result);
            for ($col = 0; $col < $resultLength; $col++) {
                $file_contents = nl2br(file_get_contents($result[$col][Constant::TABLE_DISCUSSION_COLUMN_FILENAME]));
                $result[$col][Constant::TABLE_DISCUSSION_COLUMN_FILENAME] = auto_link($file_contents, 'url', TRUE);
            }
            // This function restrict users from calling each/post/{post_id} if not the post owner.
            $post_owner = $this->post->get_posteduser_by_postid($post_id);
            if ($post_owner === $this->posted_user) {
                // Update seen status of current post of current user to TRUE(1)
                $this->db->update(Constant::TABLE_DISCUSSIONS, [
                    Constant::TABLE_DISCUSSION_COLUMN_SEEN => TRUE
                        ], [
                    Constant::TABLE_DISCUSSION_COLUMN_POST_ID => $post_id,
                    Constant::TABLE_DISCUSSION_COLUMN_SEEN => FALSE
                ]);
            }
            return $result;
        }
    }

    /**
     * Get current user's post discussions that are not yet see.
     * @return mixed result set array or NULL
     */
    public function get_unseen_discussions() {
        $this->db->select('p._id as pid, p.post_title as title, count(discussions._id) as dcount');
        $this->db->join('posts as p', 'p._id = discussions.post_id', 'inner');
        $this->db->join('accounts as a', 'a._id = p.account_id', 'inner');
        $this->db->group_by('p._id');
        $result = $this->db->get_where(Constant::TABLE_DISCUSSIONS, [
                    'a._id' => $this->posted_user,
                    'discussions.discussed_by != ' => $this->posted_user,
                    'discussions.seen' => 0
                ])->result_array();
        if (!is_null($result) && !empty($result)) {
            return $result;
        } else {
            return NULL;
        }
    }

    /**
     *
     * @param string $pid post id
     * @return int
     */
    public function get_unseen_discussions_count_by_postid($pid) {
        $this->db->where([
            Constant::TABLE_DISCUSSION_COLUMN_POST_ID => $pid,
            Constant::TABLE_DISCUSSION_COLUMN_SEEN => FALSE,
            Constant::TABLE_DISCUSSION_COLUMN_DISCUSSEDBY . ' != ' => $this->posted_user
        ]);
        $this->db->from(Constant::TABLE_DISCUSSIONS);
        return $this->db->count_all_results();
    }

    /**
     * Insert new record in discussions table.
     * Generate unique id and unique file name for discussion text.
     * Write discussion to text.
     *
     * @param type $data user submitted form data
     * @return mixed return submitted data on success or null on failure
     */
    public function insert_discussion($data) {
        $diss_id = null;
        do {
            $diss_id = KeyGenerator::getAlphaNumString(10, true, true);
            $query = $this->db->get_where(Constant::TABLE_DISCUSSIONS, [Constant::TABLE_DISCUSSION_COLUMN_ID => $diss_id]);
            $result = $query->result();
        } while (!empty($result));
        // create file
        $filename = null;
        do {
            $filename = $this->base_path . KeyGenerator::getAlphaNumString(10, true, true) . '.txt';
            $is_file_exist = file_exists($filename);
        } while ($is_file_exist);
        // write content to file
        write_file($filename, $data['diss'], 'w+');
        // values to insert
        $values = [
            Constant::TABLE_POSTS_COLUMN_ID => $diss_id,
            Constant::TABLE_DISCUSSION_COLUMN_FILENAME => $filename,
            Constant::TABLE_DISCUSSION_COLUMN_DISCUSSEDBY => $this->posted_user,
            Constant::TABLE_DISCUSSION_COLUMN_UPDATEDAT => $data['updated_at'],
            Constant::TABLE_DISCUSSION_COLUMN_POST_ID => $data['pid']
        ];
        // insert
        $insert_success = $this->db->insert(Constant::TABLE_DISCUSSIONS, $values);
        if ($insert_success) {
            // get user name
            $this->db->select(Constant::TABLE_ACCOUNTS_COLUMN_NAME);
            $name = $this->db->get_where(Constant::TABLE_ACCOUNTS, [Constant::TABLE_ACCOUNTS_COLUMN_ID => $this->posted_user])->result_array();
            $values[Constant::TABLE_DISCUSSION_COLUMN_DISCUSSEDBY] = $name[0][Constant::TABLE_ACCOUNTS_COLUMN_NAME];
            // get file contents
            $values[Constant::TABLE_DISCUSSION_COLUMN_FILENAME] = auto_link(nl2br(file_get_contents($filename)), 'url', TRUE);
            return $values;
        } else {
            return NULL;
        }
    }

    /**
     * Update discussion.
     *
     * @param array $data values to update
     * @return mixed return updated values array or null on failure
     */
    public function update_disscussion_by_id($data) {
        // get file name of current updated post
        $this->db->select(Constant::TABLE_DISCUSSION_COLUMN_FILENAME);
        $path = $this->db->get_where(Constant::TABLE_DISCUSSIONS, [Constant::TABLE_DISCUSSION_COLUMN_ID => $data['diss_id'], Constant::TABLE_DISCUSSION_COLUMN_DISCUSSEDBY => $this->posted_user])->result_array();
        if (!is_null($path) && !empty($path)) { // work only if flie path is not null
            // write data to file
            $write_success = write_file($path[0][Constant::TABLE_DISCUSSION_COLUMN_FILENAME], $data['discussion']);
        } else {
            return NULL;
        }
        if ($write_success) {
            // format discussion text
            $data["discussion"] = auto_link(nl2br($data["discussion"]), 'url', TRUE);
            $this->db->where(Constant::TABLE_DISCUSSION_COLUMN_DISCUSSEDBY, $this->posted_user);
            $this->db->where(Constant::TABLE_DISCUSSION_COLUMN_ID, $data['diss_id']);
            $result = $this->db->update(Constant::TABLE_DISCUSSIONS, [
                Constant::TABLE_DISCUSSION_COLUMN_UPDATEDAT => $data['updated_at']
            ]);
            return $result ? $data : NULL;
        } else {
            return NULL;
        }
    }

    /**
     * Delete a discussion.
     *
     * @param string $id discussion id
     * @return boolean true on success, false on failure
     */
    function delete_discussion_by_id($id) {
        $this->db->trans_begin();
        // get file path to delete content file
        $this->db->select(Constant::TABLE_DISCUSSION_COLUMN_FILENAME);
        $path = $this->db->get_where(Constant::TABLE_DISCUSSIONS, [
                    Constant::TABLE_DISCUSSION_COLUMN_ID => $id])->result_array();
        // delete table data
        $this->db->where(Constant::TABLE_DISCUSSION_COLUMN_ID, $id);
        $db_del_success = $this->db->delete(Constant::TABLE_DISCUSSIONS);
        if (!$db_del_success) {
            return FALSE;
        }
        // delete file
        $file_del_success = unlink($path[0][Constant::TABLE_DISCUSSION_COLUMN_FILENAME]);
        if (!$file_del_success) {
            $this->db->trans_rollback();
            return FALSE;
        }
        $this->db->trans_commit();
        return TRUE;
    }

}
