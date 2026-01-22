<?php

namespace App\Services\Vendor;

use App\Models\Banner;
use App\Repositories\Vendor\Banner\BannerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BannerService
{
    protected $bannerRepository;

    public function __construct(BannerRepositoryInterface $bannerRepository)
    {
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * Get all banners for a specific store
     */
    public function getAllBanners(int $storeId)
    {
        return $this->bannerRepository->getAllBannersByStore($storeId);
    }

    /**
     * Store a new banner
     */
    public function store(int $storeId, array $data)
    {
        $bannerData = [
            'store_id' => $storeId,
            'type' => $data['type'],
            'link' => $data['link'] ?? null,
            'title' => [
                'ar' => $data['title']['ar'],
                'en' => $data['title']['en'],
            ],
            'description' => [
                'ar' => $data['description']['ar'] ?? null,
                'en' => $data['description']['en'] ?? null,
            ],
            'active' => $data['active'] ?? true,
        ];

        return $this->bannerRepository->createBanner($bannerData);
    }

    /**
     * Update an existing banner
     */
    public function update(int $storeId, Banner $banner, array $data)
    {
        // Authorization: check if banner belongs to vendor's store
        if (!$this->isAuthorized($storeId, $banner)) {
            abort(403, 'Unauthorized action.');
        }

        $updateData = [
            'type' => $data['type'],
            'link' => $data['link'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'],
            'active' => $data['active'] ?? false,
        ];

        return $this->bannerRepository->updateBanner($banner, $updateData);
    }

    /**
     * Delete a banner
     */
    public function delete(int $storeId, Banner $banner)
    {
        if (!$this->isAuthorized($storeId, $banner)) {
            abort(403);
        }

        $banner->clearMediaCollection('banners');
        return $this->bannerRepository->deleteBanner($banner);
    }

    /**
     * Toggle banner status
     */
    public function toggleStatus(int $storeId, Banner $banner, bool $status)
    {
        if (!$this->isAuthorized($storeId, $banner)) {
            abort(403);
        }

        $banner->active = $status;
        $banner->save();

        return $banner;
    }

    /**
     * Check if banner belongs to the current store
     */
    private function isAuthorized(int $storeId, Banner $banner): bool
    {
        return $banner->store_id === $storeId;
    }
}
