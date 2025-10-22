<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Checkout Notification Recipients
    |--------------------------------------------------------------------------
    |
    | This value contains a comma separated list of email addresses that will
    | receive a copy of the checkout confirmation. It can be useful to notify
    | the store administrators about each purchase.
    |
    */

    'notification_emails' => array_values(array_filter(array_map('trim', explode(',', env('CHECKOUT_NOTIFICATION_EMAILS', ''))))),

    /*
    |--------------------------------------------------------------------------
    | Checkout Confirmation Subject
    |--------------------------------------------------------------------------
    |
    | Subject used for the checkout confirmation email. It can be customised
    | through the CHECKOUT_CONFIRMATION_SUBJECT environment variable and will
    | fall back to a sensible default that includes the application name.
    |
    */

    'confirmation_subject' => env(
        'CHECKOUT_CONFIRMATION_SUBJECT',
        'Confirmación de pedido - ' . env('APP_NAME', 'Laravel')
    ),
];
