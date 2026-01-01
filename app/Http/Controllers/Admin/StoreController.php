<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use App\Http\Requests\Admin\StoreRequest;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::with('user')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name->ar', 'like', "%{$search}%")
                        ->orWhere('name->en', 'like', "%{$search}%")
                        ->orWhere('subdomain', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Stores/Index', [
            'stores' => $stores,
            'users' => User::role('vendor')->get(['id', 'name', 'email'])
        ]);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $store = Store::create($data);

        if ($request->logo_path && Storage::disk('public')->exists($request->logo_path)) {
            $store->addMedia(storage_path('app/public/' . $request->logo_path))
                ->toMediaCollection('store_logo');
        }

        return redirect()->back();
    }

    public function update(StoreRequest $request, Store $store)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $store->update($data);

        if ($request->logo_path && Storage::disk('public')->exists($request->logo_path)) {
            $store->addMedia(storage_path('app/public/' . $request->logo_path))
                ->toMediaCollection('store_logo');
        }

        return redirect()->back();
    }

    public function destroy(Store $store)
    {
        $store->delete();
        return back();
    }
}
