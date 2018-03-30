<?php

/**
 * Messages for the application
 */
return [

    /** Error Messages**/

    'errors'    =>  [
        'jwt'   =>  [
            'token_not_found'   =>  'Token not provided.',
            'token_expired'     =>  'Token expired.',
            'token_invalid'     =>  'Token invalid.',
            'token_mismatch'    =>  'Token mismatch'
        ],

        'users' =>  [
            'not_found'         =>  'No user found.'
        ],

        'response'  =>  [
            'invalid_user'          =>  'Invalid email or password.',
            'forget_password'       =>  [
                'user_not_found'    =>  'Sorry! No user has been found using this email address.',
            ],
            'reset_password'        =>  [
                'token_expired'     =>  "Sorry, This token has expired! You need to reset your password within 30 minutes of request."
            ]
        ]
    ],



    /** Success Messages**/

    'success'   =>  [
        'response'  =>  [
            'logout_success'    =>  'User signed out successfully.',
            'forget_password'   =>  [
                'email_sent'    =>  'Confirmation mail send to your email address.'
            ],
            'reset_password'    =>  [

            ]
        ]
    ],



    /** Email Description**/

    'email'     =>  [
        'app_name'      =>  'ChatVago',
        'subject'       =>  'ChatVago Reset Password Link'
    ]
];