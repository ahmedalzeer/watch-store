<?php

namespace App\Repositories\Vendor\Banner;

use App\Models\Banner;
use Illuminate\Support\Collection;

class BannerRepository implements BannerRepositoryInterface
{

    public function getAllBannersByStore(int $storeId): Collection
    {
        return Banner::where('store_id', $storeId)
            ->with('media')
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getBannerById(int $id): Banner
    {
        return Banner::findOrFail($id);
    }

    public function createBanner(array $data): Banner
    {
        return Banner::create($data);
    }

    public function updateBanner(Banner $banner, array $data): Banner
    {
        $banner->update($data);
        return $banner;
    }

    public function deleteBanner(Banner $banner): bool
    {
        return $banner->delete();
    }
}
