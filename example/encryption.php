<?php

include '../library/ignite.php';
$app = new ignite();

try {
    $app->generate_keys();
    $input = "Ignite";
    $cipher = $app->encrypt_aes($input, $app->get_master_key(), true);
    $plain = $app->decrypt_aes($cipher, $app->get_master_key(), true);

    echo "<b><u>Plain Text:</u></b><br/>$input<br/><br/>";
    echo "<b><u>Cipher Text:</u></b><br/>$cipher<br/><br/>";
    echo "<b><u>Decrypted Text:</u></b><br/>$plain<br/><br/><br/><hr/><br/><br/>";

    echo "<b><u>Master Key:</u></b><br/>" . $app->get_master_key() . "<br/><br/><br/>";
    echo "<b><u>Private Key:</u></b><br/>" . $app->get_private_key() . "<br/><br/><br/>";
    echo "<b><u>Public Key:</u></b><br/>" . $app->get_public_key();
} catch (Exception $e) {
    $app->handle_exception($e);
}