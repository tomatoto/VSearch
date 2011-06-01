<?php
/********** PAGE **********/
function getRecAmount($res)
{
	$key = "number of matches: ";
	$str_pos = strstr($res, $key);
	$end_pos = strpos($str_pos, "\n");
	//echo "end_pos:" . $end_pos . "<br/>";
	$amount = substr($str_pos, strlen($key), $end_pos-strlen($key));
	//echo "str:" . $amount . "<br/>";

	return $amount;
}

function getPageAmount($total, $each)
{
	if($total%$each==0)
	{
		return floor($total/$each);
	}
	else{
		return floor($total/$each)+1;
	}
}

function pageLink($text, $pg, $cur)
{
	$link = "./index.php?" . "query=" . $text . "&page=" . $pg;
	if($pg != $cur){
		$link_str = "<a class='pageLink corner' href='" . $link . "' />" . $pg . "</a>";
	}
	else{
		$link_str = "<a class='pageLink-cur corner' />" . $pg . "</a>";
	}

	return $link_str;
}

function pageLink_nxtpre($text, $cur, $side)
{
	if($side==1){
		$pg = $cur-1;
		$link = "./index.php?" . "query=" . $text . "&page=" . $pg;
		$link_str = "<a class='pageLink corner' href='" . $link . "' />Pre</a>";
	}
	else{
		$pg = $cur+1;
		$link = "./index.php?" . "query=" . $text . "&page=" . $pg;
		$link_str = "<a class='pageLink corner' href='" . $link . "' />Next</a>";
	}
	return $link_str;
}

?>
