<?php

echo '<div>';
echo '<p>';
echo anchor(base_url() . 'index.php/confirmation/signout', 'Sign Out', ['class' => 'cl-link-signout']);
echo '</p>';
// =========== CONFIRM ===========
echo '<div class="cl-div-confirmation" id="id-div-confirm">'; // begin confirmation div
//
// confirmation code sent/resent to email alert
if (isset(${Constant::LINK_PARAM_RESEND_CONCODE})) { // code regenerating
    if (!is_null(${Constant::LINK_PARAM_RESEND_CONCODE})) {
        // regenerating success
        echo '<p id="id-p-csinfo">Activation code resent to,</p>';
        echo sprintf('<p style="color: #0000FF;"> %s </p>', ${Constant::VDN_SESSION_EMAIL});
    } else {
        // regenerating failure
        echo '<p id="id-p-csinfo">Sorry, something went wrong.</p>';
        echo sprintf('<p style="color: #0000FF;"> %s </p>', 'Please, try again!');
    }
} else { // normal case
    echo '<p id="id-p-csinfo">A 6 digits activation code was sent to,</p>';
    echo sprintf('<p style="color: #0000FF;"> %s </p>', ${Constant::VDN_SESSION_EMAIL});
}

echo '<form id="id-form-actvcode">';

echo form_input([
    'class' => 'cl-text-medium',
    'id' => 'id-text-activation',
    'name' => Constant::NAME_TEXT_ACTIVATION_CODE,
    'size' => 25,
    'placeholder' => 'Enter activation code'
]);

echo '<br />';

echo form_input([
    'type' => 'button',
    'class' => 'cl-accessable cl-btn-medium',
    'id' => 'id-btn-activation',
    'onclick' => 'sendActcode(\'' . base_url() . '\');'
    ], 'Activate');

echo '</form>';

echo anchor(base_url() . 'index.php/confirmation/resend_code/' . Constant::LINK_PARAM_RESEND_CONCODE, 'Did not receive code? Resend code...', ['id' => 'id-link-rscode', 'class' => 'cl-accessable']);

echo '</div>'; // end of confirmation div

echo '<br />';

// =========== EMAIL RESEND ======

echo '<div class="cl-div-confirmation" id="id-div-mresend">'; // begin email resend div

echo '<p id="id-p-iminfo">Enter wrong email address?</p>';

echo '<form id="id-form-remail">';

echo form_input([
    'class' => 'cl-text-medium',
    'id' => 'id-text-remail',
    'name' => Constant::NAME_TEXT_RESEND_EMAIL,
    'size' => 25,
    'placeholder' => 'Enter email'
]);

echo '<br />';

echo form_input([
    'type' => 'button',
    'class' => 'cl-accessable cl-btn-medium',
    'id' => 'id-btn-remail',
    'onclick' => 'changeEmail(\'' . base_url() . '\') '
    ], 'Send');

echo '</form>';
echo '<p id="id-p-cmerror" class="cl-error-small" ></p>';
echo '</div>'; // end of email resend div

echo '</div>';
