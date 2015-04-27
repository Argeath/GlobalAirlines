Facebook API SDK module for Kohana 3.3
==========

https://github.com/facebook/facebook-php-sdk

Facebook PHP SDK (v.3.2.2)

The Facebook Platform is a set of APIs that make your app more social.

This repository contains the open source PHP SDK that allows you to access Facebook Platform from your PHP app. Except as otherwise noted, the Facebook PHP SDK is licensed under the Apache Licence, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0.html).

### How to use it?

On your page, you should either check if the user already allowed the application access, using `FB::instance()->getUser()`. This can be either in an AJAX call or your controller.

If `FB::instance()->getUser()` returns `false`, you should either redirect or popup the permission dialog pointing to `FB::instance()->getLoginUrl()`.

The callback URL is `facebook/auth` by default, that issues `FB::instance()->getUser()` and `FB::instance->getAccessToken()` so you can use the user app data right away.

Then issue your OpenGraph calls using `FB::instance()->get('')`. If you want to extend the FB class, extend `Core_FB`.
