<?php

include '../library/ignite.php';
$app = new ignite();

try {
    $app->connect('host', 'user', 'password', 'dbname');
    $email = $app->get_secure('email', true);
    $password = $app->get_secure('password', true);

    $app->db_contains_unique('Users', array('EmailAddress' => $email), true, 'Email address is already registered');
    $app->generate_keys();
    $app->query($app->insert('Users', array('EmailAddress' => $email, 'Password' => $app->encrypt_aes($password, $app->get_master_key(), true))));

    echo json_encode(array(
        'error' => false,
        'message' => 'Thank you for registering your account'
    ));
} catch (Exception $e) {
    $app->handle_exception($e);
}