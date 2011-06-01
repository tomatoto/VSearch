<?php
require('main2.php');

function printWatchLayout($data, $cache, $likeFlag, $uip)
{
	/***** duration *****/
	$duration_str = reformDuration($data['duration']);

	/***** views *****/
	$view_str = reformViews($data['_vcount']);

	/***** publish date *****/
	//$publish_time_str = reformPublishTime($data['published']);
	$publish_time_str = $data['published'];

	echo "<div style='margin:0px;'>";
		echo "<div style='width: 640px; margin-bottom: 15px;'>";
			echo "<span style='font-size: 22px; color:#094390; font-weight: bold; '>" . $data['title'] . "</span><br/>";
			echo "<span style='width:'>by <a>" . $data['author'] . "</a> </span>";
			echo "<span style='color: #666666;'>" . $publish_time_str . "</span>";
			if($cache != 0){
				echo "<span class='cached'>cached</span>";
			}
			else{
				echo "<span class='to-cached' value='id=" . $data['GAIS_Rec'] . "&vid=" . $data['src_vid'] ."'>cached it!</span> ";
			}
			echo "</div>";
		echo "<div style='width: 640px; margin: 5px 0;'>";
			if($cache == 0){
			echo "<object style='height: 390px; width: 640px'>";
			echo "<param name='movie' value='".$data['src_link']./*"&autoplay=1&version=3*/"' >";
			echo "<param name='allowScriptAccess' value='always'>";
			echo "<param name='allowFullScreen' value='true'>";
			echo "<embed src='" . $data['src_link']  /*."&autoplay=1&version=3'*/ ." type='" . $data['src_type'] ."'allowScriptAccess='always' allowfullscreen='true' width='640' height='390'></embed>";
			echo "</object>";
			}
			else if($cache == 1){
				$vid = getYoutubeVid($data['src_link']);
		       		echo "<object type='application/x-shockwave-flash' data='./flv-player/player_flv_maxi.swf' style='width:640px; height:480px;' >";
				echo "<param name='movie' value='./flv-player/player_flv_maxi.swf' />";
			        echo "<param name='allowFullScreen' value='true' />";
				echo "<param name='FlashVars' value='flv=http://irc.ccu.edu.tw/~cindera/vSearch/cacheFile/" . $vid . ".flv";
				echo "&amp;width=640&amp;height=480&amp;margin=10&amp;bgcolor1=666666&amp;bgcolor2=666666&amp;showvolume=1&amp;showtime=1&amp;showfullscreen=1&amp;playertimeout=3000&amp;playeralpha=75&amp;loadingcolor=00c7a1&amp;buttonovercolor=4ECDC4&amp;slidercolor1=888888&amp;sliderovercolor=4ECDC4&amp;showiconplay=1&amp;iconplaybgcolor=ffffff&amp;iconplaybgalpha=25&amp;showtitleandstartimage=1' />";
		 		echo "</object>";
			}
			else{
				$vid = getYoutubeVid($data['src_link']);
		       		echo "<object type='application/x-shockwave-flash' data='./flv-player/player_flv_maxi.swf' style='width:640px; height:480px;' >";
				echo "<param name='movie' value='./flv-player/player_flv_maxi.swf' />";
			        echo "<param name='allowFullScreen' value='true' />";
				echo "<param name='FlashVars' value='flv=http://irc.ccu.edu.tw/~cindera/vSearch/cacheFile/" . $vid . ".mp4";
				echo "&amp;width=640&amp;height=480&amp;margin=10&amp;bgcolor1=666666&amp;bgcolor2=666666&amp;showvolume=1&amp;showtime=1&amp;showfullscreen=1&amp;playertimeout=3000&amp;playeralpha=75&amp;loadingcolor=00c7a1&amp;buttonovercolor=4ECDC4&amp;slidercolor1=888888&amp;sliderovercolor=4ECDC4&amp;showiconplay=1&amp;iconplaybgcolor=ffffff&amp;iconplaybgalpha=25&amp;showtitleandstartimage=1' />";
		 		echo "</object>";
			}
		echo "</div>";
		echo "<div id='options' style='line-height:30px;'>";
		echo "<span id='like_radio'>";
		echo "<input type='radio' id='like_radio1' name='radio' value='id=" . $data['GAIS_Rec'] . "&uip=" . $uip . "' ";
		if($likeFlag == 1){
			echo " checked='checked' ";
		}
		echo "/>";
		echo "<label for='like_radio1'>Like</label>";
		echo "<input type='radio' id='like_radio2' name='radio' value='id=" . $data['GAIS_Rec'] . "&uip=" . $uip . "' ";
		if($likeFlag == -1){
			echo " checked='checked' ";
		}
		echo "/>";
		echo "<label for='like_radio2'>Dislike</label>";
		echo "</span>";
		if($cache ==0){
			echo "<button class='mycache' value='id=" . $data['GAIS_Rec'] . "&vid=" . $data['src_vid'] ."'>to cache</button>";
		}
		else{
			echo "<span class='ui-button ui-widget ui-state-hover ui-corner-all' style='padding: 6px 6px; line-height:1.1; cursor:text;'>cached</span>";
		}
		echo "<span style='font-size: x-large; font-weight: bold; color: #666666; float:right;'>" . $data['totalView'] . "</span><br/></div>";
		echo "<div id='tabs' style='width:640px; margin: 15px 0;'>";
			echo "<ul>";
			echo "<li><a href='#tabs-1'>Description</a></li>";
			echo "<li><a href='#tabs-2'>Key</a></li>";
			echo "<li><a href='#tabs-3'>Comment</a></li>";
			echo "<li><a href='#tabs-4'>...</a></li>";
			//echo "<li style='float: right; width:auto; position: relative; ' class='corner'><a>" . $view_str . " views</a></li>";
			echo "</ul>";
			echo "<div id='tabs-1'>" . nl2br($data['content']) . "</div>";
			echo "<div id='tabs-2'>" . $data['keyword'] . "</div>";
			echo "<div id='tabs-3'>" . "</div>";
			echo "<div id='tabs-4'>" . "</div>";
		echo "</div>";
	echo "</div>";
}

if(isset($_GET['id'])){
	$id = $_GET['id'];
	if(isset($_GET['query'])){
		$user_query = $_GET['query'];
		$normal_query = queryNormalize($_GET['query']);
		$md5_query = md5($normal_query);
	}
	/*if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	}
	else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else{
		$ip=$_SERVER['REMOTE_ADDR'];
	}*/
	$uip=$_SERVER['REMOTE_ADDR'];
	$HOME_PATH = "/data2/public_html/cindera/vSearch/";
	$CMD_REC_DELIMETER = "@\\n@GAIS_Rec:\\n";

/********** append new rec : query_click_log **********/
	$click_log_rec = "@\n@GAIS_Rec:\n";
	$click_log_rec = $click_log_rec . "@query:" . $md5_query . "\n";
	$click_log_rec = $click_log_rec . "@VID:" . $id . "\n";
	$click_log_rec = $click_log_rec . "@time:" . date("Y-m-d") . "T" . date("H:i:s") . "\n";
	$click_log_rec = $click_log_rec . "@uip:" . $ip . "\n";
	//echo "click_log_rec = " . $click_log_rec . "<br/>";

	$filename = $HOME_PATH . "db/query_click_log/qv_log.rec";
	$fp = fopen($filename, 'a');
	if($fp!=NULL){
		fwrite($fp, $click_log_rec);
		fclose($fp);
	}
/******************************************************/

/********** update new rec : query_click_db ***********/
	$filename = $HOME_PATH . "db/query_click_db/qClick_" . $md5_query . ".rec";
	if(!file_exists($filename)){
		//open new file
		//echo "need to open new file<br/>";
		$qClick_db_rec = "@\n@GAIS_Rec:\n";
		$qClick_db_rec = $qClick_db_rec . "@VID:" . $id . "\n";
		$qClick_db_rec = $qClick_db_rec . "@click_count:1\n";
		//echo "click_db_rec = " . $qClick_db_rec . "<br/>";

		$fp = fopen($filename, 'a');
		if($fp!=NULL){
			fwrite($fp, $qClick_db_rec);
			fclose($fp);
		}
	}
	else{
		//echo "file exist<br/>";
		$query_vClick_cmd = $HOME_PATH . "program/editdb_code/editDB ";
		$query_vClick_cmd = $query_vClick_cmd . "-s ";
		$query_vClick_cmd = $query_vClick_cmd . "-f \"" . $filename . "\" ";
		$query_vClick_cmd = $query_vClick_cmd . "-d \"" . $CMD_REC_DELIMETER . "\" ";
		$query_vClick_cmd = $query_vClick_cmd . "-q \"@VID:" . $id . "\\n\" " . "-c \"@click_count:\" ";
		//echo "query_vClick_cmd:" . $query_vClick_cmd . "<br/>";
		$result =  shell_exec($query_vClick_cmd);
		if(strstr($result, "@result:E")){
			//echo "click count++ success!<br/>";
		}
		else{
			if(strstr($result, "@result:B")){
				//append new record
				//echo "need to append new record<br/>";
				$qClick_db_rec = "@\n@GAIS_Rec:\n";
				$qClick_db_rec = $qClick_db_rec . "@VID:" . $id . "\n";
				$qClick_db_rec = $qClick_db_rec . "@click_count:1\n";
				//echo "click_db_rec = " . $qClick_db_rec . "<br/>";

				$fp = fopen($filename, 'a');
				if($fp!=NULL){
					fwrite($fp, $qClick_db_rec);
					fclose($fp);
				}
			}
		}
	}
/******************************************************/

/************* update new rec : video_db **************/
	$filename = $HOME_PATH . "db/video_db/video_db.rec";
	if(!file_exists($filename)){
		//open new file
		//echo "need to open new file<br/>";
		$video_db_rec = "@\n@GAIS_Rec:\n";
		$video_db_rec = $video_db_rec . "@VID:" . $id . "\n";
		$video_db_rec = $video_db_rec . "@click_count:1\n";
		$video_db_rec = $video_db_rec . "@like_count:0\n";
		$video_db_rec = $video_db_rec . "@dislike_count:0\n";
		$video_db_rec = $video_db_rec . "@cache:0\n";
		//echo "video_db_rec = " . $video_db_rec . "<br/>";

		$fp = fopen($filename, 'a');
		if($fp!=NULL){
			fwrite($fp, $video_db_rec);
			fclose($fp);
		}
	}
	else{
		//echo "file exist<br/>";
		$video_db_cmd = $HOME_PATH . "program/editdb_code/editDB ";
		$video_db_cmd = $video_db_cmd . "-s  ";
		$video_db_cmd = $video_db_cmd . "-f \"" . $filename . "\" ";
		$video_db_cmd = $video_db_cmd . "-d \"" . $CMD_REC_DELIMETER . "\" ";
		$video_db_cmd = $video_db_cmd . "-q \"@VID:" . $id . "\\n\" " . "-c \"@click_count:\" ";
		//echo "video_db_cmd:" . $video_db_cmd . "<br/>";
		$result =  shell_exec($video_db_cmd);
		if(strstr($result, "@result:E")){
			//echo "click count++ success!<br/>";
		}
		else{
			if(strstr($result, "@result:B")){
				//append new record
				//echo "need to append new record<br/>";
				$video_db_rec = "@\n@GAIS_Rec:\n";
				$video_db_rec = $video_db_rec . "@VID:" . $id . "\n";
				$video_db_rec = $video_db_rec . "@click_count:1\n";
				$video_db_rec = $video_db_rec . "@like_count:0\n";
				$video_db_rec = $video_db_rec . "@dislike_count:0\n";
				$video_db_rec = $video_db_rec . "@cache:0\n";
				//echo "video_db_rec = " . $video_db_rec . "<br/>";

				$fp = fopen($filename, 'a');
				if($fp!=NULL){
					fwrite($fp, $video_db_rec);
					fclose($fp);
				}
			}
		}
	}

/******************************************************/
}

$flag = 0;
if(isset($id)){
	$flag = 1;

	$cmd = "/data2/bin/gais -H \"/data/sw/index4\" -L 1-10 -R \"" . $id . "\"";
	//echo "Execute Command: <b>" . $cmd . "</b><br/>";
	$result = shell_exec($cmd);
	//echo $result;
	
	$rec_string = result2recStr($result);
	$rec_data = recStr2recData($rec_string[1]);
	//echo nl2br(print_r($rec_data, true));
	$rec_meta = getDataByYoutubeAPI($rec_data["id"]);
	$rec_data['thumb_link'] = $rec_meta['thumb_link'];
	$rec_data['src_link'] = $rec_meta['src_link'];
	$rec_data['src_type'] = $rec_meta['src_type'];
	$rec_data['src_vid'] = $rec_meta['src_vid'];

	$cache_flag = 0;
	$cache_file_name = "./cacheFile/" . $rec_data['src_vid'] . ".flv";
	if(file_exists($cache_file_name)){
		$cache_flag = 1;
	}
	else if( file_exists("./cacheFile/" . $rec_data['src_vid'] . ".mp4") ){
		$cache_flag = 2;
	}

	$rec_data["like"] = getLikeData($rec_data["GAIS_Rec"]);
	echo $rec_data["like"] . "A<br/>";
	$rec_data["dislike"] = getDislikeData($rec_data["GAIS_Rec"]);
	echo $rec_data["dislike"] . "B<br/>";
	$rec_data["totalView"] = getTotalviewData($rec_data["GAIS_Rec"]);
	echo $rec_data["totalView"] . "C<br/>";
}
?>

<html>
<head>
<title>NUVideo - <?php echo $rec_data['title'];?></title>
<link rel="stylesheet" href="./css/layout.css" type="text/css">
<link rel="stylesheet" href="./css/style.css" type="text/css">
<link rel="stylesheet" href="./css/icon-style.css" type="text/css">
<!--<link rel="stylesheet" href="./css/jquery-ui.css" type="text/css">-->
<link rel="stylesheet" href="./css/jquery-ui-1.8.13.custom.css" type="text/css">
<!--<link rel="stylesheet" href="./my-jquery-ui.css" type="text/css">-->
<script src="./jquery/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="./jquery/jquery-ui-1.8.11.custom.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
	$(".to-cached").click(function(){
		$.ajax({
			type:"POST",
			url:"./cache.php",
			data:$(this).attr("value"),
			dataType:"html",
			success:
				function(response){
					//alert("response:");
					//alert("response:"+response);
					$(this).toggleClass("cached");
					$(this).removeClass("to-cached");
					$(this).text("cached");
				}
		});

		$(this).addClass("cached");
		$(this).removeClass("to-cached");
		$(this).unbind('click');
		$(this).text("cached");
	});
	
});

$(function() {
	$( "#tabs" ).tabs();
	$( "#like_radio" ).buttonset({
	});
	$( "#like_radio1" ).button({
		icons: {
			primary: "ui-icon-heart"
		}
	});
	$( "#like_radio1" ).click(function() {
		//alert($(this).attr("checked"));
		$.ajax({
			type:"POST",
			url:"./like.php",
			data:$(this).attr("value"),
			dataType:"html",
			success:
				function(response){
					//alert("response:");
					//alert("response:"+response);
				}
		});
	});
	$( "#like_radio2" ).click(function() {
		//alert($(this).attr("checked"));
		$.ajax({
			type:"POST",
			url:"./dislike.php",
			data:$(this).attr("value"),
			dataType:"html",
			success:
				function(response){
					//alert("response:");
					//alert("response:"+response);
				}
		});
	});
	$( ".mycache" ).button();
	$( ".mycache" ).click(function() {
		alert($(".mycache span").html());
		$(this).button({ disabled: true });
		$.ajax({
			type:"POST",
			url:"./cache.php",
			data:$(this).attr("value"),
			dataType:"html",
			success:
				function(response){
					//alert("response:");
					//alert("response:"+response);
					$(this).toggleClass("cached");
					$(this).removeClass("to-cached");
					$(this).text("cached");
				}
		});
	});

});

</script>
<style>
</style>

</head>
<body>
<div id="wrapper">
<div id="banner">
	<a href='./index.php'><img src='./pic/NUVideo_logo_v4.png' /></a>
	<form action="./index.php" method="get">
		<input class="searchbar-text" type="text" size="40" name="query" value="<?php if(isset($user_query)){ echo $user_query;} ?>"/>
		<input type="hidden" name="page" value="1" />
		<input class="searchbar-but" type="submit" value="Search" />
	</form>
</div>

<div id="contents">
<div id="result">
<?php
if($flag==1){
	echo "uip" . $uip . "<br/>";
	$filename = $HOME_PATH . "db/user_like_db/userDB_" . $uip . ".rec";
	if(file_exists($filename)){
		$do_you_like_this = $HOME_PATH . "program/editdb_code/editDB -f \"" . $filename . "\" ";
		$do_you_like_this = $do_you_like_this . "-d \"" . $CMD_REC_DELIMETER . "\" -g ";
		$do_you_like_this = $do_you_like_this . "-q \"@VID:" . $id . "\\n\" -c \"@like:\"";
		$result = shell_exec($do_you_like_this);
		//echo $result;
		if( ($temp=strstr($result, "@count:1"))!=NULL){
			$likeFlag = 1;
		}
		else{
			$do_you_dislike_this = $HOME_PATH . "program/editdb_code/editDB -f \"" . $filename . "\" ";
			$do_you_dislike_this = $do_you_dislike_this . "-d \"" . $CMD_REC_DELIMETER . "\" -g ";
			$do_you_dislike_this = $do_you_dislike_this . "-q \"@VID:" . $id . "\\n\" -c \"@dislike:\"";
			$result = shell_exec($do_you_dislike_this);
			//echo $result;
			if( ($temp=strstr($result, "@count:1"))!=NULL){
				$likeFlag = -1;
			}
		}
	}
	else{
		$likeFlag = 0;
	}
	//echo "likeFlag = " . $likeFlag . "<br/>";
	printWatchLayout($rec_data, $cache_flag, $likeFlag, $uip);//$rec_data is the data from record, $rec_meta is the data from id and youtube api
}
else{
	echo "Try type something in the search bar and search!<br/>";
}
?>
</div>
<div id="info" class="corner">
<!--<div class="top corner">
	<span style="font-size:18pt; color:#666666;"> Search Results of: </span>
	<span style="font-size:18pt; font-weight: bold; "><?php //if(isset($query)){ echo $query;} ?> </span><br/>
	category:<br/>
	related keys:
</div>-->
<div class="infoItem corner">
<b>Related Videos</b>
<?php
/*	$cmd_related = "/data2/bin/gais -H \"/data/sw/index4\" -L 1-12 -R \"" . $rec_data['title'] . "\"";
	//echo "Execute Command: <b>" . $cmd_related . "</b><br/>";
	$result_related = shell_exec($cmd_related);
	//echo $result_related;

	$rec_string = result2recStr($result_related);
	$cnt = 1;
	$limit = 6;
	for($cnt =1; isset($rec_string[$cnt]) && $cnt<=$limit; $cnt++){
		$rec_data = recStr2recData($rec_string[$cnt]);
		//echo nl2br(print_r($rec_data, true));
		$rec_meta = getDataByYoutubeAPI($rec_data["id"]);
		$rec_data['thumb_link'] = $rec_meta['thumb_link'];
		$rec_data['src_link'] = $rec_meta['src_link'];
		$rec_data['src_type'] = $rec_meta['src_type'];
		if($rec_meta == "false"){
			echo "null<br/>";
			//$limit++;
		}
		else{
			//printMiniItem($rec_data, $rec_data['title']);//$rec_data is the data from record, $rec_meta is the data from id and youtube api
		}
	}*/
?>
</div>
</div>
<div class="clear">
12345
</div>
</div>

<div id="footer">
&copy; 2011 GAIS Lab 
</div>

</div>
</body>
</html>
