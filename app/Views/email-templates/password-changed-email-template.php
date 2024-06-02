<p>Dear <?= $mail_data['user']->name; ?></p>
<p>
  Your password on CI451test system was changed successfully. Here are your new login credentials:
  <br><br>
  <b>Login ID:</b> <?= $mail_data['user']->username; ?> or <?= $mail_data['user']->email; ?>
  <br>
  <b>Password:</b> <?= $mail_data['new_password']; ?>
</p>
<br><br>
Please, keep your credentials confidentials. Your username and password are your own credentials and you should never share anybody else.
<p>
  CI451test will not be liable for any misuse of your username or password
</p>
<br>
-------------------------------------------------
<p>
  This email was automatically sent by CI451test system. Do not reply it.
</p>
