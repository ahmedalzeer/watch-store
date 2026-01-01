<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\VendorBaseController;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends VendorBaseController
{
    public function index()
    {
        return Inertia::render("Vendor/Index");
    }
}
