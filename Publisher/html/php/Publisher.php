<?php
// Usage:
//
// http://mathunited.pragma-ade.nl:41080/Publisher/Publisher.php?user=mslob&passwd=test&cmd=publishThread&repo=ma&target=mathunited&thread=ma-test

//http://www.mathunited.nl/testcontent/publisher/Publisher.php?user=mslob&passwd=test&cmd=publishThread&target=threeships&thread=wm-havovwo-1
//// http://www.mathunited.nl/testcontent/publisher/Publisher.php?user=mslob&passwd=test&cmd=publishThread&target=mathunited&thread=ts_test
// http://www.mathunited.nl/testcontent/publisher/Publisher.php?user=mslob&passwd=test&cmd=publishThread&target=threeships&thread=ma-havovwo-12
// http://www.mathunited.nl/testcontent/publisher/logs/log.txt
//define('COMPONENTS_FILE', '/data/content-overview/components.xml');
define('MAX_TIME_LIMIT',60);
require_once("platforms/Platform.php");
require_once("platforms/ThreeShipsPlatform.php");
require_once("platforms/GAEPlatform.php");
require_once("platforms/VOPlatform.php");
require_once("platforms/VOPlatformConcept.php");
require_once("platforms/PulseOnPlatform.php");
require_once("platforms/VOQtiPlatform.php");
require_once("Logger.php");


class Publisher {
   var $loglevel = LEVEL_INFO;
   var $targetID;
   var $repoID;
   var $logger;
   var $callback;      //not null if dynamic scripting callback is used
   var $comm;          //request data $_POST or $_GET
   function Publisher() {
//       header('Content-type: text/html');
        //post or get?
        $curdir = getcwd();
        $clear_busy_file = false; //whether to set the status of Publisher to 'idle' after this call
        $statusFile = 'not_set_yet';
        $this->comm = $_POST;
        $this->repoID = 'generic';
        if( isset($this->comm['repo']) ) {
            $this->repoID = $this->comm['repo'];
        }

        include("Config.php");
        $this->logger = new Logger($this->loglevel, $this->repoID, false);
        try{
            $this->logger->trace(LEVEL_INFO, "Start Publisher");

            if( isset($this->comm['cmd']) ) {
                $cmd = $this->comm['cmd'];
            } else throw new Exception('No command supplied');

            if(!$this->login()) throw new Exception('Login failed');

            if( isset($this->comm['target']) ) {
                $this->targetID = $this->comm['target'];
            } else {
                $this->targetID = "all";
            }
            $this->logger->trace(LEVEL_INFO, "Platform targetID: " . $this->targetID);

            $baseURL = false;
            if( isset($this->comm['repo']) ) {
                $this->repoID = $this->comm['repo'];
            } else throw new Exception('repo attribute is missing');

            $this->repo = Config::getRepoConfig($this->repoID);
            $baseURL = $this->repo['basePath'];;
            if(!$baseURL) throw new Exception('repo attribute is invalid');

            //check if Publisher is not busy already
            $statusFile = "../logs/status_".$this->repoID.".xml";
            $statusDoc = file_get_contents($statusFile);
            if($statusDoc!==false) {
                $xml = new SimpleXMLElement($statusDoc);
                $statusElm = $xml->xpath("//status");
                if($statusElm && count($statusElm)>0) {
                    $status = (string)$statusElm[0];
                    if(strcmp($status,'busy')==0) throw new Exception('Publisher is busy');
                }
            }
            $statusFp = fopen($statusFile,"w");
            fwrite($statusFp,"<publisher><status>busy</status></publisher>"); 
            fclose($statusFp);
            $clear_busy_file = true;

            $result="{success:true, msg:\"Nothing done\"}";
            switch($cmd) {
                case "publishOverview":
                    $result = $this->publishOverview();
                    $this->logger->trace(LEVEL_INFO, "Finished publishing overview");
                    break;
                    
                case "publishComponentFile":
                    if( isset($this->comm['compId']) ) {
                        $compId = $this->comm['compId'];
                    } else throw new Exception('No component id given');
                    if( isset($this->comm['compRef']) ) {
                        $compRef = $this->comm['compRef'];
                    } else throw new Exception('No component filename given');

                    $this->publishComponentFile($this->targetID, $compId, $compRef, $this->repoID);
                    $this->logger->trace(LEVEL_INFO, "Finished publishing component file $compId");
                    break;
                    
                case "publishSubcomponent":
                    if( isset($this->comm['subcompId']) ) {
                        $subcompId = $this->comm['subcompId'];
                    } else throw new Exception('No subcomponent id given');
                    if( isset($this->comm['compId']) ) {
                        $compId = $this->comm['compId'];
                    } else throw new Exception('No component id given');
                    if( isset($this->comm['compRef']) ) {
                        $compRef = $this->comm['compRef'];
                    } else throw new Exception('No component filename given');
                    if( isset($this->comm['subcompRef']) ) {
                        $subCompRef = $this->comm['subcompRef'];
                    } else throw new Exception('No subcomponent filename given');

                    $this->publishSubcomponent($this->targetID, $subcompId, $compId, $subCompRef, $compRef, $this->repoID);
                    $this->logger->trace(LEVEL_INFO, "Finished publishing subcomponent $subcompId");
                    break;
                    
                case "publishThread":
                    if( isset($this->comm['thread']) ) {
                        $threadID = $this->comm['thread'];
                    } else throw new Exception('No thread id given');

                    $this->publishThread($threadID, $this->targetID, $this->repo, false);
                    $this->logger->trace(LEVEL_INFO, "Finished publishing thread $threadID");
                    break;
                case "uploadQTISubcomponent":
                    if( isset($this->comm['id']) ) {
                        $subcompId = $this->comm['id'];
                    } else throw new Exception('No subcomponent id given');
                    if( isset($this->comm['compId']) ) {
                        $compId = $this->comm['compId'];
                    } else throw new Exception('No component id given');
                    if( isset($this->comm['ref']) ) {
                        $compRef = $this->comm['ref'];
                    } else throw new Exception('No component ref given');

                    $this->uploadQTIComponent($this->targetID, $subcompId, $compId, $compRef, $this->repoID);
                    $this->logger->trace(LEVEL_INFO, "Finished uploading QTI subcomponent $subcompId");
                    break;
            }  
            $statusFp = fopen($statusFile,"w");
            fwrite($statusFp,"<publisher><status>idle</status></publisher>"); 
            fclose($statusFp);
            return "{success: true, msg:\"succeeded\"}";;
        } catch(Exception $e) {
            $msg = $e->getMessage();
            $this->logger->trace(LEVEL_ERROR, $msg);
            if($clear_busy_file){
                chdir($curdir);
                $statusFp = fopen($statusFile,"w");
                fwrite($statusFp,"<publisher><status>idle</status></publisher>"); 
                fclose($statusFp);
            }
            return "{success: false, msg:\"$msg\"}";
        }
    }
	
	function getPlatform() {
        //generate an id for this publish
        $publishId = date(DATE_RFC822);
		switch ($this->targetID) {
			case "vo": 
				return new VOPlatform($publishId, false);
			case "vo-concept": 
				return new VOPlatformConcept($publishId, false);
			default: 
				return new GAEPlatform($publishId, false);
		}
	}
    
    function publishOverview(){ 
        //generate an id for this publish
        $publishId = date(DATE_RFC822);
		$pf = $this->getPlatform();
        $threadURL = 'http://localhost'.$this->repo['threadsURL'];
        $this->logger->trace(LEVEL_INFO, 'threads url: '.$threadURL); 
        $componentsURL = 'http://localhost'.$this->repo['componentsURL'];
        $threadsXML = file_get_contents($threadURL);
        $componentsXML = file_get_contents($componentsURL);
        $threadsXML = EntityConverter::convert_entities($threadsXML);
        $componentsXML = EntityConverter::convert_entities($componentsXML);
        
        $pf->publishOverview($this->repoID, $this->repo, $this->logger, $threadsXML, $componentsXML);
        return $result;
    }

    //execute task. For specific tasks, override executeImpl()
    function uploadQTIComponent($targetID, $subcompId, $compId, $compRef, $compRepo) {
        //generate an id for this publish
        $publishId = date(DATE_RFC822);
        
        switch($targetID){
            case "pulseon": $pf = new PulseOnPlatform($publishId, false); break;
            case "mathunited": $pf = new PulseOnPlatform($publishId, false); break;
            case "vo": $pf = new VOQtiPlatform($publishId, false); break;
            case "vo-concept": $pf = new VOQtiPlatform($publishId, false); break;
            default:
                throw new Exception("uploadQTIComponent: Unknown target ID: $targetID");
                break;
        }
        
        $comp = array();
        $comp['method']=$compRepo;
        $comp['compId']=$compId;
        $comp['id']=$subcompId;
        $comp['ref']=$compRef;
        $comp['fname']=$this->repo['basePath'].$compRef;
        $pf->uploadQTIComponent($comp, "", $this->logger);
    }

    function publishComponentFile($targetID, $compId, $compRef, $compRepo) {
        //generate an id for this publish
        $publishId = date(DATE_RFC822);
        
        switch($targetID){
            case "threeships":$pf = new ThreeShipsPlatform($publishId, false); break;
            case "mathunited":$pf = new GAEPlatform($publishId); break;
            case "vo":$pf = new VOPlatform($publishId); break;
            case "vo-concept":$pf = new VOPlatformConcept($publishId); break;
            default:
                throw new Exception("publishComponentFile: Unknown target ID $targetID");
                break;
        }
        $pf->publishComponentFile($compId, $compRef, $this->repo['basePath'], $compRepo, $this->logger);
    }
    
    function publishSubcomponent($targetID, $subcompId, $compId, $subcompRef, $compRef, $compRepo) {
        //generate an id for this publish
        $publishId = date(DATE_RFC822);
        
        switch($targetID){
            case "threeships":$pf = new ThreeShipsPlatform($publishId, false); break;
            case "mathunited":$pf = new GAEPlatform($publishId); break;
            case "vo":$pf = new VOPlatform($publishId); break;
            case "vo-concept":$pf = new VOPlatformConcept($publishId); break;
            default:
                throw new Exception("publishSubcomponent: Unknown target ID $targetID");
                break;
        }
        
        $comp = array();
        $comp['method']=$compRepo;
        $comp['compId']=$compId;
        $comp['compRef']=$compRef;
        $comp['subcompId']=$subcompId;
        $comp['subcompRef']=$subcompRef;
        $comp['pathbase']=$this->repo['basePath'];
        $pf->publishSubcomponent($comp, "", $this->logger);
    }

    function openFile($fname) {
        # Get text from file
        $txt = file_get_contents($fname);

        # Regular expression to match xml-model directive (optional space between
        # starting <? and xml-model; can appear on multiple lines.
        $pattern = "/<\?\s*xml-model.*?\?>/m";
        $limit = -1; # Keep searching for more for safety. // TODO: could be = 1 as only one model should be specified
        $count = 0; # Count number of replacements
        

        # Replace pattern with the empty string
        $filtered_txt = preg_replace($pattern, "", $txt, $limit, $count);
                
        # Log if replacement was done, and set text in that case
        if ($count > 0) {
            //$msg = "DEBUG: Removed $count xml-model directives from $fname.";
            //$this->logger->trace(LEVEL_ERROR, $msg);
            #error_log("GenerateIndex::openFile: " . $msg);
            $txt = $filtered_txt;
        }

        $txt = EntityConverter::convert_entities($txt);
        $doc = new SimpleXMLElement($txt);
        return $doc;
    }

    //execute task. For specific tasks, override executeImpl()
    function publishThread($threadID, $targetID, $repo, $doDemo) {
        //generate an id for this publish
        $publishId = date(DATE_RFC822);
        
        //read threads xml
        $baseURL = $repo['basePath'];
        $threadsXMLstr = '/var/www'.$repo['threadsURL'];
        $threads = $this->openFile($threadsXMLstr);
        $compids = array();
        
        foreach ($threads->xpath("/threads/thread[@id=\"$threadID\"]//contentref") as $ref) {
            $compids[] = array( 'ref' => (string)$ref['ref'],
                                'method' => (string)$ref['method']);
        }
        if(count($compids)==0)  throw new Exception("Thread $threadID does not contain any components");
        
        $compFiles = array();
        $compsXMLstr = '/var/www'.$repo['componentsURL'];
        $comps = $this->openFile($compsXMLstr);

        switch($targetID){
            case "threeships":$tspf = new ThreeShipsPlatform($publishId, $doDemo); break;
            case "mathunited":$tspf = new GAEPlatform($publishId); break;
            case "vo":$tspf = new VOPlatform($publishId); break;
            case "vo-concept":$tspf = new VOPlatformConcept($publishId); break;
            default:
                throw new Exception("publishThread: Unknown target ID: $targetID");
                break;
        }
        
        set_time_limit(0);  //prevent timeout
        foreach($compids as $cc) {
            $ref = $cc['ref'];
            $compDef  = $comps->xpath("/mathunited/methods/method/components/component[@id=\"$ref\"]");
            $compId = $ref;
            if($compDef && count($compDef)>0){
                $compDef = $compDef[0];
                $compFile = $compDef['file'];
                $title = $compDef->title;
                $title = (string)$title[0];
                $subcomps = $compDef->xpath("subcomponents/subcomponent");

                $tspf->publishComponentFile($compId, $compFile, $this->repo['basePath'], $this->repo['name'], $this->logger);
                
                foreach($subcomps as $sc) {
                    $subCompFile= $sc->file;
                    $subCompFile = (string)$subCompFile[0];
                    $subCompId = (string)$sc['id'];

                    $comp = array();
                    $comp['method']=$this->repo['name'];
                    $comp['compId']=$compId;
                    $comp['compRef']=$compFile;
                    $comp['subcompId']=$subCompId;
                    $comp['subcompRef']=$subCompFile;
                    $comp['pathbase']=$this->repo['basePath'];
                    try{
                        $tspf->publishSubcomponent($comp, "", $this->logger);
                    } catch(Exception $e) {
                        $msg = $e->getMessage();
                        $this->logger->trace(LEVEL_ERROR, $msg);
                        $this->logger->trace(LEVEL_ERROR, 'Publishing this subcomponent failt. Continuing publishing');
                    }

                }
            }
        }
              
        $tspf->postPublish();
    }


    //specific task implementation. Return false on failure, some string on success
    function executeImpl() {
        die("not implemented");
    }

    //check login credentials.
    //returns: true if login succeeded, false otherwise
    function login() {
        $error = false;
        if( isset($this->comm['user'] )) {
           $username = $this->comm['user'];
           if($this->loglevel<=LEVEL_TRACE) {
              $this->logger->trace(LEVEL_TRACE, "-- user = $username");
           }
        } else {
           $error = true;
           $this->logger->trace(LEVEL_ERROR, "No user name supplied");
        }
        if( isset($this->comm['passwd'] )) {
           $passwd = $this->comm['passwd'];
        } else {
           $error = true;
           $this->logger->trace(LEVEL_ERROR, "No password supplied");
        }

        if(!$error &&
            (     (strcmp($username,'mslob')==0 && strcmp($passwd,'test')==0)
            )
          ) {
           $res = true;
        } else {
           $res = false;
        }
        return $res;
    }

}

$pub = new Publisher();

?>