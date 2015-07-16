<?php
	function settings($server, $username, $password, $db, $blogs_table, $comments_table, $blog_name, $admin_pass, $break_tag, $stylesheet){
		$content = '<?php
	//settings:
		global $server, $username, $password, $db, $stylesheet, $blog_name, $admin_pass, $break_tag, $blogs_table, $comments_table;
		$server = "' . $server . '"; // MySQL server ip
		$username = "' . $username . '"; // MySQL username
		$password = "' . $password . '"; // MySQL password
		$db = "' . $db . '"; // MySQL Database
		$stylesheet = "' . $stylesheet . '"; // location of stylesheet
		$blog_name = "' . $blog_name . '"; // Name of blog
		$admin_pass = "' . md5($admin_pass) . '"; // MD5 hash of admin password
		$break_tag = "' . $break_tag . '"; // Tag used to specify end of blog post summary
		$blogs_table = "' . $blogs_table . '"; // Name of blogs table
		$comments_table = "' . $comments_table . '"; // Name of comments table
?>';
		file_put_contents('settings.php', $content);
	}
	function sql($server, $username, $password){
		$conn = mysql_connect($server, $username, $password);
		if (!$conn) {
			die('Connection error: ' . mysql_error());
		}
	}
	if(isset($_POST['server'])){
		$server = $_POST['server'];
		$username = $_POST['username'];
		$password = $_POST['password'];
		$db = $_POST['db'];
		$blogs_table = $_POST['blogs_table'];
		$comments_table = $_POST['comments_table'];
		$blog_name = $_POST['blog_name'];
		$admin_pass = $_POST['admin_pass'];
		$break_tag = $_POST['break_tag'];
		$stylesheet = $_POST['stylesheet'];
		settings($server, $username, $password, $db, $blogs_table, $comments_table, $blog_name, $admin_pass, $break_tag, $stylesheet);
		sql($server, $username, $password, $db, $blogs_table, $comments_table);
		echo "Please use this SQL code to set up the tables for the blog engine to use.</br></br><textarea>--
-- Table structure for table `".$blogs_table."`
--

DROP TABLE IF EXISTS `".$blogs_table."`;
CREATE TABLE IF NOT EXISTS `".$blogs_table."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `title` varchar(50) NOT NULL DEFAULT 'untitled',
  `author` varchar(50) NOT NULL,
  `tags` text NOT NULL,
  `content` varchar(10000) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Table structure for table `".$comments_table."`
--

DROP TABLE IF EXISTS `".$comments_table."`;
CREATE TABLE IF NOT EXISTS `".$comments_table."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT 'Anonymous',
  `comment` varchar(1000) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;
</textarea></br></br>";
		echo "Congratulations! Your Blog Engine is now set up. Please visit this page to get started. <a href=\"create.php\">Click here</a>";
	}
	echo <<<END
<!doctype html>
	
<html>
	<head>
		<title>Blog Engine Setup</title>
	</head>
	<body style="font-family: calibri, sans-serif;">
		<h1>Setup your new blog engine</h1>
		<p>Welcome to your new blog engine. To begin you need to enter a few details to customise your blog engine.</p>
		<form method="post" action="setup.php">
		
			<h3>MySQL server details</h3>
			
			<h5 style="margin: 0; padding: 0;">MySQL server ip:</h5>
			<input type="text" name="server">
			<h5 style="margin: 0; padding: 0;">MySQL username:</h5>
			<input type="text" name="username">
			<h5 style="margin: 0; padding: 0;">MySQL password:</h5>
			<input type="password" name="password">
			<h5 style="margin: 0; padding: 0;">MySQL database:</h5>
			<input type="text" name="db">
			<h5 style="margin: 0; padding: 0;">MySQL blogs table name:</h5>
			<input type="text" name="blogs_table">
			<h5 style="margin: 0; padding: 0;">MySQL comments table name:</h5>
			<input type="text" name="comments_table">
			
			<h3>Personalising your blog</h3>
			
			<h5 style="margin: 0; padding: 0;">Blog name:</h5>
			<input type="text" name="blog_name">
			<h5 style="margin: 0; padding: 0;">Admin password:</h5>
			<input type="password" name="admin_pass">
			<h5 style="margin: 0; padding: 0;">Tag used to specify end of blog post summary:</h5>
			<input type="text" name="break_tag">
			<h5 style="margin: 0; padding: 0;">Stylesheet location:</h5>
			<input type="text" name="stylesheet"><br>
			<input type="submit" value="submit">
			
		</form>
	</body>
</html>
END;
?>