Very simple authentication portal for Unifi Controller's Hotspot utilizing Azure AD/Entra ID authentication.
Upon client connecting to captive portal, Microsoft Login is prompted.

This uses excellent Unifi API Client from https://github.com/Art-of-WiFi/UniFi-API-client

Requirements:
- PHP json and PHP cURL modules

In Unifi Hotspot landing page settings after enabling External portal server set these hostnames to be allowed:

Pre-Authorization Allowances:
- login.microsoftonline.com
- msauth.net
- login.live.com
- msftauth.net
- azureedge.net
