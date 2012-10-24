 <?php
 /* Security measure */
if (!defined('IN_CMS')) { exit(); }

class VcardController extends PluginController {
		public function __construct() {
        $this->setLayout('backend');
        $this->assignToLayout('sidebar', new View('../../plugins/'.'vcard'.'/views/sidebar'));
    }

    public function index() 
    { 
			$this->documentation();
    }

    public function documentation() {
        $this->display('vcard'.'/views/documentation');
    }
     function settings() {
        AuthUser::load();
        if (!AuthUser::isLoggedIn()) {
            redirect(get_url('login'));
        } else if (!AuthUser::hasPermission('admin_edit')) {
            Flash::set('error', __('You do not have permission to access the requested page!'));
            redirect(get_url());
        }
        
        $settings = Plugin::getAllSettings('vcard'.'_settings');

        if (!$settings) {
            Flash::set('error', 'Vcard - ' . __('unable to retrieve plugin settings.'));
            return;
        }

        $this->display('vcard'.'/views/settings', array('settings' => $settings));
    }
    
    function save() {
        if (isset($_POST['settings'])) {
            $settings = $_POST['settings'];
            foreach ($settings as $key => $value) {
                $settings[$key] = mysql_escape_string($value);
            }
            
            $ret = Plugin::setAllSettings($settings, 'vcard'.'_settings');

            if ($ret) {
                Flash::set('success', __('The settings have been saved.'));
            }
            else {
                Flash::set('error', 'An error occured trying to save the settings.');
            }
        }
        else {
            Flash::set('error', 'Could not save settings, no settings found.');
        }

        redirect(get_url('plugin/'.'vcard'.'/settings'));
    }
}
 ?>