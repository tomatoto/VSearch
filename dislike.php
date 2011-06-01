<?php
require('./main2.php');

if(isset($_POST['id']) && isset($_POST['uip'])){
	$id = $_POST['id'];
	$uip = $_POST['uip'];

	$HOME_PATH = "/data2/public_html/cindera/vSearch/";
	$CMD_REC_DELIMETER = "@\\n@GAIS_Rec:\\n";

/************* update new rec : video_db **************/
/*	$filename = $HOME_PATH . "db/video_db/video_db.rec";
	if(!file_exists($filename)){
	}
	else{
		//echo "file exist<br/>";
		$CMD_setDislikeCount = $HOME_PATH . "program/editdb_code/editDB ";
		$CMD_setDislikeCount = $CMD_setDislikeCount . "-s ";
		$CMD_setDislikeCount = $CMD_setDislikeCount . "-f \"" . $filename . "\" ";
		$CMD_setDislikeCount = $CMD_setDislikeCount . "-d \"" . $CMD_REC_DELIMETER . "\" ";
		$CMD_setDislikeCount = $CMD_setDislikeCount . "-q \"@VID:" . $id . "\\n\" " . "-c \"@dislike_count:\" ";
		//echo "CMD_setDislikeCount:" . $CMD_setDislikeCount . "<br/>";
		$result = shell_exec($CMD_setDislikeCount);
		echo "result:" . $result . "\n";
	}*/
/******************************************************/
/*********** update new rec : user_like_db ************/
	$filename = $HOME_PATH . "db/user_like_db/userDB_" . $uip . ".rec";
	if(!file_exists($filename)){
		//open new file
		echo "need to open new file<br/>";
		$video_db_rec = "@\n@GAIS_Rec:\n";
		$video_db_rec = $video_db_rec . "@VID:" . $id . "\n";
		$video_db_rec = $video_db_rec . "@like:0\n";
		$video_db_rec = $video_db_rec . "@dislike:1\n";
		echo "video_db_rec = " . $video_db_rec . "<br/>";

		$fp = fopen($filename, 'a');
		if($fp!=NULL){
			fwrite($fp, $video_db_rec);
			fclose($fp);
		}

		/******************************************************/
			$filename_vdb = $HOME_PATH . "db/video_db/video_db.rec";
			$CMD_setDislikeCount = $HOME_PATH . "program/editdb_code/editDB ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-s ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-f \"" . $filename_vdb . "\" ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-d \"" . $CMD_REC_DELIMETER . "\" ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-q \"@VID:" . $id . "\\n\" " . "-c \"@dislike_count:\" ";
			//echo "CMD_setDislikeCount:" . $CMD_setDislikeCount . "<br/>";
			$result = shell_exec($CMD_setDislikeCount);
			echo "result:" . $result . "\n";
		/******************************************************/
	}
	else{
		$CMD_getLikeCount = $HOME_PATH . "program/editdb_code/editDB ";
		$CMD_getLikeCount = $CMD_getLikeCount . "-g ";
		$CMD_getLikeCount = $CMD_getLikeCount . "-f \"" . $filename . "\" ";
		$CMD_getLikeCount = $CMD_getLikeCount . "-d \"" . $CMD_REC_DELIMETER . "\" ";
		$CMD_getLikeCount = $CMD_getLikeCount . "-q \"@VID:" . $id . "\\n\" " . "-c \"@like:\" ";
		//echo "CMD_getLikeCount:" . $CMD_getLikeCount . "<br/>";
		$result_1 =  shell_exec($CMD_getLikeCount);
		echo "result_1: " . $result_1;

		$CMD_getDislikeCount = $HOME_PATH . "program/editdb_code/editDB ";
		$CMD_getDislikeCount = $CMD_getDislikeCount . "-g ";
		$CMD_getDislikeCount = $CMD_getDislikeCount . "-f \"" . $filename . "\" ";
		$CMD_getDislikeCount = $CMD_getDislikeCount . "-d \"" . $CMD_REC_DELIMETER . "\" ";
		$CMD_getDislikeCount = $CMD_getDislikeCount . "-q \"@VID:" . $id . "\\n\" " . "-c \"@dislike:\" ";
		//echo "CMD_getDislikeCount:" . $CMD_getDislikeCount . "<br/>";
		$result_2 =  shell_exec($CMD_getDislikeCount);
		echo "result_2: " . $result_2;

		$CMD_setDislikeCount = $HOME_PATH . "program/editdb_code/editDB ";
		$CMD_setDislikeCount = $CMD_setDislikeCount . "-s ";
		$CMD_setDislikeCount = $CMD_setDislikeCount . "-f \"" . $filename . "\" ";
		$CMD_setDislikeCount = $CMD_setDislikeCount . "-d \"" . $CMD_REC_DELIMETER . "\" ";
		$CMD_setDislikeCount = $CMD_setDislikeCount . "-q \"@VID:" . $id . "\\n\" " . "-c \"@dislike:\" ";
		//echo "CMD_setDislikeCount:" . $CMD_setDislikeCount . "<br/>";

		$CMD_unsetLikeCount = $HOME_PATH . "program/editdb_code/editDB ";
		$CMD_unsetLikeCount = $CMD_unsetLikeCount . "-u ";
		$CMD_unsetLikeCount = $CMD_unsetLikeCount . "-f \"" . $filename . "\" ";
		$CMD_unsetLikeCount = $CMD_unsetLikeCount . "-d \"" . $CMD_REC_DELIMETER . "\" ";
		$CMD_unsetLikeCount = $CMD_unsetLikeCount . "-q \"@VID:" . $id . "\\n\" " . "-c \"@like:\" ";
		//echo "CMD_unsetLikeCount:" . $CMD_unsetLikeCount . "<br/>";

		if(strstr($result_1, "@count:0") && strstr($result_2, "@count:0")){
			echo shell_exec($CMD_setDislikeCount);
			echo "%%%% like count++ success!<br/>";


		/******************************************************/
			$filename_vdb = $HOME_PATH . "db/video_db/video_db.rec";
			$CMD_setDislikeCount = $HOME_PATH . "program/editdb_code/editDB ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-s ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-f \"" . $filename_vdb . "\" ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-d \"" . $CMD_REC_DELIMETER . "\" ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-q \"@VID:" . $id . "\\n\" " . "-c \"@dislike_count:\" ";
			//echo "CMD_setDislikeCount:" . $CMD_setDislikeCount . "<br/>";
			$result = shell_exec($CMD_setDislikeCount);
			echo "result:" . $result . "\n";
		/******************************************************/
		}
		else if(strstr($result_1, "@count:0") && strstr($result_2, "@count:1")){
		}
		else if(strstr($result_1, "@count:1") && strstr($result_2, "@count:0")){
			echo shell_exec($CMD_setDislikeCount);
			echo shell_exec($CMD_unsetLikeCount);
			echo "%%%% like toggle success!<br/>";

		/******************************************************/
			$filename_vdb = $HOME_PATH . "db/video_db/video_db.rec";
			$CMD_setDislikeCount = $HOME_PATH . "program/editdb_code/editDB ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-s ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-f \"" . $filename_vdb . "\" ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-d \"" . $CMD_REC_DELIMETER . "\" ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-q \"@VID:" . $id . "\\n\" " . "-c \"@dislike_count:\" ";
			//echo "CMD_setDislikeCount:" . $CMD_setDislikeCount . "<br/>";
			$result = shell_exec($CMD_setDislikeCount);
			echo "result:" . $result . "\n";
		/******************************************************/
			$CMD_unsetLikeCount = $HOME_PATH . "program/editdb_code/editDB ";
			$CMD_unsetLikeCount = $CMD_unsetLikeCount . "-u ";
			$CMD_unsetLikeCount = $CMD_unsetLikeCount . "-f \"" . $filename_vdb . "\" ";
			$CMD_unsetLikeCount = $CMD_unsetLikeCount . "-d \"" . $CMD_REC_DELIMETER . "\" ";
			$CMD_unsetLikeCount = $CMD_unsetLikeCount . "-q \"@VID:" . $id . "\\n\" " . "-c \"@like_count:\" ";
			//echo "CMD_unsetLikeCount:" . $CMD_unsetLikeCount . "<br/>";
			$result = shell_exec($CMD_unsetLikeCount);
			echo "result:" . $result . "\n";
		/******************************************************/
		}
		else{
			echo "need to append new record<br/>";
			$video_db_rec = "@\n@GAIS_Rec:\n";
			$video_db_rec = $video_db_rec . "@VID:" . $id . "\n";
			$video_db_rec = $video_db_rec . "@like:0\n";
			$video_db_rec = $video_db_rec . "@dislike:1\n";
			echo "video_db_rec = " . $video_db_rec . "<br/>";

			$fp = fopen($filename, 'a');
			if($fp!=NULL){
				fwrite($fp, $video_db_rec);
				fclose($fp);
			}
			

		/******************************************************/
			$filename_vdb = $HOME_PATH . "db/video_db/video_db.rec";
			$CMD_setDislikeCount = $HOME_PATH . "program/editdb_code/editDB ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-s ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-f \"" . $filename_vdb . "\" ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-d \"" . $CMD_REC_DELIMETER . "\" ";
			$CMD_setDislikeCount = $CMD_setDislikeCount . "-q \"@VID:" . $id . "\\n\" " . "-c \"@dislike_count:\" ";
			//echo "CMD_setDislikeCount:" . $CMD_setDislikeCount . "<br/>";
			$result = shell_exec($CMD_setDislikeCount);
			echo "result:" . $result . "\n";
		/******************************************************/
		}
	}

/******************************************************/
}

?>
