<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
function Get_Answ_Select(){
	global $wpdb;
	$content = json_decode(stripslashes($_POST['selected']));
	$answ_id = json_decode(stripslashes($_POST['answer_id']));
	$uniqID = json_decode(stripslashes($_POST['uniqID']));
	for ($n = 0; $n <= count($content) - 1; $n++){
		$sql = "INSERT INTO Answ_Selected (answer_id,content, uniqID) VALUES ($answ_id[$n],'$content[$n]','$uniqID[$n]')";
		$result = $wpdb->query($sql);
		if ($result == true) { 
			echo ("INSERT SUCCESSFUL!");
		}
		else {
			echo ("ERROR !!!");
		}	
	}
}
Get_Answ_Select();