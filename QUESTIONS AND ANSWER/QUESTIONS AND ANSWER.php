<?php
session_start();
/**
 * Plugin Name: QUESTIONS AND ANSWER
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: The very first plugin that I have ever created.
 * Version: 1.0
 * Author: Hoang Duc Tri
 * Author URI: http://www.mywebsite.com
 */

add_action( 'the_content', 'question' );
add_action('admin_menu', 'create_menu_213131');
add_action( 'add_meta_boxes', 'add_quiz_answ_form' );
//[question_and_answer id=xxxxxxxxx]
add_shortcode( 'question_and_answer', 'Content' );
add_shortcode('get_all_answer','Get_All_Answer');
//----------------------------------------------------------
function question ( $question ) {
    return $question .= '<p>Thank you for reading!</p>';
}
function add_quiz_answ_form() {
	add_meta_box('meta-box-id', 'Question and Answer', 'wpdocs_plugin_display_callback', 'post');
}
function create_menu_213131(){
	add_menu_page( 'Question & Answer', 'Question & Answer', 'manage_options', 'quiz_answ', 'display_list_id');
    add_submenu_page( 'quiz_answ','Add', 'Add', 'manage_options', 'add', 'wpdocs_plugin_display_callback');
}
function create_db(){
	global $wpdb;
	$ask = "SELECT 1 FROM Ask LIMIT 1";
	$check_ask = $wpdb->get_results($ask);
	if($check_ask == false){
		$sql = "CREATE TABLE Ask( Id int(11) NOT NULL AUTO_INCREMENT, content text NOT NULL , element_id varchar(1000) NOT NULL, UniqueID text NOT NULL, Created_Time text NOT NULL, PRIMARY KEY (Id))";
		$result = $wpdb->get_results($sql);
	}
	else {}
	$answer = "SELECT 1 FROM Answer LIMIT 1";
	$check_answer = $wpdb->get_results($answer);
	if($check_answer == false){
		$sql = "CREATE TABLE Answer( Id int(11) NOT NULL AUTO_INCREMENT, content text NOT NULL , element_id varchar(1000) NOT NULL, UniqueID text NOT NULL, Created_Time text NOT NULL, PRIMARY KEY (Id))";
		$result = $wpdb->get_results($sql);
	}
	else {}
	$answ_selected = "SELECT 1 FROM Answ_Selected LIMIT 1";
	$check_answ_selected = $wpdb->get_results($answ_selected);
	if($check_answ_selected == false){
		$sql = "CREATE TABLE Answ_Selected( Id int(11) NOT NULL AUTO_INCREMENT, answer_id int(11) NOT NULL , content text NOT NULL, uniqID varchar(100) NOT NULL, PRIMARY KEY (Id))";
		$result = $wpdb->get_results($sql);
	}
	else {}
}
create_db();
function Content($atts){
	if(!isset($_SESSION['i'])){ $_SESSION['i'] = 0;}
	?> 
	<style>
		.Ask_Answ {
			background-color: lightgrey;
			width: 539px;
			border: 15px solid green;
			padding: 0px;
			display: inline-block;
		}
	</style>
	<script>
	if (typeof window.id_answ === "undefined"){
		window.id_answ = 0;
	}
	else {}
	if (typeof window.z === "undefined"){
		window.z = 0;
	}
	else {}
	</script>
	<?php
	
	$value = shortcode_atts( array(
        'id' => '',     
	), 
	$atts );
	//print_r($value);
	if (!isset($_SESSION['id'])){	
		$_SESSION['id'] = array();
	}
	array_push($_SESSION['id'],$value['id']);
	//require_once "wp-content/plugins/plugin(1)/Get_Content.php";
	$sql = "SELECT * FROM Ask WHERE UniqueID = '{$value['id']}'";
	global $wpdb;
	$result = $wpdb->get_results($sql,ARRAY_A);
	
	if (!isset($_SESSION['num_ask'])) {
		$_SESSION['num_ask'] = 0;
	}

	ob_start();
	foreach ($result as $row) {
?>				
		<div class="Ask_Answ"><p value="<?php echo($row['Id']) ; ?>" id = "<?php echo($row['element_id']); ?>"><?php echo ($_SESSION['i'] = $_SESSION['i'] +1); ?>. <?php echo($row['content']); ?></p>
<?php
		$_SESSION['num_ask'] = $_SESSION['num_ask'] + 1;
		get_Answ($value['id']);
	}
	?> <input type="submit" onclick="Get_Answ_Selected()"> </div> 
	<script>
	function Get_Answ_Selected(){
		var selected = new Array ;
		var uniqID = new Array;
		var answ_id = new Array;
		var z = 0 ;
		var n = 0;
		for (var n = 0; n <= window.id_answ - 1 ; n++){
			console.log(n);
			if (document.getElementById(n).checked){
				selected[z] = unescape(document.getElementById("lb."+n).innerText);
				answ_id[z] = document.getElementById(n).value;
				uniqID[z] = document.getElementById(n).className;
				z = z + 1;
			}
			else {}
		}
		console.log(answ_id);
		var jsonString_selected = JSON.stringify(selected);
		var jsonString_answ_id = JSON.stringify(answ_id);
		var jsonString_uniqID = JSON.stringify(uniqID);
		$.ajax({ 
			type: "POST",
			url: "/wp-content/plugins/QUESTIONS AND ANSWER/Get_Answ_Select.php",
			data: {answer_id:jsonString_answ_id,selected:jsonString_selected, uniqID:jsonString_uniqID},
			
			success: function(Get_Answ_Select) {
				console.log("Submitted answer!!");
			},
			
			error: function(Get_Answ_Select){
				console.log("ERROR !!!");
			}
		});
	}
	</script>
	<?php

	$output = ob_get_contents();
	ob_clean();
	print $output;
}
function get_Answ($value){
		global $wpdb;
		$sql = "SELECT * FROM Answer WHERE UniqueID = '$value'";
		$result = $wpdb->get_results($sql,ARRAY_A);
		$n = 0;
		$z = 1;
		if (!isset($_SESSION['id_answ'])){
			$_SESSION['id_answ'] = 0;
		}
		else {}
		$content = array();
		$answ_id = array();
		foreach ($result as $row) {
			array_push($content,$row['content']);
			array_push($answ_id,$row['Id']);
		}
		//print_r($content);
		?><form> <?php
		$count = count($content);
		for ( $n = 0; $n <= 3; $n++){
?>			<input type="radio" value="<?php echo($answ_id[$n]);?>" name ="radio <?php echo($z);?>" id="<?php echo $_SESSION['id_answ']; ?>" class="<?php echo $value?>"> <label id="lb.<?php echo $_SESSION['id_answ']; ?>" for="radio <?php echo($z);?>"> <?php echo($content[$n]); ?> </label><br>
<?php
			$_SESSION['id_answ'] = $_SESSION['id_answ'] + 1;
			?><script> window.id_answ = window.id_answ + 1 ; window.z = window.z + 1;</script><?php
		}
		?></form> <?php
		$z = $z+1;
}

function display_list_id(){
	?> 
	<style>
		table {
			font-family: arial, sans-serif;
			border-collapse: collapse;
			width: 100%;
		}

		td, th {
			border: 1px solid #dddddd;
			text-align: left;
			padding: 8px;
		}
	</style>
	<?php
	
	$sql = "SELECT * FROM Ask";
	global $wpdb;
	$content = array();
	$uniqID = array();
	$time_cre = array();
	$sql_1 = "SELECT UniqueID FROM Ask";
	$result = $wpdb->get_results($sql,ARRAY_A);
	$result_1 = $wpdb->get_results($sql_1,ARRAY_A);
	?><table> 
		<tr>
		<script>var count = 0;</script>
			<th>Content</th>
			<th>ID Shortcode</th>
			<th>Time Created</th>
		</tr>
		<?php foreach ($result as $row) {
			array_push($content,$row['content']);
			array_push($uniqID,$row['UniqueID']);
			array_push($time_cre,$row['Created_Time']);
		}
		for ($i = 0; $i <= count($result_1) - 1; $i++){
	?>  <script> window.count = window.count + 1;</script>
		<tr>
			<td><?php echo($content[$i]); ?></td>
			<td id ="echo <?php "[question_and_answer id= ".$uniqID[$i]."]" ?>"><?php echo("[question_and_answer id= ".$uniqID[$i]."]"); ?></td>
			<td><?php echo($time_cre[$i]); ?></td>
		</tr>
		<?php } ?>
	</table>
	<p>* Just copy ID Shortcode and put it in your page or post * </p>
	<p>* If you want to get all answer just put this in your page or post : [get_all_answer] * </p>
<?php
}

function Get_All_Answer(){
	global $wpdb;
	if(!isset($n)){ $n = 1;}
	?>
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script><?php 
	for ($i = 0; $i < count($_SESSION['id']); $i++){
		$content = array();
		$answ_id = array();
		$count= array();
		$id_1 = $_SESSION['id'][$i];
		$sql = "SELECT * FROM Answ_Selected WHERE uniqID = '$id_1'";
		$result = $wpdb->get_results($sql,ARRAY_A);
		//print_r($result);
		foreach ($result as $row){
			array_push($content,$row['content']);
			array_push($answ_id,$row['answer_id']);
		}
		//print_r($answ_id);
		$count = array_count_values($answ_id);
		//print_r($count);
		$uniq_val = array();
		$uniq_id = array();
		//$uniq_val = array_unique($content);
		//print_r(count(array_unique($content)));
		//print_r(array_unique($content));
		$t = 0;
		$a = 0;
		for($z = 0; $z < count(array_unique($content)); $z++){
			while(!isset(array_unique($content)[$t])){
				$t = $t + 1;
				//print_r(array_unique($content)[$t]);
			}
			//print_r($t);
			//print_r(array_unique($content)[$t]);
			array_push($uniq_val,array_unique($content)[$t]);
			$t = $t + 1;
		}
		for($l = 0; $l < count(array_unique($answ_id)); $l++){
			while(!isset(array_unique($answ_id)[$a])){
				$a = $a + 1;
				//print_r(array_unique($answ_id)[$a]);
			}
			//print_r($a);
			//print_r(array_unique($answ_id)[$a]);
			array_push($uniq_id,array_unique($answ_id)[$a]);
			$a = $a + 1;
		}
		//print_r($content);
		//print_r($uniq_val); 
		$dataPoints = array();
		//print_r($uniq_val[3]);
		//print_r(count($uniq_val));
		//print_r(count(array_unique($content)));
		$m = 0;
		//print_r($uniq_id);
		for ($k = 0; $k < count($uniq_val); $k++){
			while(!isset($uniq_val[$m])){
				$m = $m + 1;
			}
			$push = array("label"=>$uniq_val[$m],"y"=>$count[$uniq_id[$m]]);
			//print_r($push);
			array_push($dataPoints,$push);
			$m = $m + 1;
		}
		//print_r($dataPoints);
		?>
		<div id="chartContainer<?php echo $n; ?>" style="width: 45%;height: 300px;display: inline-block;margin-left: 55px;"></div>	
		<script>
				console.log("Rendering !!!");
				var chart<?php echo $n; ?> = new CanvasJS.Chart("chartContainer<?php echo $n; ?>", {
					animationEnabled: true,
					exportEnabled: true,
					title:{
						text: "All Answer of Question number <?php echo $n; ?> :"
					},
					data: [{
						type: "pie",
						showInLegend: "true",
						legendText: "{label}",
						indexLabelFontSize: 16,
						indexLabel: "{label} - #percent%",
						yValueFormatString: "฿#,##0",
						dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
					}]
				});
				chart<?php echo $n; ?>.render();
		</script>
		<?php $n = $n + 1;
	}
}

function wpdocs_plugin_display_callback($object)
{
	?>
	<p class="get_id" id="<?php global $post;/**echo $post->ID;*/ echo $post_id = 86 ?>"></p>
	<?php
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");
    ?>
		<div>
			<li><a style="color:black;">Click add button to add question.</a>		
			<input type="image" class="add" src="/wp-content/plugins/QUESTIONS AND ANSWER/Plus.jpg" onclick="add_quiz();" style="height: 20px;"></li>
			<script>var max = 0;var temp = 0;var del = 0;var num_quiz = 0; var num_answ = 0; var count_add = 0; var all_id_quiz = new Array; var all_id_answ = new Array; var post_id; var quiz = new Array; var answ = new Array; var check_1 = new Array;</script>
			<ul id="demo"></ul>
			<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
			<script>
			post_id = document.getElementsByClassName("get_id")[0].id;
			console.log(post_id);
			function add_quiz(){
				window.num_quiz = window.num_quiz + 1 ;
				window.count_add = window.count_add + 1;
				//Create question
				var x = document.createElement("INPUT"); //create input tag for quiz
				var li = document.createElement("LI"); //create li tag for quiz
				var del_quiz = document.createElement("INPUT"); //create remove quiz button
				del_quiz.setAttribute("type","image");
				del_quiz.setAttribute("src","/wp-content/plugins/QUESTIONS AND ANSWER/Remove.png");
				del_quiz.setAttribute("onclick","del_quiz(this.id)");
				del_quiz.setAttribute("style","height: 25px;");
				//-----------------------------------------------------------------------------------
				var quiz = document.createElement("A");
				quiz.setAttribute("id","ID" + window.num_quiz);
				quiz.setAttribute("style","color:black;");
				var quiz_1 = document.createTextNode("Your question is : ");
				quiz.appendChild(quiz_1); // Append quiz to <a> tag
				//-----------------------------------------------------------------------------------
				x.setAttribute("id","ID_" + window.num_quiz );
				del_quiz.setAttribute("id",window.num_quiz );
				x.setAttribute("type", "text");
				x.setAttribute("style","width: 300px;");
				li.appendChild(quiz); // append quiz to <li> tag
				document.getElementById("demo").appendChild(li); // Quiz will be created firts
				document.getElementById("demo").appendChild(x);	   // Input will be created second
				document.getElementById("demo").appendChild(del_quiz);	// Remove button will be created third
				//------------------------------------------------------------------------------------
				var check_exists = document.getElementById("Submit_button");
				if(!check_exists){
					add_button();
				}
				else {}				
				//------------------------------------------------------------------------------------
				var n = 4;
				for (i=1; i<=n; i++){
					add_answ();
				}
				all_id_quiz.push("ID_" + window.num_quiz);
				//----------------------------------------------------------------------------------
				console.log("OK FOR ADD QUIZ !!");
				return false;
				//----------------------------------------------------------------------------------
			}
			function add_answ(){
				//Create answer
				window.num_answ = window.num_answ + 1;
				var y = document.createElement("INPUT"); //create input tag for answ
				var li_1 = document.createElement("LI"); //create li tag for answ
				var del_answ = document.createElement("INPUT"); // Create remove answ button
				del_answ.setAttribute("type","image");
				del_answ.setAttribute("src","/wp-content/plugins/QUESTIONS AND ANSWER/Remove.png");
				del_answ.setAttribute("onclick","del_answ(this.id)");
				del_answ.setAttribute("style","height: 25px;");
				//----------------------------------------------------------------------------------
				var answ = document.createElement("A");
				answ.setAttribute("id","Id" + window.num_answ);
				answ.setAttribute("style","color:black; margin-left:100px;");
				var answ_1 = document.createTextNode("Your answer is : ");
				answ.appendChild(answ_1); // Append answ to <a> tag
				//----------------------------------------------------------------------------------
				y.setAttribute("id","Id_" + window.num_answ );
				del_answ.setAttribute("id","Id__" + window.num_answ );
				y.setAttribute("type", "text");
				y.setAttribute("style","width: 300px; margin-left: 100px");
				li_1.appendChild(answ); // append answ to <li> tag
				document.getElementById("demo").appendChild(li_1); // Answ will be created firts
				document.getElementById("demo").appendChild(y);	   // Input will be created second
				document.getElementById("demo").appendChild(del_answ);	// Remove button will be created thirds
				//-------------------------------------------------------------------------------------
				all_id_answ.push("Id_" + window.num_answ);
				//------------------------------------------------------------------------------------
				console.log("OK FOR ADD ANSW !!");
				return false;
			}
			function add_button(){
				var sub_button = document.createElement("BUTTON");
				sub_button.innerHTML = "SUBMIT";
				sub_button.setAttribute("onclick","submit_123123() ");
				sub_button.setAttribute("style","right: 0; bottom: 0;");
				sub_button.setAttribute("id","Submit_button");
				document.getElementById("demo").appendChild(sub_button);
			}
			function del_quiz(clicked_id){
				var element = document.getElementById("demo");
				var del_quiz = document.getElementById("ID" + clicked_id);
				del_quiz.remove();
				//--------------------------------------------
				var del_quiz_1 = document.getElementById("ID_" + clicked_id);
				element.removeChild(del_quiz_1);
				//---------------------------------------------
				var del_quiz_2 = document.getElementById(clicked_id);
				element.removeChild(del_quiz_2);
				//---------------------------------------------
				var n_1 = Math.floor(clicked_id) * 4; // Rounded down clicked_id.
				for (i = n_1; i >= n_1 - 3; i--){
					del_answ(i);
					console.log(i);
				}
				console.log("OK FOR DEL QUIZ !!");
				window.count_add = window.count_add - 1;
				if (window.count_add == 0){
					console.log("ALL QUESTIONS HAVE BEEN DELETED !!");
					console.log("DELETING BUTTON SUBMIT !!");
					del_sub_button();
				}
				return false;
			}
			function del_answ(clicked_id){
				var element_1 = document.getElementById("demo");
				var str = ""+clicked_id;
				var count = str.length;
				console.log(count);
				if (count < 5 ) {
					var del_answ = document.getElementById("Id" + clicked_id);
					del_answ.remove();
					//--------------------------------------------
					var del_answ_1 = document.getElementById("Id_" + clicked_id);
					element_1.removeChild(del_answ_1);
					//---------------------------------------------
					var del_answ_2 = document.getElementById("Id__" + clicked_id);
					element_1.removeChild(del_answ_2);
					//---------------------------------------------
					console.log("OK FOR DEL ANSW !!");
				}
				else {
					var str_1 = str.slice(4);
					var clicked_id_processed = parseInt(str_1);
					var del_answ = document.getElementById("Id" + clicked_id_processed);
					del_answ.remove();
					//--------------------------------------------
					var del_answ_1 = document.getElementById("Id_" + clicked_id_processed);
					element_1.removeChild(del_answ_1);
					//---------------------------------------------
					var del_answ_2 = document.getElementById("Id__" + clicked_id_processed);
					element_1.removeChild(del_answ_2);
					//---------------------------------------------
					surplus = clicked_id_processed%4;
					console.log(surplus);
					//check dell all answ yet
					if (surplus == 1){
						var clicked_id_processed_1 = clicked_id_processed;
						for (var n = 1; n <= 3; n++){
							clicked_id_processed_1 = clicked_id_processed_1 + 1;
							var check = document.getElementById("Id__"+clicked_id_processed_1);
							if(check == null){
								check_1[n] = 0;
							}
							else if (check != null){
								check_1[n] = 1;
							}
						}
						check_1[0]=0;
						max = clicked_id_processed + 3;
					}
					else if (surplus == 2){
						var clicked_id_processed_1 = clicked_id_processed;
						var clicked_id_processed_2 = clicked_id_processed;
						for (var n = 0; n <= 0; n++){
							clicked_id_processed_1 = clicked_id_processed_1 - 1;
							var check = document.getElementById("Id__"+clicked_id_processed_1);
							if(check == null){
								check_1[n] = 0;
							}
							else if (check != null){
								check_1[n] = 1;
							}
						}
						for (var n = 2; n <=3; n++){
							clicked_id_processed_2 = clicked_id_processed_2 + 1;
							var check_2 = document.getElementById("Id__"+clicked_id_processed_2);
							if(check_2 == null){
								check_1[n] = 0;
							}
							else if (check_2 != null){
								check_1[n] = 1;
							}
						}
						check_1[1]=0;
						max = clicked_id_processed +2;
					}
					else if (surplus == 3){
						var clicked_id_processed_1 = clicked_id_processed;
						var clicked_id_processed_2 = clicked_id_processed;
						console.log(clicked_id_processed_1);
						console.log(clicked_id_processed_2);
						for (var n = 3; n <= 3; n++){
							clicked_id_processed_1 = clicked_id_processed_1 + 1;
							var check = document.getElementById("Id__"+clicked_id_processed_1);
							if(check == null){
								check_1[n] = 0;
							}
							else if (check != null){
								check_1[n] = 1;
							}
						}
						for (var n = 1; n >=0; n--){
							clicked_id_processed_2 = clicked_id_processed_2 - 1;
							var check_2 = document.getElementById("Id__"+clicked_id_processed_2);
							if(check_2 == null){
								check_1[n] = 0;
							}
							else if (check_2 != null){
								check_1[n] = 1;
							}
						}
						check_1[2]=0;
						max = clicked_id_processed +1;
					}
					else if (surplus == 0){
						var clicked_id_processed_1 = clicked_id_processed;
						for (var n = 0; n <= 2; n++){
							clicked_id_processed_1 = clicked_id_processed_1 - 1;
							var check = document.getElementById("Id__"+clicked_id_processed_1);
							if(check == null){
								check_1[n] = 0;
							}
							else if (check != null){
								check_1[n] = 1;
							}
							//console.log(clicked_id_processed_1);
						}
						check_1[3]=0;
						max = clicked_id_processed;
					}
					var z = 0;
					console.log(check_1);
					for ( var n = 0; n <= 3; n++) {
						while (check_1[n] == 1){
							console.log("BREAK!!!");
							break;
						}
						while (check_1[n] == 0){
							z = z+1;
							console.log(z);
							//console.log(max);
							if (z == 4){
								temp = max/4;
								//console.log(temp);
								window.count_add = window.count_add - 1;
								if (window.count_add == 0){
									console.log("ALL QUESTIONS HAVE BEEN DELETED !!");
									console.log("DELETING BUTTON SUBMIT !!");
									del_sub_button();
								}
								del_quiz(temp);
							}
							break;
						}
					}
					
				}
				return false;
			}
			function del_sub_button(){
				var element_2 = document.getElementById("demo");
				var del_button = document.getElementById("Submit_button");
				element_2.removeChild(del_button);
				console.log("OK FOR DEL BUTTON !!");
				return false;
			}
			function submit_123123() {
				console.log("Submited !!!");
				for (var i = 0; i <= all_id_quiz.length - 1; i++){
					quiz[i] = $("#" + all_id_quiz[i]).val();
					console.log(quiz[i]);
				}

				for (var z = 0; z <= all_id_answ.length - 1; z++){
					answ[z] = $("#" + all_id_answ[z]).val();
					console.log(answ[z]);
				}
				var jsonString_quiz = JSON.stringify(quiz);
				var jsonString_answ = JSON.stringify(answ);
				var jsonString_all_id_quiz = JSON.stringify(all_id_quiz);
				var jsonString_all_id_answ = JSON.stringify(all_id_answ);
				$.ajax({
					type: "POST",
					url: "/wp-content/plugins/QUESTIONS AND ANSWER/submit.php",
					data: {quiz:jsonString_quiz, answ:jsonString_answ, all_id_quiz:jsonString_all_id_quiz, all_id_answ:jsonString_all_id_answ, post_id},
					
					success: function(insert_content) {
						alert("Add Question and Answer successful. Please check id at Question & Answer menu");
					},
					
					error: function(insert_content){
						alert("ERROR !!!");
					}
				});
				return false;
			}
			</script>
		</div>
    <?php
}
session_destroy();