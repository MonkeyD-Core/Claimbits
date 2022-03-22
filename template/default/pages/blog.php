<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
?> 
	<main role="main" class="container">
      <div class="row">
		<?php 
			if($is_online) {
				require(BASE_PATH.'/template/'.$config['theme'].'/common/sidebar.php');
			}
		?>
		<div class="<?=($is_online ? 'col-xl-9 col-lg-8 col-md-7' : 'col-12')?>">
			<div class="my-3 p-3 bg-white rounded box-shadow box-style">
				<div id="grey-box">
					<div class="title">
						<?=$lang['blog_7']?>
					</div>
					<div class="content">
						<?php
							if(isset($_GET['x']) && is_numeric($_GET['x']))
							{
								$id = $db->EscapeString($_GET['x']);
								$blog = $db->QueryFetchArray("SELECT a.*, b.username FROM blog a LEFT JOIN users b ON b.id = a.author WHERE a.id = '".$id."' LIMIT 1");
								if(empty($blog['id'])){
									redirect(GenerateURL('blog'));
								}
								
								if(isset($_GET['cd']) && is_numeric($_GET['cd']) && $data['admin'] == 1){
									$id = $db->EscapeString($_GET['cd']);
									$db->Query("DELETE FROM `blog_comments` WHERE `id`='".$id."'");
								}
								
								$errMessage = '';
								if($config['blog_comments'] == 0)
								{
									$errMessage = '<div class="alert alert-info" role="alert">'.$lang['blog_1'].'</div>';
								}
								elseif(!$is_online)
								{
									$errMessage = '<div class="alert alert-info" role="alert">'.$lang['blog_2'].'</div>';
								}
								else
								{
									if(isset($_POST['comment']) && !empty($data['id']))
									{
										$comment = $db->EscapeString($_POST['comment_text']);
										if(strlen($comment) < 20 || strlen($comment) > 255)
										{
											$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['blog_3'].'</div>';
										}
										elseif($db->QueryGetNumRows("SELECT id FROM `blog_comments` WHERE `author`='".$data['id']."' AND `timestamp`>'".(time()-60)."' LIMIT 1") > 0)
										{
											$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['blog_8'].'</div>';
										}
										else
										{
											$db->Query("INSERT INTO `blog_comments` (`bid`,`author`,`comment`,`timestamp`)VALUES('".$blog['id']."','".$data['id']."','".$comment."','".time()."')");
										}
									}
								}
								
								$page = (isset($_GET['y']) ? $_GET['y'] : '');
								$limit = 10;
								$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);
								$total_pages = $db->QueryGetNumRows("SELECT * FROM `blog_comments` WHERE `bid`='".$blog['id']."'");
								include(BASE_PATH.'/system/libs/Paginator.php');

								$urlPattern = GenerateURL('blog&x='.$blog['id'].'&y=(:num)');
								$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
								$paginator->setMaxPagesToShow(4);
								
								$comments = $db->QueryFetchArrayAll("SELECT a.id, a.author, a.comment, a.timestamp, b.username FROM blog_comments a LEFT JOIN users b ON b.id = a.author WHERE a.bid = '".$blog['id']."' ORDER BY a.timestamp DESC LIMIT ".$start.", ".$limit);

								$db->Query("UPDATE `blog` SET `views`=`views`+'1' WHERE `id`='".$blog['id']."'");
								$content = html_entity_decode($blog['content'], ENT_QUOTES);
						?>
							<div class="card mb-2">
							  <div class="card-header">
								<a href="<?=GenerateURL('blog&x='.$blog['id'])?>" class="text-dark"><b><?=truncate($blog['title'], 100)?></b></a>
							  </div>
							  <div class="card-body">
								<blockquote class="blockquote my-0 text-dark">
								  <p class="mt-0"><?=$content?></p>
								  <footer class="blockquote-footer"><?=$lang['blog_10']?>: <i><?=number_format($blog['views'])?></i> | <?=$lang['blog_4']?>: <i><?=number_format($total_pages)?></i> | <?=$lang['l_329']?>: <i><?=date('d M Y H:i', $blog['timestamp'])?></i></footer>
								</blockquote>
							  </div>
							</div>
						<?php
							echo $errMessage;
							if($is_online && $config['blog_comments'] == 1)
							{
								foreach($comments as $comm)
								{
						?>
							<div class="comments_wrap w-100">
								<div class="content_top"><?=$comm['username']?> <span class="float-right"><small><i><?=date('d M Y H:i', $comm['timestamp'])?></i><?=($data['admin'] == 1 ? ' - <a href="'.$config['secure_url'].'/?page=blog&x='.$blog['id'].'&cd='.$comm['id'].'" onclick="return confirm(\'Are you sure?\');" style="color:red">Delete</a>' : '')?></small></span></div>
								<div class="content_text">
									<?=nl2br(stripslashes(htmlspecialchars($comm['comment'])))?>
								</div>
							</div>
						<?php } ?>
						<?php if($total_pages > $limit){ ?>
							<nav aria-label="Page navigation example">
								<ul class="pagination justify-content-center mt-3">
									<?php 
										if ($paginator->getPrevUrl()) {
											echo '<li class="page-item"><a class="page-link" href="'.$paginator->getPrevUrl().'">&laquo; Previous</a></li>';
										} else {
											echo '<li class="page-item disabled"><a href="#" class="page-link">&laquo; Previous</a></li>';
										}

										foreach ($paginator->getPages() as $page) {
											if ($page['url']) {
												if($page['isCurrent']) {
													echo '<li class="page-item active"><a class="page-link">'.$page['num'].'</a></li>';
												} else {
													echo '<li class="page-item"><a class="page-link" href="'. $page['url'].'">'.$page['num'].'</a></li>';
												}
											} else {
												echo '<li class="page-item disabled"><a class="page-link" href="#">'.$page['num'].'</a></li>';
											}
										}

										if ($paginator->getNextUrl()) {
											echo '<li class="page-item"><a class="page-link" href="'.$paginator->getNextUrl().'">Next &raquo;</a></li>';
										}
									?>
								</ul>
							</nav>
						<?php } ?>
						<script type="text/javascript">
							var maxLength = 255;
							function charLimit(el) {
								if (el.value.length > maxLength) return false;
								return true;
							}
							function characterCount(el) {
								var charCount = document.getElementById('charCount');
								if (el.value.length > maxLength) el.value = el.value.substring(0,maxLength);
								if (charCount) charCount.innerHTML = maxLength - el.value.length;
								return true;
							}
						</script>
						<div class="blog_comment w-75"><div class="com_title"><?=$lang['blog_5']?></div>
							<form method="post">
								<textarea name="comment_text" class="form-control mb-1" rows="4" onKeyPress="return charLimit(this)" onKeyUp="return characterCount(this)" required="required"></textarea>
								<input type="submit" name="comment" class="btn btn-primary btn-sm ml-1 mb-1" value="<?=$lang['l_07']?>" />
								<span class="float-right mr-1 mt-1"><strong><span id="charCount">255</span></strong> <?=$lang['blog_9']?></span>
							</form>
						</div>
						<?php
								}
							}
							else
							{
								$page = (isset($_GET['y']) ? $_GET['y'] : '');
								$limit = 5;
								$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);
								$total_pages = $db->QueryGetNumRows("SELECT * FROM `blog`");
								include(BASE_PATH.'/system/libs/Paginator.php');

								$urlPattern = GenerateURL('blog&x=p&y=(:num)');
								$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
								$paginator->setMaxPagesToShow(4);

								$blogs = $db->QueryFetchArrayAll("SELECT a.*, b.username FROM blog a LEFT JOIN users b ON b.id = a.author ORDER BY a.timestamp DESC LIMIT ".$start.", ".$limit);
								if(!$blogs)
								{
									echo '<div class="alert alert-info" role="alert">'.$lang['l_121'].'</div>';
								}

								foreach($blogs as $blog)
								{
									$comments = $db->QueryGetNumRows("SELECT `id` FROM `blog_comments` WHERE `bid`='".$blog['id']."'");
						?>
							<div class="card mb-2">
							  <div class="card-header">
								<a href="<?=GenerateURL('blog&x='.$blog['id'])?>" class="text-dark"><b><?=truncate($blog['title'], 100)?></b></a>
							  </div>
							  <div class="card-body">
								<blockquote class="blockquote mb-0 text-dark">
								  <p class="mt-0"><?=truncate($blog['description'], 150)?> <a href="<?=GenerateURL('blog&x='.$blog['id'])?>" class="text-dark"><b><?=$lang['blog_6']?></b></a></p>
								  <footer class="blockquote-footer"><?=$lang['blog_10']?>: <i><?=number_format($blog['views'])?></i> | <?=$lang['blog_4']?>: <i><?=number_format($comments)?></i> | <?=$lang['l_329']?>: <i><?=date('d M Y H:i', $blog['timestamp'])?></i></footer>
								</blockquote>
							  </div>
							</div>
						<?php 
							}

							if($total_pages > $limit){ 
						?>
							<nav aria-label="Page navigation example">
								<ul class="pagination justify-content-center mt-3">
									<?php 
										if ($paginator->getPrevUrl()) {
											echo '<li class="page-item"><a class="page-link" href="'.$paginator->getPrevUrl().'">&laquo; Previous</a></li>';
										} else {
											echo '<li class="page-item disabled"><a href="#" class="page-link">&laquo; Previous</a></li>';
										}

										foreach ($paginator->getPages() as $page) {
											if ($page['url']) {
												if($page['isCurrent']) {
													echo '<li class="page-item active"><a class="page-link">'.$page['num'].'</a></li>';
												} else {
													echo '<li class="page-item"><a class="page-link" href="'. $page['url'].'">'.$page['num'].'</a></li>';
												}
											} else {
												echo '<li class="page-item disabled"><a class="page-link" href="#">'.$page['num'].'</a></li>';
											}
										}

										if ($paginator->getNextUrl()) {
											echo '<li class="page-item"><a class="page-link" href="'.$paginator->getNextUrl().'">Next &raquo;</a></li>';
										}
									?>
								</ul>
							</nav>
						<?php
								} 
							}
						?>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>