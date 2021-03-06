<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Authentication;

use Exception;
use Zend_Config;
use Icinga\User;
use Icinga\Web\Session;
use Icinga\Logger\Logger;
use Icinga\Exception\NotReadableError;
use Icinga\Application\Config as IcingaConfig;
use Icinga\User\Preferences;
use Icinga\User\Preferences\PreferencesStore;

class Manager
{
    /**
     * Singleton instance
     *
     * @var self
     */
    private static $instance;

    /**
     * Authenticated user
     *
     * @var User
     */
    private $user;


    private function __construct()
    {
    }

    /**
     * Get the authentication manager
     *
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function setAuthenticated(User $user, $persist = true)
    {
        $username = $user->getUsername();
        try {
            $config = IcingaConfig::app();
        } catch (NotReadableError $e) {
            Logger::error(
                new Exception('Cannot load preferences for user "' . $username . '". An exception was thrown', 0, $e)
            );
            $config = new Zend_Config(array());
        }
        if (($preferencesConfig = $config->preferences) !== null) {
            try {
                $preferencesStore = PreferencesStore::create(
                    $preferencesConfig,
                    $user
                );
                $preferences = new Preferences($preferencesStore->load());
            } catch (NotReadableError $e) {
                Logger::error(
                    new Exception(
                        'Cannot load preferences for user "' . $username . '". An exception was thrown', 0, $e
                    )
                );
                $preferences = new Preferences();
            }
        } else {
            $preferences = new Preferences();
        }
        $user->setPreferences($preferences);
        $membership = new Membership();
        $groups = $membership->getGroupsByUsername($username);
        $user->setGroups($groups);
        $admissionLoader = new AdmissionLoader();
        $user->setPermissions(
            $admissionLoader->getPermissions($username, $groups)
        );
        $user->setRestrictions(
            $admissionLoader->getRestrictions($username, $groups)
        );
        $this->user = $user;
        if ($persist == true) {
            $this->persistCurrentUser();
        }
    }

    /**
     * Writes the current user to the session
     */
    public function persistCurrentUser()
    {
        $session = Session::getSession();
        $session->set('user', $this->user);
        $session->write();
        $session->refreshId();
    }

    /**
     * Tries to authenticate the user with the current session
     */
    public function authenticateFromSession()
    {
        $this->user = Session::getSession()->get('user');

        if ($this->user !== null && $this->user->isRemoteUser() === true) {
            list($originUsername, $field) = $this->user->getRemoteUserInformation();
            if (array_key_exists($field, $_SERVER) && $_SERVER[$field] !== $originUsername) {
                $this->removeAuthorization();
            }
        }
    }

    /**
     * Returns true when the user is currently authenticated
     *
     * @param  Boolean  $ignoreSession  Set to true to prevent authentication by session
     *
     * @return bool
     */
    public function isAuthenticated($ignoreSession = false)
    {
        if ($this->user === null && !$ignoreSession) {
            $this->authenticateFromSession();
        }
        return is_object($this->user);
    }

    /**
     * Whether an authenticated user has a given permission
     *
     * This is true if the user owns this permission, false if not.
     * Also false if there is no authenticated user
     *
     * TODO: I'd like to see wildcard support, e.g. module/*
     *
     * @param  string  $permission  Permission name
     * @return bool
     */
    public function hasPermission($permission)
    {
        if (! $this->isAuthenticated()) {
            return false;
        }
        foreach ($this->user->getPermissions() as $p) {
            if ($p === $permission) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get applied restrictions matching a given restriction name
     *
     * Returns a list of applied restrictions, empty if no user is
     * authenticated
     *
     * @param  string  $restriction  Restriction name
     * @return array
     */
    public function getRestrictions($restriction)
    {
        if (! $this->isAuthenticated()) {
            return array();
        }
        return $this->user->getRestrictions($restriction);
    }

    /**
     * Purges the current authorization information and session
     */
    public function removeAuthorization()
    {
        $this->user = null;
        Session::getSession()->purge();
    }

    /**
     * Returns the current user or null if no user is authenticated
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Getter for groups belonged to authenticated user
     *
     * @return  array
     * @see     User::getGroups
     */
    public function getGroups()
    {
        return $this->user->getGroups();
    }
}
