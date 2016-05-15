<?php

include '../library/ignite.php';
$app = new ignite();

try {
    echo "<b><u>Select:</u></b><br/>" . $app->select('Users', 'EmailAddress', 'user@domain.com') . "<br/><br/>";
    echo "<b><u>Select (With Limit):</u></b><br/>" . $app->select('Users', 'EmailAddress', 'user@domain.com', 1) . "<br/><br/>";
    echo "<b><u>Multiple Select (And):</u></b><br/>" . $app->select_multiple('Users', array('EmailAddress' => 'user@domain.com', 'Password' => 'qwerty'), true) . "<br/><br/>";
    echo "<b><u>Multiple Select (And with Limit):</u></b><br/>" . $app->select_multiple('Users', array('EmailAddress' => 'user@domain.com', 'Password' => 'qwerty'), true, 1) . "<br/><br/>";
    echo "<b><u>Multiple Select (Or):</u></b><br/>" . $app->select_multiple('Users', array('EmailAddress' => 'user@domain.com', 'Password' => 'qwerty'), false) . "<br/><br/>";
    echo "<b><u>Multiple Select (Or with Limit):</u></b><br/>" . $app->select_multiple('Users', array('EmailAddress' => 'user@domain.com', 'Password' => 'qwerty'), false, 1) . "<br/><br/>";
    echo "<b><u>Insert:</u></b><br/>" . $app->insert('Users', array('EmailAddress' => 'user@domain.com', 'Password' => 'qwerty')) . "<br/><br/>";
    echo "<b><u>Insert Multiple:</u></b><br/>" . $app->insert_multiple('Users', array(array('EmailAddress' => 'user@domain.com', 'Password' => 'qwerty'), array('EmailAddress' => 'user2@domain.com', 'Password' => '12345'))) . "<br/><br/>";
} catch (Exception $e) {
    $app->handle_exception($e);
}