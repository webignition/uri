.. code-block:: php

    <?php

    use webignition\Uri\Uri;

    $uri = new Uri('http://example.com/path?query#fragment');

    $uri->getScheme();
    // "http"

    $uri->getQuery();
    // "query"

    $modifiedUri = $uri
        ->withScheme('https')
        ->withPath('/modified-path')
        ->withQuery('foo=bar')
        ->withFragment('');
    (string) $modifiedUri;
    // https://example.com/modified-path?foo=bar
