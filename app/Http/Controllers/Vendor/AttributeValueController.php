<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Store;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    /**
     * Verify that the vendor owns the store (via attribute)
     */
    private function authorizeAttribute(Attribute $attribute, Store $store): void
    {
        if ($attribute->store_id !== $store->id) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Verify that the attribute value belongs to the store (via attribute)
     */
    private function authorizeAttributeValue(AttributeValue $attributeValue, Store $store): void
    {
        if ($attributeValue->attribute->store_id !== $store->id) {
            abort(403, 'Unauthorized');
        }
    }

    public function store(Request $request, Store $store)
    {
        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value.en' => 'required|string',
            'value.ar' => 'required|string',
        ]);

        $attribute = Attribute::findOrFail($request->attribute_id);
        $this->authorizeAttribute($attribute, $store);

        $value = AttributeValue::create([
            'attribute_id' => $request->attribute_id,
            'value' => $request->value,
        ]);

        return response()->json($value);
    }

    public function update(Request $request, Store $store, AttributeValue $attributeValue)
    {
        $this->authorizeAttributeValue($attributeValue, $store);

        $request->validate([
            'value.en' => 'required|string',
            'value.ar' => 'required|string',
        ]);

        $attributeValue->update([
            'value' => $request->value,
        ]);

        return response()->json($attributeValue);
    }

    public function destroy(Request $request, Store $store, AttributeValue $attributeValue)
    {
        $this->authorizeAttributeValue($attributeValue, $store);
        $attributeValue->delete();

        return response()->json(['success' => true]);
    }
}
