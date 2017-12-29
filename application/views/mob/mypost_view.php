<?php

// =========== begin create post container ============
echo '<div id = "id-div-cpcontainer" class = "cl-div-postcontainer">';
// =========== end create post form ===================
echo '<form id = "id-form-createpost">';
echo form_input([
    'type' => 'text',
    'id' => 'id-text-posttitle',
    'class' => 'cl-text-medium',
    'name' => Constant::NAME_TEXT_POST_TITLE,
    'placeholder' => 'What kind of help do you need..',
    'style' => 'width: 99%;'
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
    'style' => 'width: 99%;'
]);
echo '<br/>';
echo form_input([
    'type' => 'text',
    'id' => 'id-text-contactphone',
    'class' => 'cl-text-medium',
    'name' => Constant::NAME_TEXT_CONTACT_PHONE,
    'placeholder' => 'Contact phone (optional)',
    'style' => 'width: 99%;'
]);
echo '<br/>';
echo form_input([
    'type' => 'text',
    'id' => 'id-text-remark',
    'class' => 'cl-text-medium',
    'name' => Constant::NAME_TEXT_POST_REMARK,
    'placeholder' => 'Remark (optional)',
    'style' => 'width: 99%;'
]);
echo '<br/>';
echo form_label('Choose type of your request : ', null, ['id' => 'id-label-posttype']);
echo '<br/>';
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
// =========== begin post error p tag ====================
echo '<p id="id-p-createposterr" class="cl-p-createposterr"></p>';
// =========== end post error p tag=======================
// =========== end create post form ======================
echo '</div>';
// =========== end create post container ==================
//                                                       //
//                                                       //
//                                                       //
// =========== begin current users post list ==============
// =========== begin notification for discussion ==========
$post_list = ${Constant::VDN_CURRENTUSER_POST_LISTS};
if (!is_null($post_list)) {
    // pagination links
    echo sprintf('<p class="cl-p-paginationlinks">%s</p>', ${Constant::VDN_PAGINATION_LINK});
    foreach ($post_list as $column => $row) {
        echo '<div class="cl-div-postcontainer">';
        echo '<p class="cl-p-eptitle">' . $row[Constant::TABLE_POSTS_COLUMN_POST_TITLE] . '</p>';
        // post updated time
        echo sprintf('<span class="cl-span-posttime">%s</span>', $row[Constant::TABLE_POSTS_COLUMN_UPDATED_TIME]);
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
        // edit
        echo '<button class="cl-btn-small cl-btn-epedtbtn" onclick="postEditClick(this);">&#9998;</button>';
        // delete
        echo sprintf('<button class="cl-btn-small cl-btn-epdelbtn" onclick="postDeleteClick(this,\'%s\');">&#10007;</button>', base_url());
        // post type
        echo sprintf('<span class="cl-span-posttype" value="%s">Type : %s</span>', array_search($row[Constant::TABLE_POSTS_COLUMN_TYPE], ${Constant::VDN_POST_TYPES_OPTIONS}), $row[Constant::TABLE_POSTS_COLUMN_TYPE]);
        echo '<br />';
        // load comment text
        echo sprintf('<span class="cl-span-showdiss" value="0" appended="0" onclick="showDiscussion(this,\'%s\');">Show discussion</span>', base_url());
        echo '</div>';
    }
    // pagination links
    echo sprintf('<p class="cl-p-paginationlinks">%s</p>', ${Constant::VDN_PAGINATION_LINK});
}
// =========== end current users post list ================
// =================== Total post count ===================
echo sprintf('<p>Total %s posts.</p>', number_format(${Constant::VDN_TOTAL_POSTS_COUNT}));
