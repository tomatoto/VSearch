<?php
/***********query***************/
function queryNormalize($ori)
{
	$q_lower = strtolower($ori);
	//echo "lower: $q_lower <br/>";
	$q_trim = trim($q_lower);
	//echo "trim: $q_trim <br/>";

	$q_nospace = "";
	$i=0;
	for($i=0; $i<strlen($q_trim); $i++){
		$q_nospace = $q_nospace . $q_trim[$i];
		if($q_trim[$i]==' ' || $q_trim[$i]=='\t' || $q_trim[$i]=='\n'){
			while($q_trim[$i+1]==' ' || $q_trim[$i+1]=='\t' || $q_trim[$i+1]=='\n'){
				$i=$i+1;
			}
		}
	}
	//echo "nospace: $q_nospace <br/>";

	$q_sorted = explode(" ", $q_nospace);
	sort($q_sorted);

	$normalize_query = "";
	for($i=0; isset($q_sorted[$i]); $i++){
		$normalize_query = $normalize_query . $q_sorted[$i] . " ";
	}
	$normalize_query = trim($normalize_query);
	//echo "normalize: $normalize_query <br/>";

	return $normalize_query;
}

function getTotalviewData($id)
{
	$fileName = "./db/video_db/video_db.rec";
	if( !file_exists($fileName) ){
		return false;
	}
	$fp = fopen($fileName, 'r');
	if($fp == NULL){
		return false;
	}

	$content = fread($fp, filesize($fileName));
	//echo $content;
	
	$tempArray = explode("@\n@GAIS_Rec:\n@", $content);
	for($i=1; isset($tempArray[$i]); $i++){
		$tempArray[$i] = recStr2recData($tempArray[$i]);
		$a = $tempArray[$i]["VID"];
		if(strcmp($a, $id)==0){
			fclose($fp);
			return $tempArray[$i]["click_count"];
		}
	}

	//var_dump($tempArray);
	fclose($fp);
	return 0;
}

function getDislikeData($id)
{
	$fileName = "./db/video_db/video_db.rec";
	if( !file_exists($fileName) ){
		return false;
	}
	$fp = fopen($fileName, 'r');
	if($fp == NULL){
		return false;
	}

	$content = fread($fp, filesize($fileName));
	//echo $content;
	
	$tempArray = explode("@\n@GAIS_Rec:\n@", $content);
	for($i=1; isset($tempArray[$i]); $i++){
		$tempArray[$i] = recStr2recData($tempArray[$i]);
		$a = $tempArray[$i]["VID"];
		if(strcmp($a, $id)==0){
			fclose($fp);
			return $tempArray[$i]["dislike_count"];
		}
		//echo $dbDataArray[$a] . "===<br/>";
	}

	//var_dump($dbDataArray);
	fclose($fp);
	return 0;
}

function getLikeData($id)
{
	$fileName = "./db/video_db/video_db.rec";
	if( !file_exists($fileName) ){
		return false;
	}
	$fp = fopen($fileName, 'r');
	if($fp == NULL){
		return false;
	}

	$content = fread($fp, filesize($fileName));
	//echo $content;
	
	$tempArray = explode("@\n@GAIS_Rec:\n@", $content);
	for($i=1; isset($tempArray[$i]); $i++){
		$tempArray[$i] = recStr2recData($tempArray[$i]);
		$a = $tempArray[$i]["VID"];
		if(strcmp($a, $id)==0){
			fclose($fp);
			return $tempArray[$i]["like_count"];
		}
		//echo $dbDataArray[$a] . "===<br/>";
	}

	//var_dump($dbDataArray);
	fclose($fp);
	return 0;
}

function getClickData($q)
{
	$fileName = "./db/query_click_db/qClick_" . $q . ".rec";
	if( !file_exists($fileName) ){
		//echo"++++++++++++++++++++++++";
		return false;
	}
	$fp = fopen($fileName, 'r');
	if($fp == NULL){
		return false;
	}

	$content = fread($fp, filesize($fileName));
	//echo $content;
	
	$tempArray = explode("@\n@GAIS_Rec:\n@", $content);
	for($i=1; isset($tempArray[$i]); $i++){
		$tempArray[$i] = recStr2recData($tempArray[$i]);
		$a = $tempArray[$i]["VID"];
		$dbDataArray[$a] = $tempArray[$i]["click_count"];

		//echo $dbDataArray[$a] . "===<br/>";
	}

	//var_dump($dbDataArray);
	fclose($fp);
	return $dbDataArray;
}

function getYoutubeVid($url)
{
	$top_pos = strpos($url,"/v/") + 3;
	$end_pos = strpos($url, "?");
	$length = $end_pos - $top_pos;

	$vid = substr($url, $url+$top_pos, $length);

	//echo $vid;
	return $vid;
}

/*************************rec handle************************/
function result2recDATA($str)
{
	$tempArray = explode("\n@PID:", $str);
	for($i=1; isset($tempArray[$i]); $i++){
		$recStr = "PID:" . $tempArray[$i];
		//echo $recStr[$i] . "<br/>=====<br/><br/>";
		$recArray[$i] = recStr2recData($recStr);
	}
	return $recArray;
}

function result2recStr($str)
{
	$tempArray = explode("\n@PID:", $str);
	for($i=1; isset($tempArray[$i]); $i++){
		$recStr[$i] = "PID:" . $tempArray[$i];
		//echo $recStr[$i] . "<br/>=====<br/><br/>";
	}
	return $recStr;
}

function recStr2recData($str)
{
	//echo $str . "<br/>~~~~~<br/><br/>";
	$tempArray = explode("\n@",$str);
	for($i=0; isset($tempArray[$i]); $i++){
		//echo $tempArray[$i] . "<br/>+++++<br/><br/>";
		$val_pos = strpos($tempArray[$i], ":", 0);
		$index = substr($tempArray[$i], 0, $val_pos);
		//echo $index . "<br/>";
		$value = trim(substr($tempArray[$i], $val_pos+1));
		//echo $value . "<br/>";
		$recData[$index]=$value;
	}
	return $recData;
}

function getDataByYoutubeAPI($id)
{
	$url = $id . "?alt=json";
	//echo $url . "<br/>";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$output = curl_exec($ch);
	curl_close($ch);
	//echo $output;
	if(strcmp($output,"Video not found")==0){
		return false;
	}
	
	$jsonArray = json_decode($output, true);
	//var_dump($jsonArray, true);

	if(is_array($jsonArray)){
		$meta['thumb_link'] = $jsonArray['entry']['media$group']['media$thumbnail'][1]['url'];
		for($i=0; isset($jsonArray['entry']['media$group']['media$content'][$i]['type']); $i++){
			if(strcmp($jsonArray['entry']['media$group']['media$content'][$i]['type'], "application/x-shockwave-flash")==0){
				$meta['src_type'] = $jsonArray['entry']['media$group']['media$content'][$i]['type'];
				$meta['src_link'] = $jsonArray['entry']['media$group']['media$content'][$i]['url'];
				break;
			}
		}
	}
	if(isset($meta['src_link'])){
		$meta['src_vid'] = getYoutubeVid($meta['src_link']);
	}
	else{
		//echo "no flash src\n";
		$meta['src_link']="null";
	}

	return $meta;
}

function reformDuration($str)
{
	if($str<60){
		$duration = "00:" . $str;
	}
	else if($str< (60*60)){
		$sec = ($str % 60);
		$min = ($str-$sec) / 60;
		if($min < 10 && $sec < 10){
			$duration = "0" . $min . ":0" . $sec;
		}
		else if($min < 10){
			$duration = "0" . $min . ":" . $sec;
		}
		else if($sec < 10){
			$duration = $min . ":0" . $sec;
		}
		else{
			$duration = $min . ":" . $sec;
		}
	}

	//$hr = ($str - $sec*60*60 - $min*60)/(60*60);

	return $duration;
}

function reformViews($str)
{
	$views = explode(" ", $str);
	return $views[0];		
}
