<?php
 
if (!defined('IN_CMS')) { exit(); }

	echo $originalContent; 
?>

	<?php
	if (/*!isset($vcard_id) &&*/ isset($vcardsList)){
		?>
		<form action="<?php echo (USE_MOD_REWRITE == false) ? '?' : ''; echo $callerUri."/vcard_id"; ?>" method="post">
			<fieldset style="padding: 0.5em;">
        <legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;">Available vcards</legend>
			<table class="fieldset" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="label"><label for="include_time"><?php echo __('VCard to show'); ?>: </label></td>
                <td class="field">
                    <select class="select" name="vcard_id" id="vcard_id">
<?php
	foreach($vcardsList as $item){
		
		?>
		<option value="<?php echo $item; ?>"><?php echo $item; ?></option>
		<?php	
	}
	
?>
                    </select>
                </td>
                <td class="help"><?php echo __('Choose the vcard you will need.'); ?></td>
            </tr>
       </table>
      </fieldset>
			<p class="buttons">
        <input class="button" name="say" type="submit" accesskey="s" value="<?php echo __('Say'); ?>" />
    </p>
	</form>
		<?php
	}else if (isset($vcard_id) && !isset($vcardsList)){
		$ext = pathinfo($vcard_id, PATHINFO_EXTENSION);
		if($ext=="vcf"){
			echo "VCard: ".$vcard_id."</br>";
			$reader = new VBookReader();
			$settings = Plugin::getAllSettings('vcard'.'_settings');
			
			$vcardPath = str_replace("//","/", CMS_ROOT . '/' . $settings["vcards_path"]."/".$vcard_id);
			echo $reader->print_vcard_address_book($vcardPath,null,null);
		}
	}else{
	?>
	Error reading vcards!!
	<?php
}
?>