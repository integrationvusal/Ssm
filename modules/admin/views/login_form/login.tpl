<html>
	<head>
		<title>Log in</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet/less" type="text/css" href="{$static_url}/{$theme_folder}/less/login_form_stylesheets.less" />
		<script src="{$static_url}/{$theme_folder}/js/less.js"></script>
		<script>
			var errors = [];
			{if isset($errors)}errors = {$errors};{/if}
		</script>
	</head>
	<body>
		
		<div id="login-form-container">
			<form action="" method="post" id="login-form">
			<div id="app-builder-title-container">
				<div id="app-builder-title"><img src="{$static_url}/{$theme_folder}/img/logo.png" /></div>
			</div>
			<div id="login-title"><img src="{$static_url}/{$theme_folder}/img/login-form-title.png" /></div>
			<div class="field-container" id="errors-container">
				
			</div>
			<div class="field-container">
				<div class="field-icon">
					<img src="{$static_url}/{$theme_folder}/img/input-icon-user.png" />
				</div>
				<div class="field-input"><input type="text" name="email" value="{$messages.login_form.email_address}" default-value="{$messages.login_form.email_address}" id="email-field" /></div>
				<div class="clear"></div>
			</div>
			<div class="field-container">
				<div class="field-icon">
					<img src="{$static_url}/{$theme_folder}/img/password-icon-user.png" />
				</div>
				<div class="field-input"><input type="password" name="password" value="pswpsw" default-value="pswpsw" id="password-field" /></div>
				<div class="clear"></div>
			</div>
			{if isset($needCaptcha) && $needCaptcha}
			<div class="field-container">
				<div class="field-icon">
					<img src="{$app_url}/libs/kcaptcha/index.php" />
				</div>
				<div class="field-input"><input type="text" name="captcha" value="" id="capctha-field" /></div>
				<div class="clear"></div>
			</div>
			{/if}
			<div class="field-container">
				<div id="save-password-container">
					<div id="save-password-checkbox">
						<div class="checkbox-controller-container" val="1" key="savePassword">
							<div class="checkbox-controller-icon"></div>
							<div class="checkbox-controller-title">{$messages.login_form.save_password}</div>
							<div class="clear"></div>
							<input type="hidden" name="savePassword" value="" />
						</div>
					</div>
					<!--<div id="lost-password-link"><a href="#">{$messages.login_form.lost_password}</a></div>-->
				</div>
				<div id="login-button-container">
					<div class="button-std" onclick="document.getElementById('login-form').submit()">{$messages.login_form.login}</div>
				</div>
				<div class="clear"></div>
			</div>
			<input type="hidden" name="csrf_key" value="{$csrf_key}" />
			</form>
		</div>
		
		<!-- scripts -->
		<script src="{$static_url}/{$theme_folder}/js/jquery.js"></script>
		<script src="{$static_url}/{$theme_folder}/js/checkbox-controller.js"></script>
		<script src="{$static_url}/{$theme_folder}/js/login_actions.js"></script>
		<!-- scripts end -->
		
	</body>
</html>