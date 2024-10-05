<?php

define('CLIENT_ID', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('CLIENT_SECRET', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('REDIRECT_URI', 'https://captive_portal_url');
define('TENANT', 'xxxxx-xxxxx-xxxxx-xxxx');
define('AUTHORITY', 'https://login.microsoftonline.com/' . TENANT);
define('TOKEN_URL', AUTHORITY . '/oauth2/v2.0/token');
define('AUTHORIZATION_URL', AUTHORITY . '/oauth2/v2.0/authorize');
define('GRAPH_API_URL', 'https://graph.microsoft.com/v1.0/me');
define('UNIFI_USER', 'username');
define('UNIFI_PASSWORD', 'password');
define('UNIFI_URL', 'https://unifi-controller:8443');
define('UNIFI_SITE', 'default');
define('VALIDITY', '10800');

?>