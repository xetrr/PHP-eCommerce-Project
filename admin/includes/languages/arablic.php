<?php
function lang($phrase)
{
    static $lang = [
        'MESSAGE' => 'مرحباً يا',
        'ADMIN' => 'مدير',
    ];
    return $lang[$phrase];
}
