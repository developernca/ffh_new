<?php

// =========== begin create post container ============
if (!isset(${Constant::VDN_IS_SEARCH})) {
    echo '<div id = "id-div-cpcontainer" class = "cl-div-postcontainer">';
// =========== begin create post form ===================
    echo '<form id = "id-form-createpost">';
    echo form_input([
        'type' => 'text',
        'id' => 'id-text-posttitle',
        'class' => 'cl-text-medium',
        'name' => Constant::NAME_TEXT_POST_TITLE,
        'placeholder' => 'What kind of help do you need..',
        'style' => 'width: 90%;'
    ]);
    echo form_textarea([
        'id' => 'id-textarea-postcontent',
        'name' => Constant::NAME_TEXT_POST_CONTENT,
        'rows' => 12,
        'placeholder' => 'How can other people help you...'
    ]);
    echo '<br/>';
    echo form_input([
        'type' => 'text',
        'id' => 'id-text-contactmail',
        'class' => 'cl-text-medium',
        'name' => Constant::NAME_TEXT_CONTACT_EMAIL,
        'placeholder' => 'Contact email (optional)',
        'style' => 'width: 55%;'
    ]);
    echo '<br/>';
    echo form_input([
        'type' => 'text',
        'id' => 'id-text-contactphone',
        'class' => 'cl-text-medium',
        'name' => Constant::NAME_TEXT_CONTACT_PHONE,
        'placeholder' => 'Contact phone (optional)',
        'style' => 'width: 55%;'
    ]);
    echo '<br/>';
    echo form_input([
        'type' => 'text',
        'id' => 'id-text-remark',
        'class' => 'cl-text-medium',
        'name' => Constant::NAME_TEXT_POST_REMARK,
        'placeholder' => 'Remark (optional)',
        'style' => 'width: 55%;'
    ]);
    echo '<br/>';
    echo form_label('Choose type of your request : ');
    echo form_dropdown([
        'type' => 'select',
        'id' => 'id-select-type',
        'class' => 'cl-select-large',
        'name' => Constant::NAME_SELECT_POST_TYPE,
            ], ${Constant::VDN_POST_TYPES_OPTIONS});
    echo '<br/>';
    echo form_input([
        'type' => 'button',
        'id' => 'id-btn-submitpost',
        'class' => 'cl-btn-medium cl-common-hover',
        'value' => 'Submit',
        'onclick' => 'submitPost(\'' . base_url() . '\');'
    ]);
    echo form_input([
        'type' => 'hidden',
        'id' => 'id-hidden-createdat',
        'name' => Constant::NAME_HIDDEN_POST_CREATEDAT
    ]);
    echo '</form>';
// =========== begin post error p tag =================
    echo '<p id="id-p-createposterr" class="cl-p-createposterr"></p>';
// =========== end post error p tag==================
// =========== end create post form ===================
    echo '</div>';
}
// =========== end create post container ==================
// =========== begin current users post list ================
$post_list = ${Constant::VDN_ALL_POSTS};
if (!is_null($post_list) && !isset(${Constant::VDN_IS_SEARCH})) {
    // pagination links
    echo sprintf('<p class="cl-p-paginationlinks">%s</p>', ${Constant::VDN_PAGINATION_LINK});
    foreach ($post_list as $column => $row) {
        echo '<div class="cl-div-postcontainer">';
        echo '<p class="cl-p-eptitle">' . $row[Constant::TABLE_POSTS_COLUMN_POST_TITLE] . '</p>';
        // post updated time
        echo sprintf('<span class="cl-span-posttime">%s</span>', $row[Constant::TABLE_POSTS_COLUMN_UPDATED_TIME]);
        // posted user, if the post is updated by current user show you, name otherwise
        if ($row[Constant::TABLE_POSTS_COLUMN_ACCOUNT_ID] == $this->session->userdata(Constant::SESSION_USSID)) {
            echo sprintf('<span class="cl-span-postedby">%s</span>', 'Your post');
        } else {
            echo sprintf('<span class="cl-span-postedby">%s</span>', $row[Constant::TABLE_ACCOUNTS_COLUMN_NAME]);
        }
        echo '<p class="cl-p-epcontent">' . $row[Constant::TABLE_POSTS_COLUMN_TEXT_FILENAME] . '</p>';
        if (!is_null($row[Constant::TABLE_POSTS_COLUMN_CONTACT_EMAIL])) {
            $this->table->add_row('Contact Email', '<span class="cl-span-epcontactemail">' . $row[Constant::TABLE_POSTS_COLUMN_CONTACT_EMAIL] . '</span>');
        }
        if (!is_null($row[Constant::TABLE_POSTS_COLUMN_CONTACT_PHONE])) {
            $this->table->add_row('Contact Phone', '<span class="cl-span-epcontactphone">' . $row[Constant::TABLE_POSTS_COLUMN_CONTACT_PHONE] . '</span>');
        }
        if (!is_null($row[Constant::TABLE_POSTS_COLUMN_REMARK])) {
            $this->table->add_row('Remark', '<span class="cl-span-epremark">' . $row[Constant::TABLE_POSTS_COLUMN_REMARK] . '</span>');
        }
        $table = $this->table->generate();
        if (strcasecmp($table, "Undefined table data") !== 0) {
            echo $table;
        }
        // post id
        echo sprintf('<span name=%s class="cl-span-epid" style="display:none;">%s</span>', Constant::NAME_HIDDEN_POST_ID, $row[Constant::TABLE_POSTS_COLUMN_ID]);
        // Edit and delete button is shown only if the post is the current user post
        if ($row[Constant::TABLE_POSTS_COLUMN_ACCOUNT_ID] == $this->session->userdata(Constant::SESSION_USSID)) {
            // edit
            echo '<button class="cl-btn-small cl-btn-epedtbtn" onclick="postEditClick(this);" />&#9998;</button>';
            // delete
            echo sprintf('<button class="cl-btn-small cl-btn-epdelbtn" onclick="postDeleteClick(this,\'%s\');" />&#10007;</button>', base_url());
        }
        // post type
        echo sprintf('<span class="cl-span-posttype" value="%s"/>Type : %s</span>', array_search($row[Constant::TABLE_POSTS_COLUMN_TYPE], ${Constant::VDN_POST_TYPES_OPTIONS}), $row[Constant::TABLE_POSTS_COLUMN_TYPE]);
        echo '<br />';
        // load comment text
        echo sprintf('<span class="cl-span-showdiss" value="0" appended="0" onclick="showDiscussion(this,\'%s\');">Show discussion</span>', base_url());
        echo '</div>';
    }
    // pagination links
    echo sprintf('<p class="cl-p-paginationlinks">%s</p>', ${Constant::VDN_PAGINATION_LINK});
}
// =========== end current users post list ================
// =========== begin post search list =====================
else if (!is_null($post_list) && isset(${Constant::VDN_IS_SEARCH})) {
    // pagination links
    echo sprintf('<p class="cl-p-paginationlinks">%s</p>', ${Constant::VDN_PAGINATION_LINK});
    foreach ($post_list as $column => $row) {
        echo '<div class="cl-div-postcontainer">';
        echo '<p class="cl-p-eptitle">' . $row[Constant::TABLE_POSTS_COLUMN_POST_TITLE] . '</p>';
        // post updated time
        echo sprintf('<span class="cl-span-posttime">%s</span>', $row[Constant::TABLE_POSTS_COLUMN_UPDATED_TIME]);
        // posted user, if the post is updated by current user show your post, name otherwise
        if ($row[Constant::TABLE_POSTS_COLUMN_ACCOUNT_ID] == $this->session->userdata(Constant::SESSION_USSID)) {
            echo sprintf('<span class="cl-span-postedby">%s</span>', 'Your post');
        } else {
            echo sprintf('<span class="cl-span-postedby">%s</span>', $row[Constant::TABLE_ACCOUNTS_COLUMN_NAME]);
        }
        echo '<p class="cl-p-epcontent">' . $row[Constant::TABLE_POSTS_COLUMN_TEXT_FILENAME] . '</p>';
        if (!is_null($row[Constant::TABLE_POSTS_COLUMN_CONTACT_EMAIL])) {
            $this->table->add_row('Contact Email', '<span class="cl-span-epcontactemail">' . $row[Constant::TABLE_POSTS_COLUMN_CONTACT_EMAIL] . '</span>');
        }
        if (!is_null($row[Constant::TABLE_POSTS_COLUMN_CONTACT_PHONE])) {
            $this->table->add_row('Contact Phone', '<span class="cl-span-epcontactphone">' . $row[Constant::TABLE_POSTS_COLUMN_CONTACT_PHONE] . '</span>');
        }
        if (!is_null($row[Constant::TABLE_POSTS_COLUMN_REMARK])) {
            $this->table->add_row('Remark', '<span class="cl-span-epremark">' . $row[Constant::TABLE_POSTS_COLUMN_REMARK] . '</span>');
        }
        $table = $this->table->generate();
        if (strcasecmp($table, "Undefined table data") !== 0) {
            echo $table;
        }
        // post id
        echo sprintf('<span name=%s class="cl-span-epid" style="display:none;">%s</span>', Constant::NAME_HIDDEN_POST_ID, $row[Constant::TABLE_POSTS_COLUMN_ID]);
        // Edit and delete button is shown only if the post is the current user post
        if ($row[Constant::TABLE_POSTS_COLUMN_ACCOUNT_ID] == $this->session->userdata(Constant::SESSION_USSID)) {
            // edit
            echo '<button class="cl-btn-small cl-btn-epedtbtn" onclick="postEditClick(this);" />&#9998;</button>';
            // delete
            echo sprintf('<button class="cl-btn-small cl-btn-epdelbtn" onclick="postDeleteClick(this,\'%s\');" />&#10007;</button>', base_url());
        }
        // post type
        echo sprintf('<span class="cl-span-posttype" value="%s"/>Type : %s</span>', array_search($row[Constant::TABLE_POSTS_COLUMN_TYPE], ${Constant::VDN_POST_TYPES_OPTIONS}), $row[Constant::TABLE_POSTS_COLUMN_TYPE]);
        echo '<br />';
        // load comment text
        echo sprintf('<span class="cl-span-showdiss" value="0" appended="0" onclick="showDiscussion(this,\'%s\');">Show discussion</span>', base_url());
        echo '</div>';
    }
    // pagination links
    echo sprintf('<p class="cl-p-paginationlinks">%s</p>', ${Constant::VDN_PAGINATION_LINK});
}
// =========== end post search list =======================
// =================== Total post count ===================
echo sprintf('<p>Total %s posts.</p>', number_format(${Constant::VDN_TOTAL_POSTS_COUNT}));
