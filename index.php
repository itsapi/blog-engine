<?php
	require_once('functions.php');
	connect();
?>

<!doctype html>

<html>
	<head>
		<title><?php echo $GLOBALS['blog_name'] ?></title>
<?php
			head();
			
			echo "		<h2>Recent Blog Posts</h2>\n";
			$recent_blogs_func = recent_blog(3);
			if($recent_blogs_func != 0){
				echo "		<ul class=\"posts\">\n";
				foreach ($recent_blogs_func as $row){
					$date = getdate($row["date"]);
					$title = word_wrap($row['title']);
					$content = word_wrap($row['content']);
					$author = word_wrap($row['author']);
					$day = $date['weekday'];
					$mday = $date['mday'];
					$month = $date['month'];
					$year = $date['year'];
					$comment_no = $row[0];
					echo <<<END
				<li>
					<h3><a href="view.php?blog=$title">$title</a></h3>
					<h5>$day $mday $month $year</h5>
					<p>$content...</p>
					<h5><a href="author.php?name=$author">$author</a></h5>
					<h5>$comment_no comments</h5>
				</li>

END;
					echo "		</ul>\n";
				}
			}else{
				echo '<p class="error">Failed :(</p>';
			}
			echo "		<a id=\"all_posts\" href=\"view.php\">All posts...</a>";
			footer();
		?>
	</body>
</html>
