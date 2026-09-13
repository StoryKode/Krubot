<?php

namespace KrubiK\Drivers\Strategies;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface DeferredResponse extends Responsable
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     * @return JsonResponse
    */
    public function toResponse($request): JsonResponse;
}
