<?php
	if(!isset($_FILES['EsFile'])||!isset($_FILES['EnFile'])){
		header("Location: index.html");
		die();
	}

	//header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=Subtitles.srt");

	function file_get_contents_utf8($fn) {
		$content = file_get_contents($fn);
		return mb_convert_encoding($content, 'UTF-8',
		mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
	}

	$enf = file_get_contents($_FILES['EnFile']['tmp_name']);
	$esf = file_get_contents_utf8($_FILES['EsFile']['tmp_name']);

	preg_match_all('/\d\d:\d\d:\d\d,\d*.*\d\d:\d\d:\d\d,\d*/', $enf, $matchesen);
	preg_match_all('/\d\d:\d\d:\d\d,\d*.*\d\d:\d\d:\d\d,\d*/', $esf, $matcheses);

	foreach ($matchesen[0] as $key1 => $valueen) {
		$FinalArr[$key1]['t'] = $valueen;
		$FinalArr[$key1]['en'] = "Hello";
		$FinalArr[$key1]['es'] = "Hola";
	}

	foreach ($matcheses[0] as $key2 => $valuees) {
		$match = false;
		foreach ($FinalArr as $key1 => $valueen) {
			if($valueen['t'] == $valuees){
				$match = true;
				break;
			}
		}
		if(!$match){
			$NewTime['t'] = $valuees;
			$NewTime['en'] = "Potatoe";
			$NewTime['es'] = "Patata";
			foreach ($FinalArr as $keyn => $valuen) {
				preg_match('/(\d\d):(\d\d):(\d\d,\d*)/', $valuees, $result1);
				preg_match('/(\d\d):(\d\d):(\d\d,\d*)/', $valuen['t'], $result2);
				$t1 = $result1[1]*10000000+$result1[2]*100000+(floatval(str_replace (',', '.', $result1[3]))*1000);
				$t2 = $result2[1]*10000000+$result2[2]*100000+(floatval(str_replace (',', '.', $result2[3]))*1000);
				if($t2 > $t1){
					array_splice($FinalArr, $keyn, 0, array($NewTime));
					break;
				}
			}
		}
	}

	foreach ($FinalArr as $pos => $Arr3) {
		foreach ($matchesen[0] as $key1 => $value1) {
			$result = "";
			if($Arr3['t'] == $value1){
				preg_match('/'.$value1.'(((\n|\r).*){0,5})(\n|\r)\d{1,4}(\n|\r)/', $enf, $matchen);
				if(isset($matchen[1])){
					$result = str_replace("\n", " ", $matchen[1]);
					$result = trim($result);
					$FinalArr[$pos]['en'] = $result;
					break;
				}else{
					//echo("WTF?\n");
				}
			}
		}

		foreach ($matcheses[0] as $key2 => $value2) {
			$result2 = "";
			if($Arr3['t'] == $value2){
				preg_match('/'.$value2.'(((\n|\r).*){0,5})(\n|\r)\d{1,4}(\n|\r)/', $esf, $matches);
				if(isset($matches[1])){
					$result2 = str_replace("\n", " ", $matches[1]);
					$result2 = trim($result2);
					$FinalArr[$pos]['es'] = $result2;
					break;
				}else{
					//echo("QC?\n");
				}
			}
		}
	}

	foreach ($FinalArr as $key => $value) {
		if($value['es'] == "Patata"){
			$FinalArr[$key]['es'] = "EMPTY";
		}

		if($value['en'] == "Potatoe"){
			$FinalArr[$key]['en'] = "EMPTY";
		}

		if($value['es'] == "Hola"){
			unset($FinalArr[$key]);
		}

		if($value['en'] == "Hello"){
			$FinalArr[$key]['en'] = "EMPTY";
		}
	}

	$i = 1;
	foreach ($FinalArr as $key => $value) {
    if($i != 1){
      echo("\n");
    }
    echo($i."\n");
    echo($value['t']."\n");
    echo($value['en']."\n");
    echo($value['es']."\n");
		$i++;
	}
?>
