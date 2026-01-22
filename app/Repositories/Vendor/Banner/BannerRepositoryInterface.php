<?php

namespace App\Repositories\Vendor\Banner;

use App\Models\Banner;
use Illuminate\Support\Collection;

interface BannerRepositoryInterface
{
    public function getAllBannersByStore(int $storeId): Collection;

    public function getBannerById(int $id): Banner;

    public function createBanner(array $data): Banner;

    public function updateBanner(Banner $banner, array $data): Banner;

    public function deleteBanner(Banner $banner): bool;
}
