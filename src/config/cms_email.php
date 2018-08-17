<?php
/**
 * Author: Xavier Au
 * Date: 27/7/2018
 * Time: 4:24 PM
 */

return [
    'template_folder'               => 'emails/layouts',
    'scheduler_time_offset_minutes' => -4,
    'username'                      => "apikey",
    'password'                      => "",
    'enable_workflow'               => true,
    'enable_import_job'             => false,
    'unsbuscribe_redirect_url'      => "/", // relative link
    'confirmed_redirect_url'        => "/", // relative link
    'send_email_queue'              => "default",
    'manual_recipient_status'       => "pending",
    'email_sender'                  => \Anacreation\Notification\Provider\ServiceProviders\SendGrid::class
];