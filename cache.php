<?php
require('./main2.php');

if(isset($_POST['id']) && isset($_POST['vid'])){
	/*$user_query = $_POST['query'];
	$normal_query = queryNormalize($_POST['query']);
	$md5_query = md5($normal_query);
	echo "md5:$md5_query<br/>";*/
	$id = $_POST['id'];
	$vid = $_POST['vid'];

	//update cache db
	/*$cache_db_cmd = "/data2/public_html/cindera/vSearch/clickdb_code/editClickDB ";
	$cache_db_cmd = $cache_db_cmd . "-f \"/data2/public_html/cindera/vSearch/cache_db/cachedb_" . $md5_query . ".rec\" ";
	$cache_db_cmd = $cache_db_cmd . "-c -i \"" . $id . "\" ";
	$cache_db_cmd = $cache_db_cmd . "-d \"@\\n@GAIS_Rec:\\n\" ";
	echo "cache_db_cmd: " . $cache_db_cmd . "<br/>";
	/*echo shell_exec($cache_db_cmd);*/

	//cache the video
	$cache_video_cmd = "./youtube-video-fetch/yvideofetch ";
	$cache_video_cmd = $cache_video_cmd . "-k \"" . $vid . "\" ";
	$cache_video_cmd = $cache_video_cmd . "-s ./cacheFile/";
	echo "cache_video_cmd: " . $cache_video_cmd . "<br/>";
	shell_exec($cache_video_cmd);
}

?>
