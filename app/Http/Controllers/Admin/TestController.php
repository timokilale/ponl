<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware is now applied in the routes file
    }

    /**
     * Display a test page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['message' => 'Admin test route works!']);
    }
}
