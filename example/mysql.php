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
    echo "<b><u>Update:</u></b><br/>" . $app->update('Users', array('Verified' => '1'), 'EmailAddress', 'user@domain.com') . "<br/><br/>";
    echo "<b><u>Update Multiple (And):</u></b><br/>" . $app->update_multiple('Users', array('Verified' => '1'), array('EmailAddress' => 'user@domain.com', 'Verified' => '0'), true) . "<br/><br/>";
    echo "<b><u>Update Multiple (Or):</u></b><br/>" . $app->update_multiple('Users', array('Verified' => '1'), array('EmailAddress' => 'user@domain.com', 'Verified' => '0'), false) . "<br/><br/>";
    echo "<b><u>Add primary key</u></b><br/>" . $app->add_primary_key('Users', 'ipkUserId') . "<br/><br/>";
    echo "<b><u>Remove primary key</u></b><br/>" . $app->remove_primary_key('Users') . "<br/><br/>";
} catch (Exception $e) {
    $app->handle_exception($e);
}