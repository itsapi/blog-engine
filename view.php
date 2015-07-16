<?php
	require_once('functions.php');
	connect();
	
	if(!isset($_GET['blog'])){
		$list = true;
	}else{
		$list = false;
		$row = view_blog($_GET['blog']);
		if(!is_array($row)){
			header('location:404.php');
		}
		if(isset($_POST['name'])){
			$comment = write_comment($_POST['name'], $_POST['comment'], $row['id']);
		}
	}
?>
<!doctype html>

<html>
	<head>
		<title><?php echo ($list) ? 'All Posts' : 'Blog: ' . $row['title']; ?></title>
<?php
			head();
			if(!$list){
?>
		<article class="posts">
			<h2><?php echo stripslashes(word_wrap($row['title'])); ?></h2>
			<?php
				$date = getdate($row['date']);
				echo '<h4>' . $date['weekday'] . ' ' . $date['mday'] . ' ' . $date['month'] . ' ' . $date['year'] . "</h4>\n";
			?>
			<p><?php echo stripslashes(word_wrap($row['content'])); ?></p>
			<h4>By <a href="author.php?name=<?php echo stripslashes($row['author']); ?>"><?php echo stripslashes(word_wrap($row['author'])); ?></a></h4>
			<p><?php echo stripslashes(tags($row['tags'])); ?></p>
		</article>
		
		<?php echo (isset($comment)) ? "<p class=\"error\">$comment</p>" : ''; ?>
		
		<form id="comment" method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?blog=' . $row['title'] . '#comment'; ?>">
			Your Name<br>
			<input type="text" name="name"><br>
			Comment<br>
			<textarea name="comment"></textarea><br>
			<input type="submit" value="submit">
		</form>
		
		<?php
				$comments = view_comments($row['id']);
				if ($comments != false){
					echo "<ul class=\"posts\">\n";
					foreach($comments as $item){
						$comment_date = getdate($item['date']);
						$name = stripslashes(word_wrap($item['name']));
						$day = stripslashes($comment_date['weekday']);
						$mday = stripslashes($comment_date['mday']);
						$month = stripslashes($comment_date['month']);
						$year = stripslashes($comment_date['year']);
						$comment = stripslashes(word_wrap($item['comment']));
						echo <<<END
			<li>
				<h4>$name - $day $mday $month $year</h4>
				<p>$comment</p>
			</li>

END;
					}
					echo "</ul>\n";
				}
			}else{
				if (list_blogs() !=0){
					echo "		<table class=\"posts\">\n";
					foreach (list_blogs() as $row){
						$date = getdate($row['date']);
						$title = word_wrap($row['title']);
						$day = $date['weekday'];
						$mday = $date['mday'];
						$month = $date['month'];
						$year = $date['year'];
						$author = word_wrap($row['author']);
						$comment_no = $row[0];
						echo <<<END
				<tr>
					<td><h3><a href="view.php?blog=$title">$title</a></h3></td>
					<td><h5>$comment_no comments</h5></td>
					<td><h5>$day $mday $month $year</h5></td>
					<td><h5><a href="author.php?name=$author">$author</a></h5></td>
				</tr>

END;
					}
					echo "		</table>\n";
				}else{
					echo "<p class=\"error\">Failed :(</p>";
				}
			}
			footer();
		?>
		
	</body>
</html>