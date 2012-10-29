<?php
/* Security measure */
if (!defined('IN_CMS')) {
    exit();
}

class Vcard {
		function file_list($dir, $xtension)
		{ 
			$thelist = array();
 			if ($handle = opendir($dir)) {
			    while (false !== ($file = readdir($handle)))
			    {
			        if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == $xtension)
			        {
			            $thelist[]= $file;
			        }
			    }
			    closedir($handle);
			}
			return $thelist;

		}

    public function __construct(&$page, $params) {              	
        $this->page = & $page;
        $this->page->parent->behavior_id="Vcard";
        $this->params = $params;
				$originalContent = $this->page->part->body->content_html;
				$callerUri = $this->page->getUri();
				
				$settings = Plugin::getAllSettings('vcard'.'_settings');


				$vcardsPath = str_replace("//","/", CMS_ROOT . '/' . $settings["vcards_path"]);
				
        switch (count($params)) {
            case 2: 
               $vcard_id = $params[1];
            	 if($params[0]=="vcard_id"){
	          	 	 $this->page->part->body->content_html = 
	                 new View('../../plugins/'.'vcard'.'/views/show_vcard',
	                  array('vcardsPath'=> $vcardsPath,'vcard_id'=> $vcard_id,'originalContent'=> $originalContent,"callerUri"=> $callerUri));
              }else{
              	page_not_found();
              }
              break;
            case 1:
              if(array_key_exists("vcard_id", $_REQUEST)) {
            	 $vcard_id = $_REQUEST["vcard_id"];
            	}
            	
            	if($params[0]=="vcard_id" && array_key_exists("vcard_id", $_REQUEST)){
	          	 	 $this->page->part->body->content_html = 
	                 new View('../../plugins/'.'vcard'.'/views/show_vcard',
	                  array('vcardsPath'=> $vcardsPath,'vcard_id'=> $vcard_id,'originalContent'=> $originalContent,"callerUri"=> $callerUri));
              }else{
              	page_not_found();
              }
              break;
            case 0:
            	 {
            	 	$vcardsList = $this->file_list($vcardsPath,"vcf");
            	 $this->page->part->body->content_html = 
	                 new View('../../plugins/'.'vcard'.'/views/show_vcard',
	                  array('vcardsPath'=> $vcardsPath,'vcardsList'=> $vcardsList,'originalContent'=> $originalContent,"callerUri"=> $callerUri));
	                }
               	break;
            default:
              	page_not_found();
                     break;
        }
    }

}

class PageVcard extends Page {

    protected function setUrl() {
    	$this->url = trim($this->parent->url . '/' . $this->slug, '/');
    }

    public function title() {
        return $this->title." YEAH";
    }

    public function breadcrumb() {
        return $this->breadcrumb;
    }
}
