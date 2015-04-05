# NoseRub API Method: network info #

Returns some information about a NoseRub network: the number of registered users, the registration type, the restricted hosts,
the migration, and whether subscriptions are allowed.

By default, this API method is disabled and has to be activated by an admin of the respective NoseRub network. A 503 HTTP status
code is returned in that case.

#### URL ####
api/network/info._format_

#### Formats ####
xml, json

#### HTTP method(s) ####
GET

#### Requires Authentication ####
false