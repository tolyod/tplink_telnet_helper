<?php

function get_tplink_login_first_arr($password)
{
    return array(
        'login_prompt'  => 'User:',
        'login_success' => '',
        'login_fail'    => 'Login invalid',
        'login'         => 'admin',
        'password_prompt' => 'Password:',
        'password'      => $password,
        'prompt'        => ">",
        'debug'         => false,
    );
}

function get_tplink_login_enable_arr($enable_secret)
{
    return array(
        'login_prompt'  => '',
        'login_success' => '',  // reset from previous call
        'password'      => $enable_secret,
        'password_prompt' => 'Password:',
        'prompt'        => "#",
        'debug'         => true,
    );
}

function get_tplink_connect_init_arr($router)
{
    return array(
        'host' => $router,
        'telnet_bugs' => false,
        'debug' => false,
    );
}

function get_login_password()
{
    return "some login password";
}

function get_enable_password()
{
    return "some enable password";
}

