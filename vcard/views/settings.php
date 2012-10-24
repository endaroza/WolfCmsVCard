<?php
if (!defined('IN_CMS')) { exit(); }

?>
<h1><?php echo __('Settings'); ?></h1>
<p>
	<?php echo __('Display settings page here!'); ?>
	<!--The action on the controller-->
	<form action="<?php echo get_url('plugin/'.'vcard'.'/save'); ?>" method="post">
		<fieldset style="padding: 0.5em;">
        <legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;">HelloWorld Settings</legend>
        <table class="fieldset" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="label"><label for="vcards_path">Directory with vcards (below WolfCms path): </label></td>
                <td class="field"><input 
                	id="vcards_path" name="settings[vcards_path]" 
                	type="text" 
                	value="<?php echo $settings['vcards_path']; ?>" /></td>
                <td class="help"><?php echo __('Directory with vcards (below WolfCms path).'); ?></td>
            </tr>
        </table>
    </fieldset>
    <p class="buttons">
        <input class="button" name="commit" type="submit" accesskey="s" value="<?php echo __('Save'); ?>" />
    </p>
	</form>
</p>

<script type="text/javascript">
// <![CDATA[
    function setConfirmUnload(on, msg) {
        window.onbeforeunload = (on) ? unloadMessage : null;
        return true;
    }

    function unloadMessage() {
        return '<?php echo __('You have modified this page.  If you navigate away from this page without first saving your data, the changes will be lost.'); ?>';
    }

    $(document).ready(function() {
        // Prevent accidentally navigating away
        $(':input').bind('change', function() { setConfirmUnload(true); });
        $('form').submit(function() { setConfirmUnload(false); return true; });
    });
// ]]>
</script>