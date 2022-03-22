<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	if(isset($_GET['del']) && is_numeric($_GET['del'])){$del = $db->EscapeString($_GET['del']); $db->Query("DELETE FROM `faq` WHERE `id`='".$del."'");}
	$mesaj = '';
	if(isset($_GET['add'])){
		if(isset($_POST['addfaq']) && $_POST['question'] != '' && $_POST['answer'] != ''){
			$question = $db->EscapeString($_POST['question']);
			$answer = $db->EscapeString($_POST['answer']);
			$db->Query("INSERT INTO `faq` (`question`,`answer`)VALUES('".$question."','".$answer."')");
			$mesaj = '<div class="alert success"><span class="icon"></span>FAQ was successfuly added!</div>';
		}
	}elseif(isset($_GET['edit'])){
		$id = $db->EscapeString($_GET['edit']);
		$edit = $db->QueryFetchArray("SELECT * FROM `faq` WHERE `id`='".$id."'");
	}

	if(isset($_POST['submit'])){
		$question = $db->EscapeString($_POST['question']);
		$answer = $db->EscapeString($_POST['answer']);

		$db->Query("UPDATE `faq` SET `question`='".$question."', `answer`='".$answer."' WHERE `id`='".$id."'");
		$mesaj = '<div class="alert success"><span class="icon"></span><strong>Success!</stront> FAQ was successfuly edited!</div>';
	}
	if(isset($_GET['add'])){
?>
<section id="content" class="container_12 clearfix"><?=$mesaj?>
	<div class="grid_8">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Add FAQ</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Question</strong></label>
					<div><input type="text" name="question" value="" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Answer</strong></label>
					<div><textarea name="answer" style="width:450px;height:60px" required="required"></textarea></div>
				</div>										
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="addfaq" />
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
				<p>You can use following BB Code tags:
				<ul>
					<li>[b]text[/b] => <b>text</b></li>
					<li>[u]text[/u] => <u>text</u></li>
					<li>[i]text[/i] => <i>text</i></li>
					<li>[code]text[/code] => <code>text</code></li>
					<li>[url=http://url.com]text[/url] => <a href="#">text</a></li>
				</ul>
				</p>
            </div>
		</div>
	</div>
</section>
<?php }elseif(isset($_GET['edit'])){?>
<section id="content" class="container_12 clearfix"><?=$mesaj?>
	<div class="grid_8">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Add FAQ</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Question</strong></label>
					<div><input type="text" name="question" value="<?=(isset($_POST['question']) ? $_POST['question'] : $edit['question'])?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Answer</strong></label>
					<div><textarea name="answer" required="required"><?=(isset($_POST['answer']) ? $_POST['answer'] : $edit['answer'])?></textarea></div>
				</div>										
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
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
				<p>You can use following BB Code tags:
				<ul>
					<li>[b]text[/b] => <b>text</b></li>
					<li>[u]text[/u] => <u>text</u></li>
					<li>[i]text[/i] => <i>text</i></li>
					<li>[code]text[/code] => <code>text</code></li>
					<li>[url=http://url.com]text[/url] => <a href="#">text</a></li>
				</ul>
				</p>
            </div>
		</div>
	</div>
</section>
<?php }else{?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">FAQ</h1>
	<div class="grid_12">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th width="25">ID</th>
						<th>Question</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$faqs = $db->QueryFetchArrayAll("SELECT id,question FROM `faq` ORDER BY `id` ASC");
						
						if(count($faqs) == 0) {
							echo '<tr><td colspan="3" style="text-align: center">Nothing here yet!</td></tr>';
						}
						
						foreach($faqs as $faq){
					?>	
					<tr>
						<td><?=$faq['id']?></td>
						<td><?=$faq['question']?></td>
						<td class="center">
							<a href="index.php?x=faq&edit=<?=$faq['id']?>" class="button small grey tooltip" title="Accept"><i class="icon-edit"></i></a>
							<a href="index.php?x=faq&del=<?=$faq['id']?>" class="button small grey tooltip" title="Reject"><i class="icon-remove"></i></a>
						</td>
					</tr>
					<?php }?>
				</tbody>
			</table>
			<?php } ?>
		</div>
	</div>
</section>