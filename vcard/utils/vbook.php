<?php
if (!defined('IN_CMS')) {
    exit();
}

/*
 * File:
 *      vbook.php
 *
 * Project:
 *      vCard PHP <http://vcardphp.sourceforge.net>
 *
 * Author:
 *      Frank Hellwig <frank@hellwig.org>
 *
 * Usage:
 *      http://host/path/vbook.php?file=contacts.vcf
 *      See the index.html file for additional parameters.
 */

//require("vcard.php");

/*
if ($_GET['file']) {
print_vcard_address_book(
$_GET['file'],
$_GET['title'],
$_GET['style'],
$_GET['cat'],
$_GET['hide']);
} else {
include('index.html');
}*/

class VBookReader
{
    var $result;
    
    public function print_vcard_address_book($file, /*$title, $style,*/ $cat, $hide)
    {
        $this->result = "";
        /*if ($title) {
        $title = stripcslashes($title);
        } else if ($file) {
        $title = $file;
        } else {
        $title = "vCard PHP - A vCard Address Book";
        }*/
        
        //$this->result.= "<html>\n<head>\n<title>$title</title>\n";
        
        /*if (!$style) {
        $style = "style.css";
        }*/
        
        //$this->result.= "<link href='$style' type='text/css' rel='stylesheet'>\n";
        
        if (!$file) {
            $this->result .= ('Required $file parameter not specified.');
        } else {
            $lines = file($file);
            if (!$lines) {
                $this->result .= ("Can't read the vCard file: $file");
            } else {
                $cards = $this->parse_vcards($lines);
                $this->print_vcards($cards, $cat, $hide);
            }
        }
        
        
        $this->result .= "<p>Created by: <a href='http://vcardphp.sourceforge.net/'>";
        $this->result .= "http://vcardphp.sourceforge.net/</a></p>\n";
        
        //$this->result.= "</body>\n</html>\n";
        return $this->result;
    }
    
    /**
     * Prints a set of vCards in two columns. The $categories_to_display is a
     * comma delimited list of categories.
     */
    function print_vcards(&$cards, $categories_to_display, $hide)
    {
        $all_categories = $this->get_vcard_categories($cards);
        
        if (!$categories_to_display) {
            $categories_to_display = array(
                'All'
            );
        } else if ($categories_to_display == '*') {
            $categories_to_display = $all_categories;
        } else {
            $categories_to_display = explode(',', $categories_to_display);
        }
        
        if ($hide) {
            $hide = explode(',', $hide);
        } else {
            $hide = array();
        }
        
        $this->result .= "<p class='categories'>\nCategories: ";
        $this->result .= join(', ', $categories_to_display);
        $this->result .= "<br />\n</p>\n";
        
        $i = 0;
        foreach ($cards as $card_name => $card) {
            if (!$card->inCategories($categories_to_display)) {
                continue;
            }
            
            if ($i == 0) {
                $this->result .= "<table width='100%' cellspacing='4' border='0'>\n";
                $this->result .= "<tr>\n";
            }
            $this->result .= "<td class='vcard' width='50%' valign='top'>\n";
            $this->result .= "<p class='name'><strong>$card_name</strong>";
            // Add the categories (if present) after the name.
            $property = $card->getProperty('CATEGORIES');
            if ($property) {
                // Replace each comma by a comma and a space.
                $categories = $property->getComponents(',');
                $categories = join(', ', $categories);
                $this->result .= "&nbsp;($categories)";
            }
            $this->result .= "</p>\n";
            $this->print_vcard($card, $hide);
            $this->result .= "</td>\n";
            $i = ($i + 1) % 2;
            if ($i == 0) {
                $this->result .= "</tr>\n";
                $this->result .= "</table>\n";
            }
        }
        if ($i != 0) {
            $this->result .= "<td>&nbsp;</td>\n";
            $this->result .= "</tr>\n";
            $this->result .= "</table>\n";
        }
    }
    
    /**
     * Prints the vCard as HTML.
     */
    function print_vcard($card, $hide)
    {
        $names = array(
            'FN',
            'TITLE',
            'ORG',
            'TEL',
            'EMAIL',
            'URL',
            'ADR',
            'BDAY',
            'NOTE'
        );
        
        $row = 0;
        
        foreach ($names as $name) {
            if ($this->in_array_case($name, $hide)) {
                continue;
            }
            $properties = $card->getProperties($name);
            if ($properties) {
                foreach ($properties as $property) {
                    $show = true;
                    if (array_key_exists("TYPE", $property->params)) {
                        $types = $property->params['TYPE'];
                        if ($types) {
                            foreach ($types as $type) {
                                if ($this->in_array_case($type, $hide)) {
                                    $show = false;
                                    break;
                                }
                            }
                        }
                    }
                    
                    if ($show) {
                        $class = ($row++ % 2 == 0) ? "property-even" : "property-odd";
                        $this->print_vcard_property($property, $class, $hide);
                    }
                }
            }
        }
    }
    
    /**
     * Prints a VCardProperty as HTML.
     */
    function print_vcard_property($property, $class, $hide)
    {
        $name  = $property->name;
        $value = $property->value;
        switch ($name) {
            case 'ADR':
                $adr   = $property->getComponents();
                $lines = array();
                for ($i = 0; $i < 3; $i++) {
                    if ($adr[$i]) {
                        $lines[] = $adr[$i];
                    }
                }
                $city_state_zip = array();
                for ($i = 3; $i < 6; $i++) {
                    if ($adr[$i]) {
                        $city_state_zip[] = $adr[$i];
                    }
                }
                if ($city_state_zip) {
                    // Separate the city, state, and zip with spaces and add
                    // it as the last line.
                    $lines[] = join("&nbsp;", $city_state_zip);
                }
                // Add the country.
                if (sizeof($adr) >= 6) {
                    $lines[] = $adr[6];
                }
                $html = join("\n", $lines);
                break;
            case 'EMAIL':
                $html = "<a href='mailto:$value'>$value</a>";
                break;
            case 'URL':
                $html = "<a href='$value' target='_base'>$value</a>";
                break;
            case 'BDAY':
                $html = "Birthdate: $value";
                break;
            case 'X-NOKIA-PND-GROUP':
                $html = "Groups: ";
                for ($i = 0; $i < strlen($value); $i += 2) {
                    $html .= $value{$i};
                }
                break;
            default:
                $components = $property->getComponents();
                $lines      = array();
                foreach ($components as $component) {
                    if ($component) {
                        $lines[] = $component;
                    }
                }
                $html = join("\n", $lines);
                break;
        }
        $this->result .= "<p class='$class'>\n";
        $this->result .= nl2br(stripcslashes($html));
        if (array_key_exists("TYPE", $property->params)) {
            $types = $property->params['TYPE'];
            if ($types) {
                $type = join(", ", $types);
                $this->result .= " (" . ucwords(strtolower($type)) . ")";
            }
        }
        $this->result .= "\n</p>\n";
    }
    
    function get_vcard_categories(&$cards)
    {
        $unfiled = false; // set if there is at least one unfiled card
        $result  = array();
        foreach ($cards as $card_name => $card) {
            $properties = $card->getProperties('CATEGORIES');
            if ($properties) {
                foreach ($properties as $property) {
                    $categories = $property->getComponents(',');
                    foreach ($categories as $category) {
                        if (!in_array($category, $result)) {
                            $result[] = $category;
                        }
                    }
                }
            } else {
                $unfiled = true;
            }
        }
        if ($unfiled && !in_array('Unfiled', $result)) {
            $result[] = 'Unfiled';
        }
        return $result;
    }
    
    /**
     * Parses a set of cards from one or more lines. The cards are sorted by
     * the N (name) property value. There is no return value. If two cards
     * have the same key, then the last card parsed is stored in the array.
     */
    function parse_vcards(&$lines)
    {
        $cards = array();
        $card  = new VCardReader();
        while ($card->parse($lines)) {
            $property = $card->getProperty('N');
            if(!$property)return "";
            $n   = $property->getComponents();
            $tmp = array();
            if (sizeof($n) >= 4 && $n[3])
                $tmp[] = $n[3]; // Mr. 
            if (sizeof($n) >= 2 && $n[1])
                $tmp[] = $n[1]; // John
            if (sizeof($n) >= 3 && $n[2])
                $tmp[] = $n[2]; // Quinlan
            if (sizeof($n) >= 5 && $n[4])
                $tmp[] = $n[4]; // Esq.
                
                
            if(sizeof($tmp)==0 && !($n[0])){
                $property = $card->getProperty('FN');   
                $n   = $property->getComponents();
                if(!$property)return "";
            }
            
            $ret = array();
            if ($n[0])
                $ret[] = $n[0];
            $tmp = join(" ", $tmp);
            if ($tmp)
                $ret[] = $tmp;
            $key         = join(", ", $ret);
            $cards[$key] = $card;
            
            // MDH: Create new VCardReader to prevent overwriting previous one (PHP5)
            $card        = new VCardReader();
        }
        ksort($cards);
        return $cards;
    }
    
    // ----- Utility Functions -----
    
    /**
     * Checks if needle $str is in haystack $arr but ignores case.
     */
    function in_array_case($str, $arr)
    {
        foreach ($arr as $s) {
            if (strcasecmp($str, $s) == 0) {
                return true;
            }
        }
        return false;
    }
}
?>
