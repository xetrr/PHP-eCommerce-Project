<?php
function lang($phrase)
{
    static $lang = array(
        'MESSAGE' => 'مرحباً يا',
        'ADMIN' => 'مدير'
    );
    return $lang[$phrase];
}
