<?php
function lang($phrase)
{
    static $lang = array(
        'MESSAGE' => 'Welcome',
        'ADMIN' => 'Adminstrator'
    );
    return $lang[$phrase];
}
