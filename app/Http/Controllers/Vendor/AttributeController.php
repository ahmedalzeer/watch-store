<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttributeController extends Controller
{
    /**
     * Verify that the vendor owns the store
     */
    private function authorizeStore(Store $store): void
    {
        if ($store->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Verify that the attribute belongs to the store
     */
    private function authorizeAttribute(Attribute $attribute, Store $store): void
    {
        if ($attribute->store_id !== $store->id) {
            abort(404);
        }
    }

    public function index(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        $attributes = Attribute::where('store_id', $store->id)
            ->with('values')
            ->get();

        return response()->json([
            'attributes' => $attributes
        ]);
    }

    public function store(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        $request->validate([
            'name.en' => 'required|string',
            'name.ar' => 'required|string',
        ]);

        $attribute = Attribute::create([
            'store_id' => $store->id,
            'name' => $request->name,
        ]);

        return response()->json($attribute);
    }

    public function update(Request $request, Store $store, Attribute $attribute)
    {
        $this->authorizeStore($store);
        $this->authorizeAttribute($attribute, $store);

        $request->validate([
            'name.en' => 'required|string',
            'name.ar' => 'required|string',
        ]);

        $attribute->update([
            'name' => $request->name,
        ]);

        return response()->json($attribute);
    }

    public function destroy(Request $request, Store $store, Attribute $attribute)
    {
        $this->authorizeStore($store);
        $this->authorizeAttribute($attribute, $store);
        $attribute->delete();

        return response()->json(['success' => true]);
    }
}
