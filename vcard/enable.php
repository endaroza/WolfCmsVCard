<?php
if (!defined('IN_CMS')) { exit(); }

// Check if the plugin's settings already exist and create them if not.
if (Plugin::getSetting('vcards_path', 'vcard'.'_settings') === false) {
    // Store settings new style
    $settings = array('vcards_path' => ''
                     );

    Plugin::setAllSettings($settings, 'vcard'.'_settings');
}
?>