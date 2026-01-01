<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends AdminBaseController
{
    public function index()
    {
        return Inertia::render("Admin/Index");
    }
}
