<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Web\Controller;

use Icinga\Application\Config as IcingaConfig;
use Icinga\Exception\ConfigurationError;
use Icinga\Web\Session;
use Icinga\User\Preferences\PreferencesStore;

/**
 *  Base class for Preference Controllers
 *
 *  Module preferences use this class to make sure they are automatically
 *  added to the general preferences dialog. If you create a subclass of
 *  BasePreferenceController and overwrite @see init(), make sure you call
 *  parent::init(), otherwise you won't have the $tabs property in your view.
 *
 */
class BasePreferenceController extends ActionController
{
    /**
     * Return an array of tabs provided by this preference controller.
     *
     * Those tabs will automatically be added to the application's preference dialog
     *
     * @return array
     */
    public static function createProvidedTabs()
    {
        return array();
    }

    /**
     *  Initialize the controller and collect all tabs for it from the application and it's modules
     *
     *  @see ActionController::init()
     */
    public function init()
    {
        parent::init();
        $this->view->tabs = ControllerTabCollector::collectControllerTabs('PreferenceController');
    }

    protected function savePreferences(array $preferences)
    {
        $session = Session::getSession();
        $currentPreferences = $session->user->getPreferences();
        foreach ($preferences as $key => $value) {
            if ($value === null) {
                $currentPreferences->remove($key);
            } else {
                $currentPreferences->{$key} = $value;
            }
        }
        $session->write();

        if (($preferencesConfig = IcingaConfig::app()->preferences) === null) {
            throw new ConfigurationError(
                'Cannot save preferences changes since you\'ve not configured a preferences backend'
            );
        }
        $store = PreferencesStore::create($preferencesConfig, $session->user);
        $store->load(); // Necessary for patching existing preferences
        $store->save($currentPreferences);
    }
}
