.. code-block:: php

    <?php

    use webignition\Uri\Normalizer;

    $uri = new Uri('http://example.com/path?c=cow&a=apple&b=bear#fragment');

    $normalizedUri = Normalizer::normalize(
        $uri,
        Normalizer::SORT_QUERY_PARAMETERS | Normalizer::REMOVE_FRAGMENT
    );

    (string) $normalizedUri;
    // "http://example.com/path?a=apple&b=bear&c=cow"
