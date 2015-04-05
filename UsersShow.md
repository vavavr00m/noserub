# NoseRub API Method: users show #

Returns extended information of a given user, specified by ID or screen name as per the required id parameter.
The author's most recent status will be returned inline.

#### URL ####
api/users/show._format_

#### Formats ####
xml, json

#### HTTP method(s) ####
GET

#### Requires Authentication ####
false

#### Parameters ####

One of the following is required:

  * id.  The ID or screen name of a user.
> > Example: http://example.com/api/users/show/12345.json or http://example.com/api/users/show/bob.xml
  * user\_id. Specfies the ID of the user to return. Helpful for disambiguating when a valid user ID is also a valid screen name.
> > Example: http://example.com/api/users/show.xml?user_id=1401881
  * screen\_name. Specfies the screen name of the user to return. Helpful for disambiguating when a valid screen name is also a user ID.
> > Example: http://example.com/api/users/show.xml?screen_name=101010