<?php
if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
if(isset($_GET['del']) && is_numeric($_GET['del']))
{
	$del = $db->EscapeString($_GET['del']);
	$db->Query("DELETE FROM `blog` WHERE `id`='".$del."'");
	$db->Query("DELETE FROM `blog_comments` WHERE `bid`='".$del."'");
}

$message = '';
if(isset($_GET['add']))
{
	if(isset($_POST['addblog'])){
		$title = $db->EscapeString($_POST['title']);
		$description = $db->EscapeString($_POST['description']);
		$content = htmlentities(trim($_POST['content']), ENT_QUOTES);

		if(empty($content) || empty($title) || empty($description)){
			$message = '<div class="alert error"><span class="icon"></span><strong>Error!</stront> Please complete all fields!</div>';
		}else{
			$db->Query("INSERT INTO `blog` (`author`,`title`,`description`,`content`,`timestamp`)VALUES('".$data['id']."','".$title."','".$description."','".$content."','".time()."')");
			$message = '<div class="alert success"><span class="icon"></span> Blog was successfuly added!</div>';
		}
	}
}
elseif(isset($_GET['edit']))
{
	$id = $db->EscapeString($_GET['edit']);
	$edit = $db->QueryFetchArray("SELECT * FROM `blog` WHERE `id`='".$id."' LIMIT 1");

	if(isset($_POST['submit']))
	{
		$title = $db->EscapeString($_POST['title']);
		$description = $db->EscapeString($_POST['description']);
		$content = htmlentities(trim($_POST['content']), ENT_QUOTES);

		if(empty($content) || empty($title) || empty($description)){
			$message = '<div class="alert error"><span class="icon"></span><strong>Error!</stront> Please complete all fields!</div>';
		}else{
			$db->Query("UPDATE `blog` SET `content`='".$content."', `description`='".$description."', `title`='".$title."' WHERE `id`='".$id."'");
			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</stront> Blog was successfuly edited!</div>';
		}
	}
}
if(isset($_GET['add'])){
?>
<script type="text/javascript">
	function bbcode(code, tag){
		document.getElementById(code).value += tag; 
	}
</script>
<section id="content" class="container_12 clearfix"><?=$message?>
	<div class="grid_12">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Add Blog</h2>
			</div>
				<div class="content">
					<div class="row">
						<label><strong>Title</strong><small>Maximum 75 characters</small></label>
						<div><input type="text" name="title" maxlength="75" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Short Description</strong><small>Maximum 255 characters</small></label>
						<div><textarea name="description" id="description" required="required"></textarea></div>
					</div>
					<div class="row">
						<label><strong>Content</strong></label>
						<div><textarea name="content" id="message" class="editor" required="required"></textarea></div>
					</div>										
                </div>
				<div class="actions">
					<div class="right">
						<input type="submit" value="Submit" name="addblog" />
					</div>
				</div>
		</form>
	</div>
</section>
<?php 
	}
	elseif(isset($_GET['edit']))
	{
?>
<section id="content" class="container_12 clearfix"><?=$message?>
	<div class="grid_12">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Edit Blog</h2>
			</div>
				<div class="content">
					<div class="row">
						<label><strong>Title</strong><small>Maximum 75 characters</small></label>
						<div><input type="text" name="title" value="<?=(isset($_POST['title']) ? $_POST['title'] : $edit['title'])?>" maxlength="75" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Short Description</strong><small>Maximum 255 characters</small></label>
						<div><textarea name="description" id="description" required="required"><?=(isset($_POST['description']) ? $_POST['description'] : $edit['description'])?></textarea></div>
					</div>
					<div class="row">
						<label><strong>Content</strong></label>
						<div><textarea name="content" id="content" class="editor" required="required"><?=(isset($_POST['content']) ? $_POST['content'] : html_entity_decode($edit['content'], ENT_QUOTES))?></textarea></div>
					</div>										
                </div>
				<div class="actions">
					<div class="right">
						<input type="submit" value="Submit" name="submit" />
					</div>
				</div>
		</form>
	</div>
</section>
<?php 
	}else{
?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">Blog</h1>
	<div class="grid_12">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th width="25">#</th>
						<th>Title</th>
						<th>Description</th>
						<th>Views</th>
						<th>Comments</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$page = (isset($_GET['p']) ? $_GET['p'] : 0);
						$limit = 20;
						$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);

						$total_pages = $db->QueryGetNumRows("SELECT id FROM blog ");
						include('../system/libs/Paginator.php');
						
						$urlPattern = GetHref('p=(:num)');
						$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
						$paginator->setMaxPagesToShow(5);

						if(empty($total_pages))
						{
							echo '<tr><td colspan="6"><center>There is nothing here yet!</center></td></tr>';
						}
						
						$blogs = $db->QueryFetchArrayAll("SELECT a.id,a.title,a.description,a.content,a.views FROM blog a ORDER BY a.id DESC LIMIT ".$start.",".$limit."");
						foreach($blogs as $blog)
						{
							$comments = $db->QueryGetNumRows("SELECT `id` FROM `blog_comments` WHERE `bid`='".$blog['id']."'");
					?>	
					<tr>
						<td><?=$blog['id']?></td>
						<td><a href="<?=GenerateURL('blog&x='.$blog['id'])?>" target="_blank"><?=truncate($blog['title'], 50)?></a></td>
						<td><?=truncate($blog['description'], 50)?></td>
						<td><?=number_format($blog['views'])?></td>
						<td><?=number_format($comments)?></td>
						<td class="center">
							<a href="index.php?x=blog&edit=<?=$blog['id']?>" class="button small grey tooltip"><i class="icon-edit"></i></a>
							<a href="index.php?x=blog&del=<?=$blog['id']?>" onclick="return confirm('You sure you want to delete this blog?');" class="button small grey tooltip"><i class="icon-remove"></i></a>
						</td>
					</tr>
					<?php }?>
				</tbody>
			</table>
			<?php if($total_pages > $limit){ ?>
			<div class="dataTables_wrapper">
				<div class="footer">
					<div class="dataTables_paginate paging_full_numbers">
					<?php 
						if ($paginator->getPrevUrl()) {
							echo '<a class="first paginate_button" href="'.$paginator->getPrevUrl().'">&laquo; Previous</a></li>';
						} else {
							echo '<a class="first paginate_button">&laquo; Previous</a>';
						}
						
						echo '<span>';

						foreach ($paginator->getPages() as $page) {
							if ($page['url']) {
								if($page['isCurrent']) {
									echo '<a class="paginate_active">'.$page['num'].'</a>';
								} else {
									echo '<a class="paginate_button" href="'. $page['url'].'">'.$page['num'].'</a>';
								}
							} else {
								echo '<a class="paginate_active">'.$page['num'].'</a>';
							}
						}
						
						echo '<span>';
						
						if ($paginator->getNextUrl()) {
							echo '<a class="last paginate_button" href="'.$paginator->getNextUrl().'">Next &raquo;</a></li>';
						}
					?>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</section>
<?php } ?>