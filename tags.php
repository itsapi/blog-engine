<?php
	require_once('functions.php');
	connect();
	
	if(!isset($_GET['t'])){
		$list = true;
	}else{
		$list = false;
		$posts = view_tags($_GET['t']);
	}
?>

<!doctype html>

<html>
	<head>
		<title>
			<?php echo ($list) ? 'Tags' : 'Tag: ' . $_GET['t']; ?>
		</title>
<?php
			head();
			if($list){
				echo "		<h2>All tags</h2>\n";
				$tags_array = list_tags();
				if($tags_array != 0){
					echo "		<ul class=\"list\">\n";
					foreach($tags_array as $tag){
						echo '			<li><a href="tags.php?t=' . $tag . '">' . word_wrap($tag) . "</a></li>\n";
					}
					echo "		</ul>\n";
				}else{
					echo "<p class=\"error\">Failed :(</p>";
				}
			}else{
		?>
		<h2>Posts with tag <?php echo $_GET['t']; ?></h2>
		<?php
				if($posts != 0){
					echo $posts;
				}else{
					echo "<p class=\"error\">Failed :(</p>";
				}
			}
			footer();
		?>
	</body>
</html>