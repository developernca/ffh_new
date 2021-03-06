<?php

echo form_open(base_url() . 'index.php/general/account/', ['id' => 'id-form-genralacc', 'method' => 'post']);

// ============== Table template ====================
$template = array(
    'table_open' => '<table id="id-table-general" border="0" cellpadding="15" cellspacing="0">',
    'tbody_open' => '<tbody>',
    'tbody_close' => '</tbody>',
    'row_start' => '<tr>',
    'row_end' => '</tr>',
    'cell_start' => '<td>',
    'cell_end' => '</td>',
    'table_close' => '</table>'
);
$this->table->set_template($template);

// ================================ Validation errors =========================================
$form_err_text = '<span class="cl-error-small">' . ${Constant::VDN_ACCFORM_ERRMSG} . '</span>';
$this->table->add_row([
    'colspan' => 2,
    'data' => $form_err_text
]);

// ============== Current name ====================
$this->table->add_row(['Current name', form_input([
        'name' => Constant::NAME_TEXT_CURRENT_NAME,
        'class' => 'cl-text-medium',
        'value' => ${Constant::VDN_CURRENT_USRNAME},
        'length' => '30'
])]);

// ================= Email ========================
$this->table->add_row(['Email', form_input([
        'name' => Constant::NAME_TEXT_CURRENT_EMAIL,
        'class' => 'cl-text-medium',
        'length' => '30',
        'value' => ${Constant::VDN_CURRENT_USREMAIL},
        'disabled' => TRUE
])]);

// ============== Change password ====================
$password_change_chbk = form_checkbox([
    'type' => 'checkbox',
    'name' => Constant::NAME_CHECKBOX_PASSCHANGE,
    'value' => 1,
    'checked' => FALSE,
    'style' => 'transform: scale(2.5);'
        ]);

$this->table->add_row([
    'colspan' => 2,
    'data' => $password_change_chbk . 'Change password',
    'style' => 'text-align: center;font-size: x-large;'
]);

// ============== Current password ====================
$this->table->add_row(['Current password', form_input([
        'type' => 'password',
        'name' => Constant::NAME_PASS_CURRENT_PASSWORD,
        'class' => 'cl-text-medium',
        'length' => '30'
])]);

// ============== New password ====================
$this->table->add_row(['New password', form_input([
        'type' => 'password',
        'name' => Constant::NAME_PASS_NEW_PASSWORD,
        'class' => 'cl-text-medium',
        'length' => '30'
])]);

// ============== New re password ==========================
$this->table->add_row(['Re-enter new password', form_input([
        'type' => 'password',
        'name' => Constant::NAME_PASS_NEW_REPASSWORD,
        'class' => 'cl-text-medium',
        'length' => '30'
])]);

// ============== Submit button =====================
$save_change_button = form_input([
    'type' => 'submit',
    'class' => 'cl-btn-medium cl-common-hover',
    'name' => Constant::NAME_SUBMIT_GENERAL_ACCOUNT,
    'value' => 'Save'
        ]);

$this->table->add_row([
    'colspan' => 2,
    'data' => $save_change_button,
    'style' => 'text-align: center'
]);

// ============== genrate table ====================
echo $this->table->generate();
echo form_close();
echo '<br/>';
echo '<div class="cl-div-postcontainer" style="width: 96%; line-height: 40px;">' . Constant::USER_COMPLAINT . safe_mailto('contact@theffh.com', 'contact@theffh.com', ['style' => 'font-size: medium;']) . '</div>';
//echo form_open(base_url() . 'index.php/general/contact', ['method' => 'post', 'id' => 'id-form-generalcontact']);
//echo form_close();

