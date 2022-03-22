<?php
if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
$n1 = rand(1,9);
$n2 = rand(1,9);

$all_users = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE `activate`='0' AND `disabled`!='1'");
$vip_users = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE (`activate`='0' AND `disabled`!='1') AND `membership`>'0'");
$active_users = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE (`activate`='0' AND `disabled`!='1') AND (".time()."-`last_activity`) < 604800");
$inactive_users = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE (`activate`='0' AND `disabled`!='1') AND (".time()."-`last_activity`) > 604800");
?>
<script type="text/javascript">
	var start = 0;
	var limit = 50;
	var interval = 2;
	var eTotal = 0;
	var secode = '<?=($n1+$n2)?>';
	var captcha = 0;
	var receivers = 0;
	var title = '';
	var message = '';
	function send(){
		captcha = $("#captcha").val();
		limit = $("#batches").val();
		receivers = $("#receivers").val();
		interval = $("#interval").val();
		title = $("#title").val();
		message = $("#message").val();
		country = $("#country").val();
		start_process();
	}
	function start_process() {
		$("#Hint").html("<div class=\"alert information\">Please wait...</div>");
		$.ajax({
			type: "POST", 
			url: "sections/inc/sendemails.php", 
			data: "action=get&country="+country+"&receivers="+receivers, 
			success: function (a) {
				eTotal = a;
				send_mails();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert(textStatus);
			}
		});
	}
	function send_mails() {
		$.ajax({
			type: "POST", 
			url: "sections/inc/sendemails.php", 
			data: "action=send&start="+start+"&limit="+limit+"&title="+title+"&message="+message+"&captcha="+captcha+"&secode="+secode+"&country="+country+"&receivers="+receivers, 
			success: function (a) {
				if (a == 'DONE') {
					start = 0;
					$("#Hint").html("<div class=\"alert success\">"+eTotal+" emails were successfully sent!</div>");
				} else if (a == 'CAPTCHA_ERROR') {
					$("#Hint").html("<div class=\"alert error\"><strong>ERROR!</strong> Security answer is wrong!</div>");
				} else if (a == 'FIELDS_ERROR') {
					$("#Hint").html("<div class=\"alert error\"><strong>ERROR!</strong> Please complete all fields!</div>");
				} else if (!isNaN(a)) {
					start = a;
					$("#Hint").html("<div class=\"alert information\">Sending emails " + start + " / "+eTotal+". Please wait...</div>");
					b = setTimeout(function () {send_mails();}, (interval*1000));
				}
			}
		});
	}
	function getval(sel) {
		if(sel.value == '7') {
			$('#CountryBlock').show();
		} else {
			$('#CountryBlock').hide();
		}
    }
	function bbcode(code, tag){
		document.getElementById(code).value += tag; 
	}
</script>
<section id="content" class="container_12 clearfix">
	<div class="grid_12" id="Hint"></div>
	<div class="grid_8">
		<form name="news" action="javascript:send()" class="box">
			<div class="header">
				<h2>Send Newsletter</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>To</strong></label>
					<div><select style="width:100%" name="receivers" id="receivers" onchange="getval(this);"><option value="0">All users (<?=number_format($all_users['total'])?>)</option><option value="7">Selected Country</option><option value="5">Upgraded Users (<?=number_format($vip_users['total'])?>)</option><option value="1">Users active in past 7 days (<?=number_format($active_users['total'])?>)</option><option value="2">Users inactive in past 7 days (<?=number_format($inactive_users['total'])?>)</option><option value="6">Preview Email (will be sent to: <?=$config['site_email']?>)</option></select></div>
				</div>
				<div class="row" id="CountryBlock" style="display:none">
					<label><strong>Country</strong></label>
					<div><select style="width:100%" name="country" id="country">
						<?php
							$countries = $db->QueryFetchArrayAll("SELECT country,id FROM `list_countries` ORDER BY country ASC"); 
							echo '<option value="0"></option>';
							foreach($countries as $country){ 
								echo '<option value="'.$country['id'].'">'.$country['country'].'</option>';
							}
						?>
					</select></div>
				</div>
				<div class="row">
					<label><strong>Batches of emails</strong></label>
					<div><select style="width:100%" name="batches" id="batches"><option value="25">25</option><option value="50" selected="selected">50</option><option value="100">100</option><option value="150">150</option><option value="200">200</option></select></div>
				</div>
				<div class="row">
					<label><strong>Time between batches</strong></label>
					<div><select style="width:100%" name="interval" id="interval"><option value="2">2 seconds</option><option value="5">5 seconds</option><option value="10">10 seconds</option><option value="15">15 seconds</option><option value="20">20 seconds</option></select></div>
				</div>
				<div class="row">
					<label><strong>Subject</strong></label>
					<div><input style="width:100%" type="text" name="title" id="title" maxlength="60" value="<?=(isset($_POST['title']) ? $_POST['title'] : '')?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Message</strong></label>
					<div><br />
						<span style="margin:0">
							<input type="button" value="Bold" name="bold" onclick="bbcode('message', '[b][/b]')" style="display:inline-block;" />
							<input type="button" value="Underline" name="underline" onclick="bbcode('message', '[u][/u]')" style="display:inline-block;" />
							<input type="button" value="Italic" name="italic" onclick="bbcode('message', '[i][/i]')" style="display:inline-block;" />
							<input type="button" value="Link" name="link" onclick="bbcode('message', '[url][/url]')" style="50px;display:inline-block;" />
							<input type="button" value="Code" name="code" onclick="bbcode('message', '[code][/code]')" style="display:inline-block;" />
							<input type="button" value="Center" name="center" onclick="bbcode('message', '[center][/center]')" style="display:inline-block;" />
							<input type="button" value="Image" name="image" onclick="bbcode('message', '[img][/img]')" style="display:inline-block;" />
						</span> 
						<textarea style="width:100%" name="message" id="message" rows="8" required="required"><?=(isset($_POST['message']) ? $_POST['message'] : '')?></textarea>
					</div>
				</div>
				<div class="row">
					<label><strong><?=($n1." + ".$n2." = ?")?></strong></label>
					<div><input style="width:200px" type="text" name="captcha" id="captcha" required="required" /></div>
				</div>										
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Send" />
				</div>
			</div>
		</form>
	</div>
	<div class="grid_4">
		<div class="box">
			<div class="header">
				<h2>Info</h2>
			</div>
            <div class="content"><br />
				<p>Here you can send emails to all members registered on this website!</p>
				<p>If you want to include usernames in email, you can use -USER-<br />
				E.g: <b>Hello -USER-!</b> will be <b>Hello Username!</b></p>
				<p>Also, all emails are sent in HTML format, so you can use following BB Code tags:
				<ul>
					<li>Bold: <br /><span style="margin:35px">[b]text[/b] => <b>text</b></span></li>
					<li>Underline: <br /><span style="margin:35px">[u]text[/u] => <u>text</u></span></li>
					<li>Italic: <br /><span style="margin:35px">[i]text[/i] => <i>text</i></span></li>
					<li>Code: <br /><span style="margin:35px">[code]text[/code] => <code>text</code></span></li>
					<li>Link: <br /><span style="margin:35px">[url=http://url.com]text[/url] => <a href="#">text</a></span></li>
				</ul>
				</p>
            </div>
		</div>
	</div>
</section>