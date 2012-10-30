<?php

/*
 * Filter logic for the ebGallery plugin
 *
 * This file is part of the ebGallery plugin for Wolf CMS
 */

//security measure
if (!defined('IN_CMS')) { exit(); }

/**
 * Main filter class
 */
class VcardFilter {

    // image resizing settings
    private $vcardFile;
    private $thumb_width;
    private $thumb_height;
    private $gallery_title;

    private $default_thumb_width = 100;
    private $default_thumb_height = 100;

    // template settings
    private $gallery_tmpl;
    private $title_tmpl;
    private $image_tmpl;
    private $selectedimage_tmpl;

    private $default_gallery_tmpl = '';//<div class="gallery">((gallery))</div>';
    private $default_title_tmpl = '';//<h2>((title))</h2>';
    private $default_image_tmpl = '';//<a class="photo" rel="((title))" href="((image))"><img src="((thumb))"/></a>';
    private $default_selectedimage_tmpl = '';

    // main wolf cms filter function which applies the current filter to the content
    public function apply($text) {
        $parsed_text = $this->_parse($text);
        return $parsed_text;
    }

    // parses the shortcodes
    private function _parse($text) {
        preg_match_all('/\(\((.*)\)\)/i', $text, $matches);
        $renderedVcard = "";
        if (is_array($matches) && isset($matches[1]) && count($mathes[1] > 0)) {
            
            foreach($matches[1] as $k => $vcardFile) {
                
                $makro = $matches[0][$k];

                $vcardFile = CMS_ROOT . '/public/vcards/' . $vcardFile;
                
                if(!is_dir($vcardFile)){
                    $reader = new VBookReader();
        			$renderedVcard .=  $reader->print_vcard_address_book($vcardFile,null,null);
                }
                
            }
        }

        return $renderedVcard;
    }
}