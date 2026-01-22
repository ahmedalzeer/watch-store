<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\Vendor\BannerService;
use App\Http\Requests\Vendor\BannerRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BannerController extends Controller
{
    protected $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    /**
     * Get the current store ID from request or fallback to first store
     */
    private function getStoreId(Request $request): int
    {
        return $request->store_id ?? auth()->user()->stores()->first()->id;
    }

    /**
     * Check if user owns the store
     */
    private function authorizeStore(int $storeId): void
    {
        if (!auth()->user()->stores()->where('id', $storeId)->exists()) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $storeId = $this->getStoreId($request);

        $banners = $this->bannerService->getAllBanners($storeId)->map(function ($banner) {
            return [
                'id' => $banner->id,
                'title' => $banner->getTranslations('title'),
                'description' => $banner->getTranslations('description'),
                'type' => $banner->type,
                'link' => $banner->link,
                'active' => (bool) $banner->active,
                'image_url' => $banner->getFirstMediaUrl('banners', 'thumb') ?: $banner->getFirstMediaUrl('banners'),
            ];
        });

        return Inertia::render('Vendor/Banners/Index', [
            'banners' => [
                'data' => $banners,
                'links' => [],
            ],
            'storeId' => (int) $storeId,
        ]);
    }

    public function store(BannerRequest $request)
    {
        $storeId = $this->getStoreId($request);
        $this->authorizeStore($storeId);

        $banner = $this->bannerService->store($storeId, $request->validated());

        if ($request->image_path) {
            $this->handleMedia($banner, $request->image_path);
        }

        return redirect()->back();
    }

    public function update(BannerRequest $request, Banner $banner)
    {
        $storeId = $this->getStoreId($request);
        $this->authorizeStore($storeId);

        $this->bannerService->update($storeId, $banner, $request->validated());

        if ($request->filled('image_path')) {
            $banner->clearMediaCollection('banners');
            $this->handleMedia($banner, $request->image_path);
        }

        return redirect()->back();
    }

    public function destroy(Banner $banner)
    {
        $storeId = auth()->user()->stores()->where('id', $banner->store_id)->first()?->id;

        if (!$storeId) {
            abort(403);
        }

        $this->bannerService->delete($storeId, $banner);

        return redirect()->back();
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:banners,id',
            'status' => 'required|boolean',
        ]);

        $banner = Banner::findOrFail($validated['id']);
        $storeId = auth()->user()->stores()->where('id', $banner->store_id)->first()?->id;

        if (!$storeId) {
            abort(403);
        }

        $this->bannerService->toggleStatus($storeId, $banner, $validated['status']);

        return response()->json([
            'success' => true,
            'message' => __('messages.status_updated'),
        ]);
    }

    private function handleMedia(Banner $banner, string $path)
    {
        $fullPath = storage_path('app/public/' . $path);
        if (file_exists($fullPath)) {
            $banner->addMedia($fullPath)->toMediaCollection('banners');
        }
    }
}
