<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
function insert_content() {
	global $wpdb;
	$quiz = json_decode(stripslashes($_POST['quiz']));
	$answ = json_decode(stripslashes($_POST['answ']));
	$all_id_quiz = json_decode(stripslashes($_POST['all_id_quiz']));
	$all_id_answ = json_decode(stripslashes($_POST['all_id_answ']));
	$quiz__answ_uniqueID = "qa_".uniqid();
	
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$cur_time = date("d m, Y, g:i a");
	print_r($cur_time);
	for ( $n = 0; $n <= count($answ) - 1; $n++ ){
		$sql = "INSERT INTO Answer (content, element_id, UniqueID, Created_Time) VALUES ( '$answ[$n]', '$all_id_answ[$n]', '$quiz__answ_uniqueID', '$cur_time')"; 
		$result = $wpdb->query($sql);
		if ($result == true) { 
			echo ("INSERT SUCCESSFUL!");
		}
		else {
			echo ("ERROR !!!");
		}	
	}
	for ($k = 0; $k <= count($quiz) - 1; $k++){
		$sql = "INSERT INTO Ask (content, element_id, UniqueID, Created_Time) VALUES ('$quiz[$k]', '$all_id_quiz[$k]', '$quiz__answ_uniqueID', '$cur_time' )"; 
		$result = $wpdb->query($sql);
		if ($result == true) { 
			echo ("INSERT SUCCESSFUL!");
		}
		else {
			echo ("ERROR !!!");
		}
	}	
}
insert_content();
?>
