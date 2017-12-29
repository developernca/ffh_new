<?php

echo '<div id="id-div-navcontainer">'; // begin navication container div

echo '<p onclick="toggleNav();">';
echo img('usr/arrow.png');
echo '</p>';

$nav_list = [
    anchor(base_url() . 'index.php/Home/signout/', 'Sign Out'),
    anchor(base_url() . 'index.php/MyPost/', 'My Posts'),
    anchor(base_url() . 'index.php/Home/', 'Home'),
    anchor(base_url() . 'index.php/General/', 'General')
];
echo ul($nav_list, ['id' => 'id-ul-navlinkcontainer']);

echo '</div>'; // end navication container div