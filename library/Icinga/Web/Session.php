<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Web;

use Icinga\Web\Session\PhpSession;
use Icinga\Web\Session\Session as BaseSession;
use Icinga\Exception\ProgrammingError;

/**
 * Session container
 */
class Session
{
    /**
     * The current session
     *
     * @var BaseSession $session
     */
    private static $session;

    /**
     * Create the session
     *
     * @param   BaseSession  $session
     *
     * @return  BaseSession
     */
    public static function create(BaseSession $session = null)
    {
        if ($session === null) {
            self::$session = new PhpSession();
        } else {
            self::$session = $session;
        }

        return self::$session;
    }

    /**
     * Return the current session
     *
     * @return  BaseSession
     * @throws  ProgrammingError
     */
    public static function getSession()
    {
        if (self::$session === null) {
            throw new ProgrammingError('No session created yet');
        }

        return self::$session;
    }
}
