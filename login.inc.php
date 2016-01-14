		  <div class="demo-content mdl-color--white mdl-shadow--4dp content mdl-color-text--grey-800">
			<h3>输入教务系统信息</h3>
			<form action="./" method="POST">
				<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					<input class="mdl-textfield__input" type="text" id="username" name="username" value="<?php if(isset($_REQUEST['username'])){echo esc_attr($_REQUEST['username']);} ?>" autofocus />
					<label class="mdl-textfield__label" for="username">学号</label>
				</div>
				<div class="mdl-grid mdl-grid--no-spacing">
					<div class="mdl-cell mdl-cell--5-col">
						<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
							<input class="mdl-textfield__input" type="password" id="password" value="<?php if(isset($_REQUEST['password'])){echo esc_attr($_REQUEST['password']);} ?>" name="password" />
							<label class="mdl-textfield__label" for="password">密码</label>
						</div>
			  		</div>
				</div>
				<p>教务系统密码默认为身份证后六位大写。你的密码不会被保存。</p>
				<button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">提交</button>
				<a class="mdl-button mdl-js-button" href="?action=offline">离线测试</a>
				<input type="hidden" name="action" value="login" />
				<br />
				<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					<input class="mdl-textfield__input" type="text" id="year" name="year" value="<?php if(isset($_REQUEST['year'])){echo esc_attr($_REQUEST['year']);} ?>" />
					<label class="mdl-textfield__label" for="password">学年</label>
				</div>
				<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					<input class="mdl-textfield__input" type="number" id="term" name="term" value="<?php if(isset($_REQUEST['term'])){echo esc_attr($_REQUEST['term']);} ?>" />
					<label class="mdl-textfield__label" for="password">学期</label>
				</div>
			</form>
		  </div>