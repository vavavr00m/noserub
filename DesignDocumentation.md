# Networks and Groups #

![http://noserub.googlecode.com/svn/wiki/uml/network.png](http://noserub.googlecode.com/svn/wiki/uml/network.png)

Networks and Groups are new concepts in NoseRub 0.9. A Network represents a single social network (e.g. a network
for PHP programmers), and each social network can contain multiple Groups (for example, the network for PHP
programmers could contain the groups "beginners" and "design patterns"). With this addition, it will be possible to
use a single NoseRub installation to host many social networks.

Each Network can be managed by many Admins, though each Admin can only manage one Network, due to security
considerations. For the same reason we have a separate Admin model, and don't allow Identities to manage Networks.
Identities can subscribe to many Networks, and each Identity has one "home" Network. In the future it is possible
we have to introduce a NetworkSubscription model for the n:n association between Network and Identity.

Each Group can be managed by many Identities, and many Identities can subscribe to a Group. In the future it is
possible we have to introduce models for those associations (GroupSubscription, GroupAdmin), though currently this
would be overkill.

# Locations #

![http://noserub.googlecode.com/svn/wiki/uml/locations.png](http://noserub.googlecode.com/svn/wiki/uml/locations.png)

The Locations sub-system is trivial: an Identity can define multiple Locations, plus we are interested in the latest
Location the Identity set.

# Twitter Account #

![http://noserub.googlecode.com/svn/wiki/uml/twitteraccount.png](http://noserub.googlecode.com/svn/wiki/uml/twitteraccount.png)

Each Identity can specify one TwitterAccount to which the Identity's messages are forwarded. In NoseRub 0.8, the
TwitterAccount stores username/password to authenticate with Twitter whereas in NoseRub 0.9 the authentication will
happen via OAuth.