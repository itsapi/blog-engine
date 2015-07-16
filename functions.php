<?php
	include('settings.php');
	function connect(){
		mysql_connect($GLOBALS['server'], $GLOBALS['username'], $GLOBALS['password']);
		mysql_select_db($GLOBALS['db']);
	}
	function head(){
		$stylesheet = $GLOBALS['stylesheet'];
		$blog_name = $GLOBALS['blog_name'];
		echo <<<END
		<link href="$stylesheet" rel="stylesheet" type="text/css">
	</head>
	<body>
		<h1><a href="index.php">$blog_name</a></h1>
		<nav>
			<ul>
				<li><a href="author.php">Authors</a></li>
				<li><a href="tags.php">Tags</a></li>
			</ul>
		</nav>








END;
	}
	function footer(){
		$tag_cloud = tag_cloud(10);
		$blog_name = $GLOBALS['blog_name'];
		$date = date('Y');
		echo <<<END




		<section id="sidebar">
			<h3>Subscribe to our posts:</h3>
			<form>
				<input type="email" placeholder="john@example.com">
				<input type="submit" value="Submit">
			</form>
			<h3>Popular tags:</h3>
			$tag_cloud
		</section>
		<footer>Copyright &copy; $blog_name $date</footer>




END;
	}
	function q($query,$assoc=1) {
		$r = @mysql_query($query);
		if( mysql_errno() ) {
			$error = 'MYSQL ERROR #'.mysql_errno().' : <small>' . mysql_error(). '</small><br><VAR>$query</VAR>';
			echo($error); return FALSE;
		} 
		if( strtolower(substr($query,0,6)) != 'select' ) return array(mysql_affected_rows(),mysql_insert_id());
		$count = @mysql_num_rows($r);
		if( !$count ) return 0;
		if( $count == 1 ) {
			if( $assoc ) $f = mysql_fetch_assoc($r);
			else $f = mysql_fetch_row($r);
			mysql_free_result($r);
			if( count($f) == 1 ) {
				list($key) = array_keys($f);    
				return $f[$key];
			} else {
				$all = array();
				$all[] = $f;
				return $all;
			}
		} else {
			$all = array();
			for( $i = 0; $i < $count; $i++ ) {
				if( $assoc ) $f = mysql_fetch_assoc($r);
				else $f = mysql_fetch_row($r);
				$all[] = $f;
			}
			mysql_free_result($r);
			return $all;
		}
	}
	function password($pass){
		if(md5($pass) == $GLOBALS['admin_pass']){
			return true;
		}else{
			return false;
		}
	}
	function word_wrap($text){
		$array = explode(" ", $text); // breaking down each word in the $comment into arrays using explode().




		// loop to enable me to run each word one by one through word_wrap() and add a space into the word if it is longer than 15 characters.
		$str = '';
		for ($i = 0, $array_num = count($array); $i < $array_num; $i++) {
			$word_split = wordwrap($array[$i], 15, " ", true);
			$str = "$str$word_split ";
		}
		
		return $str;
	}
	function recent_blog($no){
		$blogs_table = $GLOBALS['blogs_table'];
		$result = q("SELECT id, date, title, author, content FROM $blogs_table");
		$dates = array();
		$i = 0;
		if ($result == 0){
			return 0;
		}else{
			foreach($result as $row){
				$dates[$row['id']] = $row['date'];
				$comment_no = count_comments($row['id']);
				array_push($result[$i], $comment_no);
				$i++;
			}
			arsort($dates);
			$ids = array_keys($dates);
			$content = array();
			
			if(sizeof($ids) == 1){
				foreach($result as $row){
					if($row['id'] == $ids[0]){
						$content[] = $row;
					}
				}
			}else{
				foreach($ids as $id){
					foreach($result as $row){
						if($id == $row['id']){
							$content[] = $row;
						}
					}
				}
			}
			$posts = array();
			if(sizeof($content) > $no){
				for($i = 0; $i<$no; $i++){
					$posts[] = $content[$i];
				}
			}else{
				foreach($content as $post){
					$posts[] = $post;
				}
			}
			$i = 0;
			foreach($posts as $post){
				$parts = explode($GLOBALS['break_tag'], $post['content']);
				if(sizeof($parts) == 1){
					$parts[0] = substr($parts[0], 0, 100);
				}
				$posts[$i]['content'] = $parts[0];
				$i++;
			}
			return $posts;
		}
	}
	function create_blog($title, $author, $tags, $content, $pass){
	
		if (password($pass)){
			$date = $_SERVER['REQUEST_TIME'];
			$title = addslashes($title);
			$author = addslashes($author);
			$tags = addslashes(mb_strtolower($tags));
			$content = addslashes($content);
			$blogs_table = $GLOBALS['blogs_table'];
			if (mysql_query("INSERT INTO $blogs_table (id, date, title, author, tags, content) VALUES (NULL, '$date', '$title', '$author', '$tags', '$content')")) {
				return 'Success: <a href="view.php?blog=' . $title . '">' . $title . '</a>';
			} else {
				return "Failed :(";
			}
			
		} else {
			return 'Incorrect password.';
		}
	}
	function view_blog($title){
		$blogs_table = $GLOBALS['blogs_table'];
		$result = q("SELECT id, date, title, author, tags, content FROM $blogs_table WHERE title = '$title'");
		if ($result) {
			$result[0]['content'] = explode($GLOBALS['break_tag'], $result[0]['content']);
			$result[0]['content'] = implode('', $result[0]['content']);
			$blog = $result[0];
			return $blog;
		} else {
			return 0;
		}
	}
	function tags($tags){
		$tags = explode(', ', $tags);
		$newtags = array();
		foreach($tags as $tag){
			$newtags[] = '<a href="tags.php?t=' . $tag . '">' . word_wrap($tag) . '</a>';
		}
		return implode(', ', $newtags);
	}
	function view_tags($tag){
		$blogs_table = $GLOBALS['blogs_table'];
		$result = q("SELECT title, tags FROM $blogs_table");
		$posts = array();
		if ($result) {
			foreach($result as $row){
				$tags = explode(', ', $row['tags']);
				foreach($tags as $tag_tester){
					if($tag_tester == $tag){
						$posts[] = $row['title'];
					}
				}
			}
			$str = "<ul class=\"list\">\n";
			foreach($posts as $post){
				$str = $str . '			<li><a href="view.php?blog=' . $post . '">' . word_wrap($post) . "</a></li>\n";
			}
			return $str . "		</ul>\n";
		} else {
			return 0;
		}
	}
	function view_authors($name){
		$blogs_table = $GLOBALS['blogs_table'];
		$result = q("SELECT title FROM $blogs_table WHERE author = '$name'");
		$str = "<ul class=\"list\">\n";
		if ($result) {
			if(is_array($result)){
				foreach($result as $post){
					$str = $str . '			<li><a href="view.php?blog=' . $post['title'] . '">' . $post['title'] . "</a></li>\n";
				}
			}else{
				$str = $str . '			<li><a href="view.php?blog=' . $result . '">' . word_wrap($result) . "</a></li>\n"	;
			}
			return $str . "		</ul>\n";
		} else {
			return 0;
		}
	}
	function write_comment($name, $comment, $bid){
		$date = $_SERVER['REQUEST_TIME'];
		$name = addslashes($name);
		$comment = addslashes($comment);
		$comments_table = $GLOBALS['comments_table'];
		if (mysql_query("INSERT INTO $comments_table (id, bid, name, comment, date) VALUES (NULL, '$bid', '$name', '$comment', '$date')")) {
			return 'Success!';
		} else {
			return "Failed :(";
		}
	}
	function view_comments($bid){
		$comments_table = $GLOBALS['comments_table'];
		$result = q("SELECT id, name, comment, date FROM $comments_table WHERE bid = '$bid'");
		if ($result != false){
			$dates = array();
			foreach($result as $row){
				$dates[$row['id']] = $row['date'];
			}
			arsort($dates);
			$ids = array_keys($dates);
			$comments = array();
			
			if(sizeof($ids) == 1){
				foreach($result as $row){
					if($row['id'] == $ids[0]){
						$comments[] = $row;
					}
				}
			}else{
				foreach($ids as $id){
					foreach($result as $row){
						if($id == $row['id']){
							$comments[] = $row;
						}
					}
				}
			}
			return $comments;
		}else{
			return false;
		}
	}
	function list_tags(){
		$blogs_table = $GLOBALS['blogs_table'];
		$result = q("SELECT tags FROM $blogs_table");
		$tags = array();
		if($result != 0){
			foreach($result as $tag_array){
				$tags[] = explode(', ', $tag_array['tags']);
			}
			$tags_list = array();
			foreach($tags as $tag_array){
				foreach($tag_array as $tag){
					$tags_list[] = $tag;
				}
			}
			return array_unique($tags_list);
		}else{
			return 0;
		}
	}
	function list_names(){
		$blogs_table = $GLOBALS['blogs_table'];
		$result = q("SELECT author FROM $blogs_table");
		$names = array();
		if($result != 0){
			foreach($result as $name_array){
				foreach($name_array as $name){		
					$names[] = $name;
				}
			}
			return array_unique($names);
		}else{
			return 0;
		}
	}
	function list_blogs(){
		$blogs_table = $GLOBALS['blogs_table'];
		$result = q("SELECT id, date, title, author FROM $blogs_table");
		if($result != 0){
			$dates = array();
			$i = 0;
			foreach($result as $row){
				$dates[$row['id']] = $row['date'];
				$comment_no = count_comments($row['id']);
				array_push($result[$i], $comment_no);
				$i++;
			}
			arsort($dates);
			$ids = array_keys($dates);
			$content = array();
			
			if(sizeof($ids) == 1){
				foreach($result as $row){
					if($row['id'] == $ids[0]){
						$content[] = $row;
					}
				}
			}else{
				foreach($ids as $id){
					foreach($result as $row){
						if($id == $row['id']){
							$content[] = $row;
						}
					}
				}
			}
			return $content;
		}else{
			return 0;
		}
	}
	function tag_cloud($no){
		$blogs_table = $GLOBALS['blogs_table'];
		$result = q("SELECT tags FROM $blogs_table");
		$tags = array();
		if(is_array($result)){
			foreach($result as $tag_array){
				$tags[] = explode(', ', $tag_array['tags']);
			}
		}else{
			$tags[] = explode(', ', $result);
		}
		$tags_list = array();
		foreach($tags as $tag_array){
			foreach($tag_array as $tag){
				$tags_list[] = $tag;
			}
		}
		$unique_tags = array_unique($tags_list);
		$tag_frequency = array();
		foreach($unique_tags as $tag){
			$i = 0;
			foreach($tags_list as $tag2){
				if($tag == $tag2){
					$i++;
				}
			}
			$tag_frequency[] = array($i, $tag);
		}
		$list_tags = array();
		$list_freq = array();
		foreach($tag_frequency as $tag_array){
			$list_freq[] = $tag_array[0];
			$list_tags[] = $tag_array[1];
		}
		arsort($list_freq);
		$tag_rank = array();
		reset($list_freq);
		
		$key = true;
		$size = sizeof($list_freq);
		if($size < $no){
			for($i = 0; $i < $size; $i++){
				$key = each($list_freq);
				$key2 = $key['key'];
				$font_size = 100+($list_freq[$i]/sizeof($tags_list))*500;
				$font_size = $font_size . '%';
				$this_tag = word_wrap($list_tags[$key2]);
				$tag_rank[] = "				<li style=\"font-size: $font_size;\"><a href=\"tags.php?t=$list_tags[$key2]\" title=\"$list_freq[$i] occurrences\">$this_tag</a></li>\n";
			}
		}else{
			for($i = 0; $i < $no; $i++){
				$key = each($list_freq);
				$key2 = $key['key'];
				$font_size = 100+($list_freq[$i]/sizeof($tags_list))*500;
				$font_size = $font_size . '%';
				$this_tag = word_wrap($list_tags[$key2]);
				$tag_rank[] = "				<li style=\"font-size: $font_size;\"><a href=\"tags.php?t=$list_tags[$key2]\" title=\"$list_freq[$i] occurrences\">$this_tag</a></li>\n";
			}
		}
		return "<ol>\n" . implode('', $tag_rank) . "			</ol>\n";
	}
	function count_comments($bid){
		$comments_table = $GLOBALS['comments_table'];
		$result = q("SELECT id FROM $comments_table WHERE bid = '$bid'");
		if($result){
			return sizeof($result);
		}else{
			return 0;
		}
	}
?>















