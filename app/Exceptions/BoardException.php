<?php

namespace App\Exceptions;

use Exception;

class BoardException extends Exception
{
    public function render($request): \Illuminate\Http\JsonResponse
    {
    }
}
