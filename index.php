<html>
<head>
<title>NUVideo</title>
<link rel="stylesheet" href="./css/layout.css" type="text/css">
<link rel="stylesheet" href="./css/style.css" type="text/css">
<link rel="stylesheet" href="./css/icon-style.css" type="text/css">
<link rel="stylesheet" href="./css/jquery-ui-1.8.13.custom.css" type="text/css">
<style>
#eq span {
	height:120px; float:left; margin:15px
}
.slide{
	display: block;
}
</style>
<script src="./jquery/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="./jquery/jquery-ui-1.8.11.custom.min.js" type="text/javascript"></script>
<script src="./jquery/cache.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
	$(".to-cached").click(function(){
		$(this).text("caching... ");	
		$.ajax({
			type:"POST",
			url:"./cache.php",
			data:$(this).attr("value"),
			dataType:"html",
			success:
				function(response){
					//alert("response:");
					//alert("response:"+response);
					$(this).addClass("cached");
					$(this).removeClass("to-cached");
					$(this).text("cached");
				}
		});

		$(this).toggleClass("cached");
		$(this).removeClass("to-cached");
		$(this).text("cached");
	});

});
$(function() {
	$(function() {
		$( "#accordion" ).accordion({
			collapsible: true
		});
	});

});
</script>
</head>
<?php
require('main2.php');
require('pagination.php');

function printItem($data, $q)
{
	/***** duration *****/
	$duration_str = reformDuration($data['duration']);

	/***** views *****/
	$view_str = reformViews($data['_vcount']);

	/***** publish date *****/
	//$publish_time_str = reformPublishTime($data['published']);
	$publish_time_str = $data['published'];

	echo "<div class='resultItem corner'>";
	echo "<div style='width:130px; height:100px; float: left; position:relative; margin-right:5px;'>";
	echo "<a href='./watch.php?query=" . $q . "&id=" . $data['GAIS_Rec'] ."'>";
	echo "<span class='duration'>".$duration_str."</span>";
	echo "<img class='thumbnail corner' src='".$data['thumb_link']."' />";
	echo "</a>";
	echo "</div>";

	echo "<div stype='width:300px; float: right; text-align: left; padding-top: 5px;'>";
	echo "<span class='category'>" . $data['category'] . "</span> ";
	if($data['cache']!=0){
		echo "<span class='cached'>cached</span>";
	}
	else{
		echo "<span class='to-cached' value='id=" . $data['GAIS_Rec'] . "&vid=" . $data['src_vid'] ."'>cached it!</span> ";
	}

	echo "<br/><a href='./watch.php?query=" . $q . "&id=" . $data['GAIS_Rec'] . "'>" . $data['title'] . "</a>";
	echo "<span class='category' style='float: right; background-color:#094390; margin-left:10px;'>" . $data['like'] . "</span> ";
	echo "<span class='category' style='float: right; background-color:#094390; margin-left:10px;'>" . $data['score'] . "</span> <br/>";
	echo "<span style='color: rgb(102,102,102); font-size:small;'>by " . $data['author'] . "</span><br/>";
	echo "<span style='color: rgb(102,102,102); font-size:small;'>" . $view_str . " views, ";
	echo $data['favoriteCount'] . " like this!</span><br/>";

	echo "<span></span>";
	echo "</div>";
	echo "<div class='clear'></div>";
	echo "</div>";
}

function my_sort($a)
{
	$array_size = count($a);
	for($i=1; $i<$array_size; $i++){
		for($j=1; $j<$array_size; $j++){
			if($a[$j]["score"]<$a[$j+1]["score"]){
				swap($a[$j]["PID"], 		$a[$j+1]["PID"]);
				swap($a[$j]["GAIS_Rec"], 	$a[$j+1]["GAIS_Rec"]);
				swap($a[$j]["id"], 		$a[$j+1]["id"]);
				swap($a[$j]["published"],	$a[$j+1]["published"]);
				swap($a[$j]["updated"],		$a[$j+1]["updated"]);
				swap($a[$j]["title"], 		$a[$j+1]["title"]);
				swap($a[$j]["content"],		$a[$j+1]["content"]);
				swap($a[$j]["author"], 		$a[$j+1]["author"]);
				swap($a[$j]["keyword"],		$a[$j+1]["keyword"]);
				swap($a[$j]["favoriteCount"],	$a[$j+1]["favoriteCount"]);
				swap($a[$j]["_vcount"],		$a[$j+1]["_vcount"]);
				swap($a[$j]["duration"],	$a[$j+1]["duration"]);
				swap($a[$j]["category"],	$a[$j+1]["category"]);
				swap($a[$j]["thumb_link"],	$a[$j+1]["thumb_link"]);
				swap($a[$j]["src_type"],	$a[$j+1]["src_type"]);
				swap($a[$j]["src_link"],	$a[$j+1]["src_link"]);
				swap($a[$j]["score"],		$a[$j+1]["score"]);
				swap($a[$j]["like"],		$a[$j+1]["like"]);
				swap($a[$j]["queryClick"],	$a[$j+1]["queryClick"]);
				swap($a[$j]["totalView"],	$a[$j+1]["totalView"]);
				swap($a[$j]["dislike"],		$a[$j+1]["dislike"]);
				swap($a[$j]["cache"],		$a[$j+1]["cache"]);
			}
		}
	}
	return $a;
}

function swap(&$a, &$b)
{
	$temp = $a;
	$a = $b;
	$b = $temp;
}

/**************************function list end ****************************/

if(isset($_GET['query']) && isset($_GET['page'])){
	$user_query = $_GET['query'];
	$normal_query = queryNormalize($_GET['query']);
	$md5_query = md5($normal_query);
	//echo "md5:$md5_query<br/>";
	$page = $_GET['page'];
}
?>
<body>
<div id="wrapper">
<div id="banner">
	<a href='./index.php'><img src='./pic/NUVideo_logo_v4.png' /></a>
	<form action="./index.php" method="get">
		<input type="text" size="50" name="query" value="<?php if(isset($user_query)){ echo $user_query;} ?>"/>
		<input type="hidden" name="page" value="1" />
		<input type="submit" value="Search" />
	</form>
</div>

<div id="contents">
<div id="info" class="corner">
<div id="accordion" class="infoItem">
	<h3><a href="#">Sorting options</a></h3>
	<div>
		A = cache<br/>
		B = queryView<br/>
		C = totalView<br/>
		D = likeRatio<br/><br/>
		sorting score:<br/>
		( A * <input type="text" name="" value="10000" size="2" maxlength="5"/> ) + 
		( B * <input type="text" name="" value="100" size="2" maxlength="5"/> ) + 
		( C * <input type="text" name="" value="10" size="2" maxlength="5"/> ) + 
		( D * <input type="text" name="" value="10" size="2" maxlength="5"/> )
	</div>
</div>
<div class="infoItem corner">
</div>
</div>
<div id="result">
<div class="top corner10">
</div>
<?php
$flag = 0;
if(isset($user_query) && isset($page) && isset($normal_query)){
	$flag = 1;

	/*****page handle*****/
	$list_beg = ($page-1)*10+1;
	$list_end = $page * 10;
	$list_str = $list_beg . "-" . $list_end;

	/*****get clickdb*****/
	$merge_clickdb_flag = 0;
	$clickdb_data = getClickData($md5_query);
	if($clickdb_data!=false && $page <= 5){
		$clickdb_amount=count($clickdb_data);
		//echo "clickdb_amount:" . $clickdb_amount . "<br/>";
		$list_str = "1-50";
		$merge_clickdb_flag = 1;
	}

	/*****search cmd*****/
	$cmd = "/data2/bin/gais -H \"/data/sw/index4\" -L " . $list_str . " -R \"" . $normal_query . "\"";
	//echo "Execute Command: <b>" . $cmd . "</b><br/>";
	$result = shell_exec($cmd);
	//echo $result;

	/*****exam page handle*****/
	$recs_amount = getRecAmount($result);
	$page_amount = getPageAmount($recs_amount, 10);
	//echo $recs_amount;
	if($page > $page_amount){
		echo "wrong page access!\n";
	}
	else{
?>

<!--<div class="top corner10">
	<span style="font-size:18pt; color:#666666;"> Search Results of: </span>
	<span style="font-size:18pt; font-weight: bold; "><?php //if(isset($user_query)){ echo $user_query;} ?> </span><br/>-->
	<!--category:<br/>
	related keys:-->
	<!--<div id="slider-range"></div>
</div>-->
<?php
		//arsort($clickdb_data);
		if($merge_clickdb_flag == 1){
			$cnt = 1;
			$limit = 50;
		}
		else{
			$cnt = 1;
			$limit = 10;
		}
		$click_count = 0;

		$rec_DATA = result2recDATA($result);
		for(; $cnt<=$limit; $cnt++){
			$rec_meta[$cnt] = getDataByYoutubeAPI($rec_DATA[$cnt]["id"]);
			if($rec_meta[$cnt]!=false){
				$rec_DATA[$cnt]["thumb_link"] = $rec_meta[$cnt]["thumb_link"];
				$rec_DATA[$cnt]["src_type"] = $rec_meta[$cnt]["src_type"];
				$rec_DATA[$cnt]["src_link"] = $rec_meta[$cnt]["src_link"];
				$rec_DATA[$cnt]["src_vid"] = $rec_meta[$cnt]["src_vid"];
			}
			/*****clickdb*****/
			$rec_DATA[$cnt]["queryClick"] = 0;
			if($merge_clickdb_flag == 1 && $click_count < $clickdb_amount && isset($clickdb_data)){
				foreach($clickdb_data as $key => $value){
					if( strcmp($key,$rec_DATA[$cnt]["GAIS_Rec"])==0 ){
						$rec_DATA[$cnt]["queryClick"] = $value;
						$click_count ++;
						unset($clickdb_data[$key]);
						break;
					}
				}
			}

			/*****cache*****/
			$rec_DATA[$cnt]["cache"] = 0;
			$cache_file_name = "./cacheFile/" . $rec_DATA[$cnt]['src_vid'] . ".flv";
			if(file_exists($cache_file_name)){
				$rec_DATA[$cnt]["cache"] = 1;
			}else if(file_exists("./cacheFile/" . $rec_DATA[$cnt]['src_vid'] . ".mp4")){
				$rec_DATA[$cnt]["cache"] = 2;
			}

			/*****like*****/
			$rec_DATA[$cnt]["like"] = getLikeData($rec_DATA[$cnt]["GAIS_Rec"]);
			$rec_DATA[$cnt]["dislike"] = getDislikeData($rec_DATA[$cnt]["GAIS_Rec"]);
			$rec_DATA[$cnt]["totalView"] = getTotalviewData($rec_DATA[$cnt]["GAIS_Rec"]);

			$rec_DATA[$cnt]["score"] = $rec_DATA[$cnt]["cache"] * 10000;
 			$rec_DATA[$cnt]["score"] = $rec_DATA[$cnt]["score"] + $rec_DATA[$cnt]["queryClick"] * 100;
			$rec_DATA[$cnt]["score"] = $rec_DATA[$cnt]["score"] + $rec_DATA[$cnt]["totalView"] * 10;
			if(($rec_DATA[$cnt]["like"]+$rec_DATA[$cnt]["dislike"])!=0){
				$rec_DATA[$cnt]["score"] = $rec_DATA[$cnt]["score"] + $rec_DATA[$cnt]["like"]/($rec_DATA[$cnt]["like"]+$rec_DATA[$cnt]["dislike"]) * 10;
			}
		}
		//echo nl2br(print_r($rec_DATA,true));

		if($merge_clickdb_flag == 1){	
			//sort
			$rec_DATA = my_sort($rec_DATA);
		}

		//print
		if($merge_clickdb_flag == 1){
			$cnt = $list_beg;
			$limit = $list_end;
		}
		else{
			$cnt = 1;
			$limit = 10;
		}
		for(; $cnt<=$limit; $cnt++){
			if($rec_meta[$cnt] == "false"){
				echo "null<br/>";
				//$limit++;
			}
			else{
				printItem($rec_DATA[$cnt], $user_query);//$rec_data is the data from record, $rec_meta is the data from id and youtube api
			}
		}	
	}
}
else{
	echo "Try type something in the search bar and search!<br/>";
}
?>
</div>
<!--<div id="info" class="corner">
<div id="accordion" class="infoItem">
	<h3><a href="#">Sorting options</a></h3>
	<div>
	</div>
</div>
<div class="infoItem corner">
</div>
</div>-->
<div class="clear">
<!--12345-->
<?php
if($flag == 1){
	if($page_amount <= 10){
		if($page>1){
			$pageLink = pageLink_nxtpre($user_query, $page, 1);
			echo $pageLink;
		}
		for($i=1; $i<$page_amount; $i++){
			echo "[$i] ";
		}
		if($page<$page_amount){
			$pageLink = pageLink_nxtpre($user_query, $page, 0);
			echo $pageLink;
		}
		echo "<br/>";
	}
	else{
		if($page < 4){
			if($page>1){
				$pageLink = pageLink_nxtpre($user_query, $page, 1);
				echo $pageLink;
			}
			for($i=1; $i<=7; $i++){
				$pageLink = pageLink($user_query, $i, $page);
				echo $pageLink;
			}
			if($page<$page_amount){
				$pageLink = pageLink_nxtpre($user_query, $page, 0);
				echo $pageLink;
			}
		}
		else if($page<=$page_amount && $page>($page_amount-4)){
			if($page>1){
				$pageLink = pageLink_nxtpre($user_query, $page, 1);
				echo $pageLink;
			}
			for($i=$page-3; $i<=$page_amount; $i++){
				$pageLink = pageLink($user_query, $i, $page);
				echo $pageLink;
			}
			if($page<$page_amount){
				$pageLink = pageLink_nxtpre($user_query, $page, 0);
				echo $pageLink;
			}
		}
		else{
			if($page>1){
				$pageLink = pageLink_nxtpre($user_query, $page, 1);
				echo $pageLink;
			}
			for($i=$page-3; $i<=$page+3; $i++){
				$pageLink = pageLink($user_query, $i, $page);
				echo $pageLink;
			}
			if($page<$page_amount){
				$pageLink = pageLink_nxtpre($user_query, $page, 0);
				echo $pageLink;
			}
		}
	}
}
?>
</div>
</div>

<div id="footer">
&copy; 2011 GAIS Lab 
</div>

</div>
</body>
</html>
