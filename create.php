<?php
	require_once('functions.php');
	connect();
	
	
	if (isset($_POST["title"])){
		$title = $_POST["title"];
		$author = $_POST["author"];
		$tags = $_POST["tags"];
		$content = $_POST["content"];
		$pass = $_POST["password"];
		$msg = create_blog($title, $author, $tags, $content, $pass);
	}
?>

<!doctype html>

<html>
	<head>
		<title>Add Post</title>
<?php head(); echo (isset($msg)) ? "<p class=\"error\">$msg</p>" : ''; ?>
		<form id="create" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<p>Title</p>
			<input type="text" name="title">
			<p>Author</p>
			<input type="text" name="author">
			<p>Tags</p>
			<input type="text" name="tags">
			<p>Blog Post</p>
			<textarea name="content" style="width:400px;height:200px;"><?php echo $GLOBALS['break_tag']; ?></textarea><br>
			<p>Password</p>
			<input type="password" name="password"><br>
			<input type="submit" value="submit">
		</form>
		<?php footer(); ?>
	</body>
</html>
