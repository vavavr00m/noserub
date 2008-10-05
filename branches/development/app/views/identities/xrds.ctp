<xrds:XRDS
    xmlns:xrds="xri://$xrds"
    xmlns="xri://$xrd*($v*2.0)">
  <XRD>
    <Service priority="0">
      <Type>http://specs.openid.net/auth/2.0/signon</Type>
      <Type>http://openid.net/signon/1.1</Type>
      <URI><?php echo $server; ?>auth</URI>
    </Service>
  </XRD>
  <XRD xmlns="xri://$xrd*($v*2.0)" xml:id="oauth" xmlns:simple="http://xrds-simple.net/core/1.0" version="2.0">
    <Type>xri://$xrds*simple</Type>
      <Service>
   <URI><?php echo $server; ?>pages/omb/request_token</URI>
   <Type>http://oauth.net/core/1.0/endpoint/request</Type>
   <Type>http://oauth.net/core/1.0/parameters/auth-header</Type>
   <Type>http://oauth.net/core/1.0/parameters/post-body</Type>
   <Type>http://oauth.net/core/1.0/signature/HMAC-SHA1</Type>
   <LocalID><?php echo $server.$username; ?></LocalID>
</Service>
  <Service>
   <URI><?php echo $server; ?>pages/omb/authorize</URI>
   <Type>http://oauth.net/core/1.0/endpoint/authorize</Type>
   <Type>http://oauth.net/core/1.0/parameters/auth-header</Type>
   <Type>http://oauth.net/core/1.0/parameters/post-body</Type>
   <Type>http://oauth.net/core/1.0/signature/HMAC-SHA1</Type>
</Service>
  <Service>
   <URI><?php echo $server; ?>pages/omb/access_token</URI>
   <Type>http://oauth.net/core/1.0/endpoint/access</Type>
   <Type>http://oauth.net/core/1.0/parameters/auth-header</Type>
   <Type>http://oauth.net/core/1.0/parameters/post-body</Type>
   <Type>http://oauth.net/core/1.0/signature/HMAC-SHA1</Type>
</Service>
  <Service>
   <Type>http://oauth.net/core/1.0/endpoint/resource</Type>
   <Type>http://oauth.net/core/1.0/parameters/auth-header</Type>
   <Type>http://oauth.net/core/1.0/parameters/post-body</Type>
   <Type>http://oauth.net/core/1.0/signature/HMAC-SHA1</Type>
</Service>
</XRD>
 <XRD xmlns="xri://$xrd*($v*2.0)" xml:id="omb" xmlns:simple="http://xrds-simple.net/core/1.0" version="2.0">
  <Type>xri://$xrds*simple</Type>
  <Service>
   <URI><?php echo $server; ?>pages/omb/post_notice</URI>
   <Type>http://openmicroblogging.org/protocol/0.1/postNotice</Type>
</Service>
  <Service>
   <URI><?php echo $server; ?>pages/omb/update_profile</URI>
   <Type>http://openmicroblogging.org/protocol/0.1/updateProfile</Type>
</Service>
</XRD>
 <XRD xmlns="xri://$xrd*($v*2.0)" version="2.0">
  <Type>xri://$xrds*simple</Type>
  <Service>
   <URI>#oauth</URI>
   <Type>http://oauth.net/discovery/1.0</Type>
</Service>
  <Service>
   <URI>#omb</URI>
   <Type>http://openmicroblogging.org/protocol/0.1</Type>
</Service>
</XRD>
</xrds:XRDS>