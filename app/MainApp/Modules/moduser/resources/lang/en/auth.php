<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    //----------
    'registersuccess' => 'Registrasi berhasil. Silahkan cek email Anda untuk melakukan verifikasi email.',
    'registerfailed' => 'Registrasi gagal. Error : :error ',
    'emailverify_fail_mailnotfound' => 'Verifikasi email gagal. Error : Email tidak ditemukan',
    'emailverify_fail_verificationcodeinvalid' => 'Verifikasi email gagal. Error : Kode verifikasi invalid',
    'phoneverify_fail_phonenotfound' => 'Verifikasi nomor telepon gagal. Error : Nomor telepon tidak ditemukan',
    'phoneverify_fail_verificationcodeinvalid' => 'Verifikasi nomor telepon gagal. Error : Kode verifikasi invalid',
    'resetpassword_fail_mailnotfound' => 'Reset Password Gagal. Error : Email tidak ditemukan',
    'resetpassword_fail_verificationcodeinvalid' => 'Reset Password Gagal. Error : Kode verifikasi invalid',
    //----------
    'logout' => 'Log Out',
    //text lang di halaman forgot password
    'forgotpassword' => [
        'title' => 'Reset Your Password',
        'description' => 'Enter your email address and we will send you a link to reset your password.',
        'emailcaption' => 'Enter your email address',
        'button' => 'Send email address',
        'back_to_login' => 'Back to Login'
    ],
    //text lang di halaman loting
    'login' => [
        'title' => 'Login to Your Account',
        'usernamecaption' => 'Username/Email',
        'passwordcaption' => 'Password',
        'forgotpassword' => 'Forgot Password ?',
        'remember_me' => 'Remeber Me',
        'sigincaption' => 'Sign In',
        'dont_have_an_account'=>'Don\'t have an account yet?',
        'signupcaption' => 'Sign Up'
    ],

];
