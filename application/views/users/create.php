<!doctype html>
<html>
	<head><meta	charset="utf-8">
		<title>Create User</title>
	</head>
	<body>
		<?php if ($this->session->flashdata('ok')): ?>
			<p class="text-success"><?= html_escape($this->session->flashdata('ok')) ?></p>
		<?php endif; ?>
		<?= form_open_multipart('users/create') ?>
			<div class="form-group">
				<label for="username">Username</label>
				<input type="text" name="username" id="username" class="form-control" value="<?= set_value('username') ?>">
				<?= form_error('username', '<div class="text-danger">', '</div>') ?>
			</div>
			<div class="form-group">
				<label for="email">Email</label>
				<input type="text" name="email" id="email" class="form-control" value="<?= set_value('email') ?>">
				<?= form_error('email', '<div class="text-danger">', '</div>') ?>
			</div>
			<div class="form-group">
				<label for="pass">Password</label>
				<input type="text" name="pass" id="pass" class="form-control">
				<?= form_error('pass', '<div class="text-danger">', '</div>') ?>
			</div>
			<div class="form-group">
				<label for="confirm_password">Confirm Password</label>
				<input type="text" name="confirm_password" id="confirm_password" class="form-control">
				<?= form_error('confirm_password', '<div class="text-danger">', '</div>') ?>
			</div>
			<div class="form-group">
				<label for="avatar">Avatar</label>
				<input type="file" name="avatar" id="avatar" class="form-control">
				<?= isset($upload_error) ? $upload_error : '' ?>
			</div>
			<button type="submit" class="btn btn-primary">Create User</button>
		<?= form_close() ?>
	</body>
</html>
