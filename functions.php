<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
	$parent_style = 'parent-style';
    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css' );
}

function build_table($array, $wideNum) {
    // start table
    $html = '<table class="sidebar sidebar-table"><thead>';
    // header row

    $odd = False;
    $first = True;
    // data rows
    foreach( $array as $key=>$value){
    	if(!strcmp($value[0], "Yale") || !strcmp($value[1], "Yale")) {
    		$html .= '<tr class="yale">';
    	} else if ($odd) {
    		$html .= '<tr class="odd">';
    	} else {
    		$html .= '<tr class="even">';
    	}
        $len = count($value);
        foreach($value as $key2=>$value2) {
        	if($first) {
        		$html .= '<th ';
        	} else {
        		$html .= '<td ';
        	}
        	if($i < $wideNum) {
        		$html .= 'class="super-small">' . $value2;
        	} else if ($i == $wideNum) {
        		$html .= 'class="wide">' . $value2;
        	} else {
        		$html .= 'class="small">' . $value2;
        	}
        	if($first) {
        		$html .= '</th>';
        	} else {
        		$html .= '</td>';
        	}
        	$i++;
        }
        if($first) {
        	$html .= '</tr></thead><tbody>';
        	$first = False;
        } else {
        	$html .= '</tr>';
        }
        $odd = !$odd;
    }

    // finish table and return it
    $html .= '</tbody></table>';
    return $html;
}

function build_schedule($array) {
    // start table
    $html = '<p class="sidebar sidebar-title">Upcoming Yale Schedule</p>';
    // header row
    $inTable = False;
    // data rows
    foreach($array as $key=>$value){
    	if(count($value) == 1) {
    		if($inTable) $html .= '</table>';
    		$html .= '<p class="sidebar sidebar-subtitle">'.$value[0].'</p><table class="sidebar sidebar-table">';
    		$inTable = True;
    		continue;
    	}
    	if ($odd) {
    		$html .= '<tr>';
    	} else {
    		$html .= '<tr>';
    	}
    	$i = 0;
        foreach($value as $key2=>$value2){
        	if(count($value) == 1) {
        		$html .= '<td class="team" colspan="3">' . $value2 . '</td>';
        	} else {
        		if($i == 0) {
        			$html .= '<td class="team">' . $value2 . '</td>';
        		} else if ($i == 1) {
        			$html .= '<td class="opponent">' . $value2 . '</td>';
        		} else {
        			$html .= '<td class="time">' . $value2 . '</td>';
        		}
        		$i++;
    			
    		}
        }
        $html .= '</tr>';
        $odd = !$odd;
    }

    // finish table and return it
    $html .= '</table>';
    return $html;
}


function delete_col(&$array, $offset) {
    return array_walk($array, function (&$v) use ($offset) {
        array_splice($v, $offset, 1);
    });
}

function hockey_standings($dom, $sport_name) {
	$tables = $dom->getElementsByTagName('table');

	$standings_table = NULL;
	foreach ($tables as $table) {
		if ($table->hasAttribute('class')) {
			$class = $table->getAttribute('class');
			if (!strcmp($class, 'briefstats-table')) {
				$standings_table = $table;
				break;
			}
		}
	}

	if (!$standings_table) {
		return '';
	}

	$rows = $tables->item($i)->getElementsByTagName('tr');

	$table = array();
	$header_row = array();
	$header_row[] = '';
	$header_row[] = '';
	$header_row[] = 'Points';
	$header_row[] = 'Conference';
	$header_row[] = 'Overall';

	$table[] = $header_row;

	$counter = 0;
	foreach ($rows as $row) {
		if ($counter++ == 0) {
			continue;
		}
		$cols = $row->getElementsByTagName('td');   
		$row = array();
		foreach ($cols as $node) {
			$row[] = $node->nodeValue;
		}
		$table[] = $row;
	}

	echo '<p class="sidebar sidebar-title">ECAC '.$sport_name.' Standings</p>';
	echo build_table($table, 1);
}

function get_table($tables, $i) {
	// get each column by tag name  
	$rows = $tables->item($i)->getElementsByTagName('tr');
	$cols = $rows->item(0)->getElementsByTagName('th');
	$row_headers = NULL;

	foreach ($cols as $node) {
	//print $node->nodeValue."\n";   
		$row_headers[] = $node->nodeValue;
	}   

	$table = array();
	//get all rows from the table 

	foreach ($rows as $row) {   
			// get each column by tag name  
		$cols = $row->getElementsByTagName('td');   
		$row = array();
		$i=0;
		foreach ($cols as $node) {
       # code...
        //print $node->nodeValue."\n";   
			if($row_headers==NULL)
				$row[] = $node->nodeValue;
			else
				$row[$row_headers[$i]] = $node->nodeValue;
			$i++;
		}   
		$table[] = $row;
	}

	$newtable = array();

	$row = $table[1];
	$i = -1;
	$vals = array("", "Record");
	$numBlanks = 0;
	$numRecords = 0;

	foreach($row as $elt) {
		$i++;
		if (in_array($elt, $vals)) {
			if (!strcmp($elt, "")) {
				$numBlanks++;
				if($numBlanks >= 3) continue;
				$newtable[0][] = $table[1][$i];
			} else { // record
				$numRecords++;
				if($numRecords == 1) {
					$newtable[0][] = "Conference";
				} else {
					$newtable[0][] = "Overall";
				}
			}
				
			for($j = 2; $j < count($table); $j++) {
				$newtable[$j-1][] = $table[$j][$i];
			}
		}
	}
	return $newtable;
}

function update_schedule() {
	if (!file_exists('yaleschedule/')) {
		mkdir('yaleschedule');
	}
	$schedule_str = wp_remote_fopen('http://www.yalebulldogs.com/composite?print=rss');
	$schedule = simplexml_load_string($schedule_str);
	$fp = fopen('yaleschedule/schedule.csv', 'w');
	foreach($schedule->channel->item as $item) {
		$dc = $item->children('http://purl.org/dc/elements/1.1/');
		$date = strtotime((string)$dc->date);

		$ps = $item->children('http://www.prestosports.com/rss/schedule');
		$opponent = (string)$ps->opponent;

		if ($date && $date >= time()) {
			$fields = array();
			$fields[] = (string)$item->category;
			$fields[] = (string)$opponent;
			$fields[] = (string)$date;
			$fields[] = (string)$item->description;
			fputcsv($fp, $fields);
		}
	}
	fclose($fp);
}

function update_standings() {
	if (!file_exists('ivyleaguestandings/')) {
		mkdir('ivyleaguestandings');
	}
	$arr = array(
		array("http://www.ivyleaguesports.com/sports/mbkb/2016-17/standings",		"ivyleaguestandings/mensbasketball.html"),
		array("http://www.ivyleaguesports.com/sports/wbkb/2016-17/standings",		"ivyleaguestandings/womensbasketball.html"),		
		array("http://www.ecachockey.com/men/index",								"ivyleaguestandings/menshockey.html"),
		array("http://www.ecachockey.com/women/index",								"ivyleaguestandings/womenshockey.html"),
		array("http://www.ivyleaguesports.com/sports/msquash/2016-17/standings",	"ivyleaguestandings/menssquash.html"),
		array("http://www.ivyleaguesports.com/sports/wsquash/2016-17/standings",	"ivyleaguestandings/womenssquash.html"),
		array("http://www.ivyleaguesports.com/sports/mswimdive/2016-17/standings",	"ivyleaguestandings/mensswimming.html"),
		array("http://www.ivyleaguesports.com/sports/wswimdive/2016-17/standings",	"ivyleaguestandings/womensswimming.html"));
/*		array("http://www.ivyleaguesports.com/sports/fball/2016-17/standings",		"ivyleaguestandings/football.html"),
		array("http://www.ivyleaguesports.com/sports/wvball/2016-17/standings",		"ivyleaguestandings/volleyball.html"),		
		array("http://www.ivyleaguesports.com/sports/msoc/2016-17/standings",			"ivyleaguestandings/menssoccer.html"),
		array("http://www.ivyleaguesports.com/sports/wsoc/2016-17/standings",			"ivyleaguestandings/womenssoccer.html"),
		array("http://www.ivyleaguesports.com/sports/fh/2016-17/standings",			"ivyleaguestandings/fieldhockey.html")); */
/*		array("http://www.ivyleaguesports.com/sports/bsb/2015-16/standings",           "ivyleaguestandings/baseball.html"),
		array("http://www.ivyleaguesports.com/sports/sball/2015-16/standings-include", "ivyleaguestandings/softball.html"),
		array("http://www.ivyleaguesports.com/sports/mlax/2015-16/standings",          "ivyleaguestandings/menslacrosse.html"),
		array("http://www.ivyleaguesports.com/sports/wlax/2015-16/standings",		     "ivyleaguestandings/womenslacrosse.html"),
		array("http://www.ivyleaguesports.com/sports/mten/2015-16/standings",          "ivyleaguestandings/menstennis.html"),
		array("http://www.ivyleaguesports.com/sports/wten/2015-16/standings",          "ivyleaguestandings/womenstennis.html")); */

	foreach($arr as $row) {
		file_put_contents($row[1], wp_remote_fopen($row[0]));
	}
}

function find_time($str) {
	$str = str_replace(array('a.m.','p.m.', 'A.M.', 'P.M.'), array('am','pm', 'am', 'pm'), $str);
	$len = strlen($str);
	for ($i = 0; $i < $len - 2; $i++) {
		if (!is_numeric($str[$i])) {
			continue;
		}
		if ($str[$i] == '0') {
			continue;
		}
		if (is_numeric($str[$i + 1])) {
			if ($str[$i] != '1') {
				continue;
			}
			$colon = $i + 2;
		} else {
			$colon = $i + 1;
		}
		if ($str[$colon] == ' ') {
			$ampm = $colon + 1;
		} else if ($str[$colon] != ':') {
			continue;
		} else {
			if ($colon + 2 >= $len || !is_numeric($str[$colon + 1]) || !is_numeric($str[$colon + 2])) {
				continue;
			}
			$ampm = $colon + 3;
		}
		if ($ampm >= $len) {
			continue;
		}
		if ($str[$ampm] == ' ') {
			$ampm++;
		}
		if ($ampm + 1 >= $len || (strcmp(substr($str, $ampm, 2), 'am') && strcmp(substr($str, $ampm, 2), 'pm'))) {
			continue;
		}
		return substr($str, $i, $ampm + 2 - $i);
	}
	return '';
}

function schedule() {
	date_default_timezone_set('America/New_York');
	$max_games = 7;
	$file = fopen('yaleschedule/schedule.csv', 'r');

	$schedule_array = array();

	$new_row = array();

	$former_date = '';

	$games_used = 0;
	$cooldown = 0;
	while ($row = fgetcsv($file)) {
		if (!strcmp($row[0], '')) {
			continue;
		}
		$date = date('l, F j, Y', (int)$row[2]);
		if (strcmp($former_date, $date)) {
			if ($cooldown) {
				break;
			}
			$new_row[] = $date;
			$schedule_array[] = $new_row;
			$new_row = array();
			$former_date = $date;
		}
		$new_row[] = $row[0];
		$new_row[] = $row[1];
		$date = date('g:i a', (int)$row[2]);
		if (!strcmp($date, '12:00 am')) {
			$new_row[] = str_replace(array('am','pm', 'AM', 'PM', 'A.M.', 'P.M.'), array('a.m.','p.m.', 'a.m.', 'p.m.', 'a.m.', 'p.m.'), find_time($row[3]));
		} else {
			$new_row[] = str_replace(array('am','pm', 'AM', 'PM', 'A.M.', 'P.M.'), array('a.m.','p.m.', 'a.m.', 'p.m.', 'a.m.', 'p.m.'), $date);
		}
		$schedule_array[] = $new_row;
		$new_row = array();
		$games_used++;
		if ($games_used >= $max_games) {
			$cooldown = 1;
		}
	}
	echo build_schedule($schedule_array);
}

function standings() {

	$arr = array(
		array("ivyleaguestandings/mensbasketball.html", "Men's Basketball", 1),
		array("ivyleaguestandings/womensbasketball.html", "Women's Basketball", 1),
		array("ivyleaguestandings/menshockey.html", "Men's Hockey", 2),
		array("ivyleaguestandings/womenshockey.html", "Women's Hockey", 2),
//		array("ivyleaguestandings/menssquash.html", "Men's Squash", 1),
//		array("ivyleaguestandings/womenssquash.html", "Women's Squash", 1),
		array("ivyleaguestandings/mensswimming.html", "Men's Swimming and Diving", 0),
		array("ivyleaguestandings/womensswimming.html", "Women's Swimming and Diving", 0));

/*		array("ivyleaguestandings/football.html", "Football", 1),
		array("ivyleaguestandings/volleyball.html", "Volleyball", 1),
		array("ivyleaguestandings/menssoccer.html", "Men's Soccer", 1),
		array("ivyleaguestandings/womenssoccer.html", "Women's Soccer", 1),
		array("ivyleaguestandings/fieldhockey.html", "Field Hockey", 1)); */

/*		array("ivyleaguestandings/baseball.html", "Baseball", 0),
		array("ivyleaguestandings/softball.html", "Softball", 0),
		array("ivyleaguestandings/menslacrosse.html", "Men's Lacrosse", 1),
		array("ivyleaguestandings/womenslacrosse.html", "Women's Lacrosse", 1),
		array("ivyleaguestandings/menstennis.html", "Men's Tennis", 1),
		array("ivyleaguestandings/womenstennis.html", "Women's Tennis", 1)); */

	$num_sports = count($arr);
	for($sport = 0; $sport < $num_sports; $sport++) {

		//load the html  
		$dom = new DOMDocument();
		$internalErrors = libxml_use_internal_errors(true);
		$html = $dom->loadHTMLFile($arr[$sport][0]);
		libxml_use_internal_errors($internalErrors);

		//discard white space   
		$dom->preserveWhiteSpace = false;

		if ($arr[$sport][2] == 2) {
			echo hockey_standings($dom, $arr[$sport][1]);
			continue;
		}

		//the table by its tag name
		$tables = $dom->getElementsByTagName('table');

		//get all rows from the table  
		$newtable = get_table($tables, 0);

		echo '<p class="sidebar sidebar-title">Ivy League '.$arr[$sport][1].' Standings</p>';
		if(!strcmp($arr[$sport][1], "Baseball")) {
			echo '<p class="sidebar sidebar-subtitle">Red Rolfe Division</p>';
			$newtable2 = get_table($tables, 1);
			echo build_table($newtable2, $arr[$sport][2]);
			echo '<p class="sidebar sidebar-subtitle">Lou Gehrig Division</p>';
		}
		if(!strcmp($arr[$sport][1], "Softball")) {
			echo '<p class="sidebar sidebar-subtitle">North Division</p>';
			echo build_table($newtable, $arr[$sport][2]);
			$newtable = get_table($tables, 1);
			echo '<p class="sidebar sidebar-subtitle">South Division</p>';
		}
		echo build_table($newtable, $arr[$sport][2]);
	}
}

// give contributor permission to upload photos
if (current_user_can('contributor') && !current_user_can('upload_files') ) {
	add_action('admin_init', 'allow_contributor_uploads');
}

function allow_contributor_uploads() {
     $contributor = get_role('contributor');
     $contributor->add_cap('upload_files');
}

// allow for home page image upload
if (class_exists('MultiPostThumbnails')) {
	new MultiPostThumbnails(array(
		'label' => 'Home Page Image (will be cropped as roughly 2:1 horizontal)',
		'id' => 'homepage-image',
		'post_type' => 'post'
		) );
}

remove_filter( 'excerpt_more', 'twentysixteen_excerpt_more' );

//read More Button For all excerpts
// excerpt read more links and wrap excerpt in p tag
function all_excerpts_get_more_link($post_excerpt) {
    return '<p>' . $post_excerpt. ' <a href="'. get_permalink($post->ID) . '">' . 'Read more' . '</a></p>';
}
add_filter('wp_trim_excerpt', 'all_excerpts_get_more_link');

function downthefield_entry_meta() {
	if (function_exists('coauthors_links')) {
		printf('By ');
		coauthors_links();
		printf('&nbsp|&nbsp');
	} else {
		printf('By '.get_the_author_link().'&nbsp|&nbsp');
	}

	if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
		downthefield_entry_date();
		printf(', '.str_replace(array('am','pm'),array('a.m.','p.m.'),get_post_time('g:i a')));
	}

}

function downthefield_entry_date() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		get_the_date(),
		esc_attr( get_the_modified_date( 'c' ) ),
		get_the_modified_date()
	);

	printf( '<span class="posted-on"><span class="screen-reader-text">%1$s</span>%2$s</span>',
		_x( 'Posted on', 'Used before publish date.', 'twentysixteen' ),
		$time_string
	);
}

function downthefield_post_thumbnail($class = 'attachment-post-thumbnail size-post-thumbnail') {
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular() ) :
	?>

	<div class="post-thumbnail">
		<?php the_post_thumbnail(); ?>
	</div><!-- .post-thumbnail -->

	<?php else : ?>

	<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
		<?php the_post_thumbnail( 'post-thumbnail', array( 'alt' => the_title_attribute( 'echo=0' ), 'class' => $class) ); ?>
	</a>

	<?php endif; // End is_singular()
}

//Remove a function from the parent theme
function remove_parent_filters(){ //Have to do it after theme setup, because child theme functions are loaded first
	if (has_filter( 'excerpt_more', 'twentysixteen_excerpt_more' )) {
		remove_filter('excerpt_more', 'twentysixteen_excerpt_more');
	}
}

add_action( 'after_setup_theme', 'remove_parent_filters' );

// this ellipsis looks better than
function space() {
	return " … ";
}

add_filter( 'excerpt_more', 'space' );

function possibly_update_standings_schedule() {
	if (!file_exists('yaleschedule/last_update.txt')) {
		$f = fopen("yaleschedule/last_update.txt", "w");
		update_schedule();
		fwrite($f, time());
		fclose($f);
	} else {
		$f = fopen("yaleschedule/last_update.txt", "r");
		$last_update = 0;
		$check = fscanf($f, "%d", $last_update);
		if ($check == 1 && time() - $last_update >= 43200) {
			fclose($f);
			$f = fopen("yaleschedule/last_update.txt", "w");
			update_schedule();
			fwrite($f, time());
			fclose($f);
		}
	}

	if (!file_exists('ivyleaguestandings/')) {
		mkdir('ivyleaguestandings');
	}
	if (!file_exists('ivyleaguestandings/last_update.txt')) {
		$f = fopen("ivyleaguestandings/last_update.txt", "w");
		update_standings();
		fwrite($f, time());
		fclose($f);
	} else {
		$f = fopen("ivyleaguestandings/last_update.txt", "r");
		$last_update = 0;
		$check = fscanf($f, "%d", $last_update);
		if ($check == 1 && time() - $last_update >= 43200) {
			fclose($f);
			$f = fopen("ivyleaguestandings/last_update.txt", "w");
			update_standings();
			fwrite($f, time());
			fclose($f);
		}
	}
}

$counter = -1;
$row_mobile = '';

add_image_size('row', 300, 150, true );

?>