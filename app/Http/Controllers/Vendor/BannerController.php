<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Store;
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
     * Verify that the vendor owns the store
     */
    private function authorizeStore(Store $store): void
    {
        if ($store->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Verify that the banner belongs to the store
     */
    private function authorizeBanner(Banner $banner, Store $store): void
    {
        if ($banner->store_id !== $store->id) {
            abort(404);
        }
    }

    public function index(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        $paginatedBanners = $this->bannerService->paginate($store->id);

        $banners = $paginatedBanners->getCollection()->map(function ($banner) {
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
                'links' => $paginatedBanners->linkCollection()->toArray(),
            ],
            'store' => $store,
        ]);
    }

    public function create(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        return Inertia::render('Vendor/Banners/Create', [
            'store' => $store,
        ]);
    }

    public function store(BannerRequest $request, Store $store)
    {
        $this->authorizeStore($store);

        $banner = $this->bannerService->store($store->id, $request->validated());

        if ($request->image_path) {
            $this->handleMedia($banner, $request->image_path);
        }

        return redirect()->route('vendor.banners.show', ['store' => $store, 'banner' => $banner]);
    }

    public function show(Request $request, Store $store, Banner $banner)
    {
        $this->authorizeStore($store);
        $this->authorizeBanner($banner, $store);

        return Inertia::render('Vendor/Banners/Show', [
            'banner' => $banner,
            'store' => $store,
        ]);
    }

    public function edit(Request $request, Store $store, Banner $banner)
    {
        $this->authorizeStore($store);
        $this->authorizeBanner($banner, $store);

        return Inertia::render('Vendor/Banners/Edit', [
            'banner' => $banner,
            'store' => $store,
        ]);
    }

    public function update(BannerRequest $request, Store $store, Banner $banner)
    {
        $this->authorizeStore($store);
        $this->authorizeBanner($banner, $store);

        $this->bannerService->update($store->id, $banner, $request->validated());

        if ($request->filled('image_path')) {
            $banner->clearMediaCollection('banners');
            $this->handleMedia($banner, $request->image_path);
        }

        return redirect()->route('vendor.banners.show', ['store' => $store, 'banner' => $banner]);
    }

    public function destroy(Request $request, Store $store, Banner $banner)
    {
        $this->authorizeStore($store);
        $this->authorizeBanner($banner, $store);

        $this->bannerService->delete($store->id, $banner);

        return redirect()->route('vendor.banners.index', ['store' => $store]);
    }

    public function updateStatus(Request $request, Store $store)
    {
        $this->authorizeStore($store);

        $validated = $request->validate([
            'id' => 'required|exists:banners,id',
            'status' => 'required|boolean',
        ]);

        $banner = Banner::findOrFail($validated['id']);
        $this->authorizeBanner($banner, $store);

        $this->bannerService->toggleStatus($store->id, $banner, $validated['status']);

        return response()->json([
            'success' => true,
            'message' => 'Status updated',
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
