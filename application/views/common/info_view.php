<?php

echo '<br/>';
$info_list = [
    $this->session->userdata(Constant::SESSION_CURRENTUSER_NAME),
    '<span id="id-span-notibell">&#128276;</span><span id="id-span-noticount"></span>'
];
echo ul($info_list, ['id' => 'id-ul-infoview']);
echo '<br/>';
