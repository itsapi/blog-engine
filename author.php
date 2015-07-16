<?php
	require_once('functions.php');
	connect();
	
	if(!isset($_GET['name'])){
		$list = true;
	}else{
		$list = false;
		$posts = view_authors($_GET['name']);
	}
?>

<!doctype html>

<html>
	<head>
		<title>
			<?php echo ($list) ? 'Authors' : 'Author: ' . $_GET['name']; ?>
		</title>
<?php
			head();
			if($list){
				echo "		<h2>All authors</h2>\n";
				$name_array = list_names();
				if($name_array != 0){
					echo "		<ul class=\"list\">\n";
					foreach($name_array as $name){
						echo '			<li><a href="author.php?name=' . $name . '">' . word_wrap($name) . "</a></li>\n";
					}
					echo "		</ul>\n";
				}else{
					echo "<p class=\"error\">Failed :(</p>";
				}
			}else{
		?>
		<h2>Posts by <?php echo $_GET['name']; ?></h2>
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