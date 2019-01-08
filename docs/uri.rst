=========
URI Model
=========

.. include:: includes/uri/introduction.rst

.. rst-class:: precede-list

    Minimal non-optional RFC 3986 normalization is applied by default:

- converts scheme to lowercase
- converts host to lowercase
- removes the default port

--------------
Creating a URI
--------------

A new ``Uri`` instance is created by passing a URL string to the constructor:

.. code-block:: php

    <?php

    use webignition\Uri\Uri;

    $uri = new Uri('https://example.com');

----------------
Component Access
----------------

.. code-block:: php

    <?php

    use webignition\Uri\Uri;

    $uri = new Uri('https://user:password@example.com:8080/path?query#fragment');

    $uri->getScheme();
    // "https"

    $uri->getUserInfo();
    // "user:password"

    $uri->getHost();
    // "example.com"

    $uri->getPort();
    // 8080

    $uri->getAuthority();
    // "user:password@example.com:8080"

    $uri->getPath();
    // "/path"

    $uri->getQuery();
    // "query"

    $uri->getFragment();
    // "fragment"

----------------------
Component Modification
----------------------

The ``Uri::with*()`` are used to set components. A ``Uri`` is immutable. The return value is a new ``Uri`` instance.

.. code-block:: php

    <?php

    use webignition\Uri\Uri;

    $uri = new Uri('https://user:password@example.com:8080/path?query#fragment');
    (string) $uri;
    // "https://user:password@example.com:8080/path?query#fragment"

    $uri = $uri->withScheme('http');
    (string) $modifiedUri;
    // "http://user:password@example.com:8080/path?query#fragment"

    $uri = $uri->withUserInfo('new-user', 'new-password');
    (string) $modifiedUri;
    // "http://new-user:new-password@example.com:8080/path?query#fragment"

    $uri = $uri->withUserInfo('');
    (string) $modifiedUri;
    // "http://example.com:8080/path?query#fragment"

    $uri = $uri->withHost('new.example.com');
    (string) $modifiedUri;
    // "http://new.example.com:8080/path?query#fragment"

    $uri = $uri->withPort(null);
    (string) $modifiedUri;
    // "http://new.example.com/path?query#fragment"

    $uri = $uri->withPath('');
    (string) $modifiedUri;
    // "http://new.example.com?query#fragment"

    $uri = $uri->withQuery('');
    (string) $modifiedUri;
    // "http://new.example.com#fragment"

    $uri = $uri->withFragment('');
    (string) $modifiedUri;
    // "http://new.example.com"

--------------------------
Non-Optional Normalization
--------------------------

.. code-block:: php

    <?php

    use webignition\Uri\Uri;

    $uri = new Uri('HTTPS://EXAMPLE.com:443');

    $uri->getScheme();
    // "https"

    $uri->getHost();
    // "example.com"

    $uri->getPort();
    // null
