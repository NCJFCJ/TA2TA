<?php

namespace IAWP\Utils;

use IAWPSCOPED\League\Uri\Contracts\UriException;
use IAWPSCOPED\League\Uri\Uri;
/** @internal */
class URL
{
    private $url;
    public function __construct(string $url)
    {
        $this->url = $url;
    }
    public function is_valid_url() : bool
    {
        $valid_url = \filter_var($this->url, \FILTER_VALIDATE_URL);
        if (!$valid_url) {
            return \false;
        }
        try {
            // Recommend approach for uri validation: https://uri.thephpleague.com/uri/6.0/rfc3986/#uri-validation
            $components = Uri::createFromString($this->url);
            if (\is_null($components->getHost())) {
                return \false;
            }
            return \true;
        } catch (UriException $e) {
            return \false;
        }
    }
    public function get_domain() : ?string
    {
        if ($this->is_valid_url()) {
            $components = Uri::createFromString($this->url);
            $host = $components->getHost();
            if (!\is_null($host)) {
                return $host;
            }
        }
        return null;
    }
}
