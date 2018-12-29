<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class FinanceiroController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        return view('admin.financeiro.index');
    }
}
