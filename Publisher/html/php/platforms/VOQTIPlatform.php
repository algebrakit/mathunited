<?php
require_once("Logger.php");
require_once("EntityConverter.php");
class VOQTIPlatform extends Platform {
    
    //constructor 
    public function VOQTIPlatform($publishId) {
        $this->publishId = $publishId;
    }
    
    //Upload QTI of a single component
    public function uploadQTIComponent($comp, $threadID, $logger) {
        $this->stderr = fopen('logs/stderr.log','a');
        $logger->trace(LEVEL_INFO, 'uploading VO QTI of subcomponent '.$comp['id'].' to VO QTI Player');        
        
        $repo = $comp['method'];
        $compFile = $comp['fname'];
        $compRef = $comp['ref'];
        $compId = $comp['compId'];
        $subcompId = $comp['id'];

        //create base path
        $ind = strrpos($compFile, '/');
        $base = '';
        if($ind > 0) {
            $base = substr($compFile, 0, $ind+1);
			$qtibase = $base.'../../QTI/';
        }
        
        $txt = file_get_contents($compFile);
        if($txt===false) throw new Exception("Component $compFile does not exist");

        $txt = EntityConverter::convert_entities($txt);
        $doc = new SimpleXMLElement($txt);

	$this->uploadAssessments($doc, $logger, $qtibase, $base);
		
        //also post containing includes
        $main = new SimpleXMLElement($txt);
        $incs = $main->xpath("//include");
        foreach($incs as $inc) {
            $incId = (string)$inc['filename'];
            $fname = $base.$incId;
            $txt = file_get_contents($fname);
            if($txt===false) throw new Exception("File $fname does not exist");
            $txt = EntityConverter::convert_entities($txt);

            //find references to resources in this document
            $doc = new SimpleXMLElement($txt);

            $this->uploadAssessments($doc, $logger, $qtibase, $base);
        }
        fclose($this->stderr);
    }

    private function uploadAssessments($doc, $logger, $qtibase, $base){
        //find references to resources in this document
        $asms = $doc->xpath("//assessment");
        foreach($asms as $asm){
            $id = (string)$asm['src'];
            $ind = strpos($id, ' ');
            if($ind!==FALSE) {
                throw new Exception("Assignment name '$id' contains spaces. This is not allowed.");
            }
            $fileExists = false;
            if(file_exists($qtibase.$id)) {
                $qtifname = $qtibase.$id;
                $fileExists = true;
            } else {
                $fileExists = false;
            }
            if($fileExists){
                $logger->trace(LEVEL_INFO, 'uploading assessment (src='.$id.')');        
                $getUrl = $this->sendAssessment($qtifname, $logger);
            } else {
                $logger->trace(LEVEL_ERROR, "Missing QTI assessment: File ".$qtibase.$id." does not exist.");
            }
        }
    }
     

    private function sendAssessment($fname, $logger) {
        $curdir = getcwd();                //store current working directory
        $ind = strrpos($fname, '/');       //and change working directory to location of qti-file
        $name = substr($fname, $ind+1);    //in the filename
        $ch = curl_init(); 
	$fp = fopen($fname, 'r');
	curl_setopt($ch, CURLOPT_URL, 'http://qti-player.appspot.com/services/putfile?repo=ster&id='.$name);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Content-Length: '.filesize($fname),
	    'Content-Type: application/octet-stream'
	));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 86400); // 1 Day Timeout
	curl_setopt($ch, CURLOPT_INFILE, $fp);
	curl_setopt($ch, CURLOPT_BUFFERSIZE, 128);
	curl_setopt($ch, CURLOPT_INFILESIZE, filesize($fname));

        $response = curl_exec($ch);

        if($response!=null && strncmp($response, 'error', 5)==0){
            throw new Exception($response);
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$errormsg = curl_error($ch);
        curl_close($ch);
        switch($code) {
            case "200":
                $logger->trace(LEVEL_INFO, "OK: return code: $code");
                break;
            default:
                $logger->trace(LEVEL_INFO, "Not OK: return code: $code, message: $response");
                break;
        }
        return $response;
    }

}

?>

