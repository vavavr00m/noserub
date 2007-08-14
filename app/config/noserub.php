<?php
/* $Id$ $id$ $ID$ $Revision$ */

/**
 * Domain name, under which the noserub-server can be accessed
 * Example: http://<NOSERUB_DOMAIN>/
 * @name NOSERUB_DOMAIN
 */
define('NOSERUB_DOMAIN', 'server1');

/**
 * the name of the subdirectory, where
 * noserub is installed on your server:
 * http://myserver.com/noserub/
 * you should not change that, because other
 * noserub servers will look at that place
 * for account information:
 * john.doe@myserver.com => http://myserver.com/noserub/john.doe
 * @name NOSERUB_URL_PREFIX
 */
define('NOSERUB_URL_PREFIX', '/noserub');

/**
 * A hash value to protect some admin routes from execution by
 * non-authorized users. Such routes are eg the one for 
 * synchronizing accounts from external identities
 * @name NOSERUB_ADMIN_HASH
 */
define('NOSERUB_ADMIN_HASH', '74364g236ewe6rw6e');

/**
 * all = everyone can register
 * none = no one is allowed to register from that point on.
 *        all previously registered identities aren't altered
 * invitation = only the admin can invite people to register. 
 *              (this is yet not implemented)
 * @name: NOSERUB_REGISTRATION_TYPE
 */
define('NOSERUB_REGISTRATION_TYPE', 'all');

