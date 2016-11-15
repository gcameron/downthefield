<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
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

function get_schedule($tables) {
	// get each column by tag name  
	$rows = $tables->item(0)->getElementsByTagName('tr');  
	$cols = $rows->item(0)->getElementsByTagName('th');
	$row_headers = NULL;

	foreach ($cols as $node) {
		$row_headers[] = $node->nodeValue;
	}   

	$table = array();

	// this code takes the HTML node for the schedule table and decides how many games to include
	// there is a cap for number of games to include and number of days to include, but we never
	// stop within a day. thus when the games cap is reached, $cooldown is set to True, we include
	// the remaining games in that day then stop

	// each game has a team and opponent, which are found in a row with class "info", possibly a score, which is
	// also found in the "info" row, and possibly a time, which is found in a row with class "links"

	$games_used = 0;
	$j = 0; // number of cells identified for current game - 1 means we have the team, 2 means we have the
			// opponent, 3 means we have the time or score
	$cooldown = False; // if cooldown is set to true, we include the remaining games in the current day then stop
	$days_used = 0;
	foreach($rows as $row) {
		if (!$row->hasAttribute('class')) continue; // just making sure we have a class
		$class = $row->getAttribute('class');

		if (!strcmp('links', $class)) { // this row has the time
			$str = $row->nodeValue;
			$time = "";
			if($j >= 3) { // we already got the score, so none of this is necessary
				$table[] = $newrow;
				continue;
			}

			// find time from row, which always has a colon in it and an &nbsp to end it
			$k = 0;
			$len = strlen($str);
			while($k < $len && strcmp(substr($str, $k, 1),':')) $k++;
			if($k < $len) {
				if(ctype_digit(substr($str, $k-2, 1))) {
					$time_start = $k - 2;
				} else {
					$time_start = $k - 1;
					$time = substr($str, $k-1, 7);
				}
				while($k < $len && strcmp(substr($str, $k, 1),'&')) $k++;
				$time_end = $k - $time_start;
				$time = substr($str, $time_start, $time_end);
			}
			$newrow[] = $time;
			$table[] = $newrow; // add game to main table

		} else if(!strcmp('info', $class)) { // this row has the team, opponent and possibly score
			$games_used++;
			$cols = $row->getElementsByTagName('td');   
			$newrow = array();
			$j = 0; // start new game for next row
			foreach ($cols as $node) {
				if(!strcmp(trim($node->nodeValue),"")) continue;
				if($j >= 3) {
					$j++;
					continue;
				}
				if($row_headers==NULL)
					$newrow[] = trim($node->nodeValue);
				else
					$newrow[$row_headers[$j]] = trim($node->nodeValue);
				$j++;
			}
			if($games_used >= 12) $cooldown = True;

		} else if(!strcmp('date', $class)) { // this row has the date
			if($cooldown) break;
			$days_used++;
			if($days_used >= 3) {
				$cooldown = True; // this will be our last day included
			}
			$cols = $row->getElementsByTagName('td');   
			$newrow = array();
			$j = 0; // start new game for next row
			foreach ($cols as $node) {
				if($j >= 1) continue;
				if($row_headers==NULL)
					$newrow[] = trim($node->nodeValue);
				else
					$newrow[$row_headers[$j]] = trim($node->nodeValue);
				$j++;
			}
			$table[] = $newrow; // add time to main table
		}
	}
	return $table;
}

function update_schedule() {
	file_put_contents('schedule.html', wp_remote_fopen('http://www.yalebulldogs.com/landing/index'));
}

function update_standings() {
	$arr = array(
		array("fball/2016-17/standings",		"football.html"),
		array("wvball/2016-17/standings",		"volleyball.html"),		
		array("msoc/2016-17/standings",			"menssoccer.html"),
		array("wsoc/2016-17/standings",			"womenssoccer.html"),
		array("fh/2016-17/standings",			"fieldhockey.html"));
/*		array("bsb/2015-16/standings",           "baseball.html"),
		array("sball/2015-16/standings-include", "softball.html"),
		array("mlax/2015-16/standings",          "menslacrosse.html"),
		array("wlax/2015-16/standings",		     "womenslacrosse.html"),
		array("mten/2015-16/standings",          "menstennis.html"),
		array("wten/2015-16/standings",          "womenstennis.html")); */

	foreach($arr as $row) {
		file_put_contents($row[1], wp_remote_fopen('http://ivyleague.com/sports/'.$row[0]));
	}
}

/*function update_schedule() {
	global $wp_filesystem;
	$wp_filesystem->put_contents('schedule.html', $wp_filesystem->get_contents('http://www.yalebulldogs.com/landing/index'));
}


function update_standings() {

	global $wp_filesystem;

	$arr = array(
		array("bsb/2015-16/standings",           "baseball.html"),
		array("sball/2015-16/standings-include", "softball.html"),
		array("mlax/2015-16/standings",          "menslacrosse.html"),
		array("wlax/2015-16/standings",		     "womenslacrosse.html"),
		array("mten/2015-16/standings",          "menstennis.html"),
		array("wten/2015-16/standings",          "womenstennis.html"));

	foreach($arr as $row) {
		$wp_filesystem->put_contents($row[1], $wp_filesystem->get_contents('http://ivyleague.com/sports/'.$row[0]));
	}
}*/

function schedule() {
	// load the html  
	$dom = new DOMDocument();
	$html = $dom->loadHTMLFile('schedule.html');  

	// discard white space   
	$dom->preserveWhiteSpace = false;

	//the table by its tag name  
	$tables = $dom->getElementsByTagName('table');   

	//get all rows from the table  
	$table = get_schedule($tables);

	echo build_schedule($table);

}

function standings() {

	$arr = array(
		array("football.html", "Football", 1),
		array("volleyball.html", "Volleyball", 1),
		array("menssoccer.html", "Men's Soccer", 1),
		array("womenssoccer.html", "Women's Soccer", 1),
		array("fieldhockey.html", "Field Hockey", 1));

/*		array("baseball.html", "Baseball", 0),
		array("softball.html", "Softball", 0),
		array("menslacrosse.html", "Men's Lacrosse", 1),
		array("womenslacrosse.html", "Women's Lacrosse", 1),
		array("menstennis.html", "Men's Tennis", 1),
		array("womenstennis.html", "Women's Tennis", 1)); */

	$num_sports = count($arr);
	for($sport = 0; $sport < $num_sports; $sport++) {

		//load the html  
		$dom = new DOMDocument();
		$html = $dom->loadHTMLFile($arr[$sport][0]);  

		//discard white space   
		$dom->preserveWhiteSpace = false;

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
if ( current_user_can('contributor') && !current_user_can('upload_files') ) {
	add_action('admin_init', 'allow_contributor_uploads');
}

function allow_contributor_uploads() {
     $contributor = get_role('contributor');
     $contributor->add_cap('upload_files');
}

// allow for home page image upload
if (class_exists('MultiPostThumbnails')) {

	new MultiPostThumbnails(array(
		'label' => 'Home Page Image (1:2 height/width ratio or shorter)',
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
		twentysixteen_entry_date();
		printf(', '.get_post_time('g:i A'));
	}

}

$counter = -1;
$row_mobile = '';

add_image_size('row', 300, 150, true );

?>