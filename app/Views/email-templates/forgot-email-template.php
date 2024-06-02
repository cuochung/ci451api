<p>Dear <?= $mail_data['user']->name; ?></p>
<p>
  We are received a request to reset password for CI451test account associated with 
  <i><?= $mail_data['user']->email; ?></i>.
  You can reset your password by clicking the button below;
  <br><br>
  <a href="<?= $mail_data['actionLink'] ?>" target="_blank">Reset password</a>
  <br><br>
  <b>NB:</b> This link will still valid within 15 minutes.
  If you did not request for password reset ,please ignore this email.
</p>