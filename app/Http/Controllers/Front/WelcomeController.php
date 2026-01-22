<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontBaseController;
use App\Models\Product;
use App\Models\Category;

class WelcomeController extends FrontBaseController
{
    public function index()
    {


        $dynamicSections = Category::with(['products' => function ($query) {
            $query->where('is_active', true)
                ->with(['brand', 'media'])
                ->latest()
                ->take(8);
        }])
            ->whereNull('parent_id')
            ->whereHas('products', function ($query) {
                $query->where('is_active', true);
            })
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->slug,
                    'title' => $category->getTranslations('name'),
                    'products' => $category->products
                ];
            });

        $bestSellers = Product::with(['brand', 'media'])
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        return inertia('FrontEnd/Welcome', [
            'heroBanners' => $this->getBanner(),
            'bestSellers' => $bestSellers,
            'dynamicSections' => $dynamicSections
        ]);
    }


    private function getBanner()
    {
        return [
            [
                'image' => '/images/0101.jpeg?auto=format&fit=crop&q=80&w=1920',
                'title' => 'PRECISION ELEGANCE',
                'subtitle' => 'تشكيلة رولكس الجديدة 2026'
            ],
            [
                'image' => '/images/0102.jpeg?auto=format&fit=crop&q=80&w=1920',
                'title' => 'LUXURY DEFINED',
                'subtitle' => 'تصاميم تعكس شخصيتك الفريدة'
            ],
            [
                'image' => '/images/0103.jpeg?auto=format&fit=crop&q=80&w=1920',
                'title' => 'PURE SOPHISTICATION',
                'subtitle' => 'دقة متناهية في كل ثانية'
            ],
            [
                'image' => '/images/0104.jpeg?auto=format&fit=crop&q=80&w=1920',
                'title' => 'MODERN HERITAGE',
                'subtitle' => 'عراقة الماضي بروح الحاضر'
            ],
            [
                'image' => '/images/0105.jpeg?auto=format&fit=crop&q=80&w=1920',
                'title' => 'ELITE CRAFTSMANSHIP',
                'subtitle' => 'صناعة يدوية لأصحاب الذوق الرفيع'
            ],
            [
                'image' => '/images/0106.jpeg?auto=format&fit=crop&q=80&w=1920',
                'title' => 'BEYOND LUXURY',
                'subtitle' => 'أكثر من مجرد ساعة.. إنها تحفة'
            ],
            [
                'image' => '/images/0107.jpeg?auto=format&fit=crop&q=80&w=1920',
                'title' => 'ICONIC MOMENTS',
                'subtitle' => 'وثق لحظاتك بأناقة لا تنتهي'
            ],
            [
                'image' => '/images/0108.jpeg?auto=format&fit=crop&q=80&w=1920',
                'title' => 'ROYAL CHRONO',
                'subtitle' => 'القوة والأناقة في تصميم واحد'
            ],
        ];
    }
}
