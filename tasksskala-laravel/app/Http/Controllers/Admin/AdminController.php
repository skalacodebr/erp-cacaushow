<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        // Redireciona para o dashboard financeiro
        return redirect()->route('admin.dashboard-financeira.index');
    }
}
