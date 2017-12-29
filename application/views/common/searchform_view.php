<?php

echo '<div id="id-div-searchbar" class="cl-div-postcontainer">';
$_searched_key = null;
$_searched_selected = null;
if (isset(${Constant::VDN_SEARCHED_KEY})) {
    $_searched_selected = ${Constant::VDN_SEARCHED_SELECT};
    $_searched_key = ${Constant::VDN_SEARCHED_KEY};
}
echo form_open(base_url() . 'index.php/home/search', ['id' => 'id-form-search', 'method' => 'get']);
echo form_input([
    'id' => 'id-text-searchkey',
    'class' => 'cl-text-medium',
    'placeholder' => 'Search...',
    'value' => $_searched_key,
    'name' => Constant::NAME_TEXT_SEARCH_KEY,
]);
echo '<br/>';
echo form_dropdown([
    'type' => 'select',
    'id' => 'id-select-searchtype',
    'class' => 'cl-select-large',
    'name' => Constant::NAME_SELECT_POST_TYPE,
        ], ${Constant::VDN_POST_TYPES_OPTIONS}, $_searched_selected);
echo '<br>';
echo form_input([
    'type' => 'submit',
    'class' => 'cl-btn-medium',
    'value' => 'Search'
]);
echo form_close();

echo '</div>';
