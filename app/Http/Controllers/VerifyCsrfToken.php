<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerifyCsrfToken extends Controller
{
    protected $except = [
        'webhook/shipment',
    ];
}
