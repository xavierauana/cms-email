<?php
/**
 * Author: Xavier Au
 * Date: 27/7/2018
 * Time: 4:24 PM
 */

return [
    'template_folder'               => 'emails/layouts',
    'scheduler_time_offset_minutes' => 0,
    'username'                      => "apikey",
    'password'                      => env("API_KEY"),
    'enable_workflow'               => true,
    'enable_import_job'             => false,
    'unsbuscribe_redirect_url'      => "/", // relative link
    'confirmed_redirect_url'        => "/", // relative link
    'send_email_queue'              => "default",
    'long_run_connection'           => "long-redis",
    'manual_recipient_status'       => "pending",
    'sand_box'                      => env("CMS_EMAIL_SANDBOX", true),
    'email_sender'                  => \Anacreation\Notification\Provider\ServiceProviders\SendGrid::class
];