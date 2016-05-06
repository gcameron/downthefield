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
	//print $node->nodeValue."\n";   
		$row_headers[] = $node->nodeValue;
	}   

	$table = array();
	//get all rows from the table 
	$i = 0;
	$j = 0;
	$cooldown = False;
	$daysused = 0;
	foreach($rows as $row) {
		if ( ! $row->hasAttribute('class')) continue;
		$class = $row->getAttribute('class');
		if (!strcmp('links', $class)) {
			$str = $row->nodeValue;
			$time = "";
			if($j >= 3) {
				$table[] = $newrow;
				continue;
			}
			$k = 0;
			$len = strlen($str);
			while($k < $len && strcmp(substr($str, $k, 1),':')) $k++;
			if($k < $len) {
				if(ctype_digit(substr($str, $k-2, 1))) {
					$time = substr($str, $k-2, 8);
				} else {
					$time = substr($str, $k-1, 7);
				}
			}
			//$times = $row->getElementsByTagName('span');
			//echo $times->item(0);
			$newrow[] = $time;
			$table[] = $newrow;
		} else if(!strcmp('info', $class)) {
			$i++;
			$cols = $row->getElementsByTagName('td');   
			$newrow = array();
			$j=0;
			foreach ($cols as $node) {
				if(!strcmp(trim($node->nodeValue),"")) continue;
				if($j >= 3) {
					$j++;
					continue;
				}
       # code...
        //print $node->nodeValue."\n";   
				if($row_headers==NULL)
					$newrow[] = trim($node->nodeValue);
				else
					$newrow[$row_headers[$j]] = trim($node->nodeValue);
				$j++;
			}
			if($i >= 12) $cooldown = True;
		} else { // date
			if($cooldown) break;
			$daysused++;
			if($daysused >= 4) {
				$cooldown = True;
			}
			$cols = $row->getElementsByTagName('td');   
			$newrow = array();
			$j=0;
			foreach ($cols as $node) {
				if($j >= 1) continue;
       # code...
        //print $node->nodeValue."\n";   
				if($row_headers==NULL)
					$newrow[] = trim($node->nodeValue);
				else
					$newrow[$row_headers[$j]] = trim($node->nodeValue);
				$j++;
			}
			$table[] = $newrow;
		}
	}
	return $table;
}

function update_schedule() {
	file_put_contents('schedule.html', wp_remote_fopen('http://www.yalebulldogs.com/landing/index'));
}

function update_standings() {
	$arr = array(
		array("bsb/2015-16/standings",           "baseball.html"),
		array("sball/2015-16/standings-include", "softball.html"),
		array("mlax/2015-16/standings",          "menslacrosse.html"),
		array("wlax/2015-16/standings",		     "womenslacrosse.html"),
		array("mten/2015-16/standings",          "menstennis.html"),
		array("wten/2015-16/standings",          "womenstennis.html"));

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
	//load the html  
	$dom = new DOMDocument();
	$html = $dom->loadHTMLFile('schedule.html');  

		//discard white space   
	$dom->preserveWhiteSpace = false;

	//the table by its tag name  
	$tables = $dom->getElementsByTagName('table');   

	//get all rows from the table  
	$table = get_schedule($tables);

	echo build_schedule($table);

}

function standings() {

	$arr = array(
		array("baseball.html", "Baseball", 0),
		array("softball.html", "Softball", 0),
		array("menslacrosse.html", "Men's Lacrosse", 1),
		array("womenslacrosse.html",		     "Women's Lacrosse", 1),
		array("menstennis.html",          "Men's Tennis", 1),
		array("womenstennis.html",          "Women's Tennis", 1));

	for($sport = 0; $sport < 6; $sport++) {

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

if ( current_user_can('contributor') && !current_user_can('upload_files') ) {
	add_action('admin_init', 'allow_contributor_uploads');
}

function allow_contributor_uploads() {
     $contributor = get_role('contributor');
     $contributor->add_cap('upload_files');
}

if (class_exists('MultiPostThumbnails')) {

	new MultiPostThumbnails(array(
		'label' => 'Home Page Image (1:2 height/width ratio or shorter)',
		'id' => 'homepage-image',
		'post_type' => 'post'
		) );
}

?>
