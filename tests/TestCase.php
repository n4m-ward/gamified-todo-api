<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Request;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function getFakeRequest($params, $method = 'GET'): Request
    {
        $request = new Request();
        $request->setMethod($method);
        $request->request->add($params);

        return $request;
    }
}
