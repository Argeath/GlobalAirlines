<div class="well">
	<div class="page-header">
		<h1>Dotacje</h1>
	</div>
	<div class="well col-md-6">
		Jeżeli podoba Ci się nasza gra oraz chciałbyś wesprzeć jej twórców dotacją, możesz to uczynić poprzez wpłatę w systemie PayPal.<br />
		
		<div class="well" style="width: 250px; margin: 0 auto;">
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="on0" value="Username">
				<input type="hidden" name="os0" value="<?= $username; ?>">
				<input type="hidden" name="hosted_button_id" value="37WKXAZYT8GWQ">
				<input type="image" src="https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — Płać wygodnie i bezpiecznie">
				<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div>
	</div>
	<div class="well col-md-3">
		<h4>Ostatnie Dotacje</h4>
		<hr>
		<table class="table table-striped">
		<thead>
		<tr>
			<th>Gracz</th>
			<th>Kwota</th>
		</tr>
		</thead>
		<tbody>
			
		</tbody>
		</table>
	</div>
	<div class="well col-md-3">
		<h4>TOP 10 Dotacji</h4>
		<hr>
		<table class="table table-striped">
		<thead>
		<tr>
			<th>Gracz</th>
			<th>Kwota</th>
		</tr>
		</thead>
		<tbody>
			
		</tbody>
		</table>
	</div>
	<div class="clearfix"></div>
</div>