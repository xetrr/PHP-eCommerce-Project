<?php
function lang($phrase)
{
    static $lang = [
        'MESSAGE' => 'Welcome',
        'ADMIN' => 'Adminstrator',
    ];
    return $lang[$phrase];
}
