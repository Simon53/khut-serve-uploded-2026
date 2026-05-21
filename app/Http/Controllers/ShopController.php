<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\MainMenu;
use App\Models\SubMenu;
use App\Models\ChildMenu;
use App\Models\CategoryBanner;
use App\Http\Controllers\StockController;

class ShopController extends Controller
{
        
            
      /* public function index()
            {
                $mainMenus = MainMenu::with('subMenus.childMenus')->get();

                $products = Product::where('published_site', 'Y')
                    ->orderByRaw("CASE WHEN stock_status = 'N' THEN 1 ELSE 0 END")
                    ->latest()
                    ->paginate(50);

                $bannerRecord = CategoryBanner::inRandomOrder()->first();

                if ($bannerRecord && !empty($bannerRecord->banner_image)) {

                // remove leading "storage/" if exists
                $cleanPath = preg_replace('#^/?storage/#', '', $bannerRecord->banner_image);

                $bannerPath  = '/storage/' . $cleanPath;
                $bannerTitle = $bannerRecord->title ?? 'Shop';

            } else {

                $bannerPath  = '/storage/category_banners/product_banner.jpg';
                $bannerTitle = 'Shop';
            }

                $category = (object)[
                    'name'   => 'Shop',
                    'banner' => $bannerPath,
                    'title'  => $bannerTitle
                ];

                $stocks = app(StockController::class)->getStocksForSkus(
                    $products->pluck('product_barcode')->filter()->values()->all()
                );

                return view('product-categories.index', compact('mainMenus', 'products', 'category', 'stocks'));
            }*/

    
     public function index()
{
    $mainMenus = MainMenu::with('subMenus.childMenus')->get();

    /*
    |--------------------------------------------------------------------------
    | Get All Products
    |--------------------------------------------------------------------------
    */
    $products = Product::with(['thumbnails', 'options'])
        ->where('published_site', 'Y')
        ->latest()
        ->get();


    /*
    |--------------------------------------------------------------------------
    | Khut Catalog
    |--------------------------------------------------------------------------
    */
    $khutCatalog = app(\App\Services\KhutCatalogService::class)->all();

    /*
    |--------------------------------------------------------------------------
    | Normalize Barcode
    |--------------------------------------------------------------------------
    */
    $khutCatalogNorm = [];

    foreach($khutCatalog as $barcode => $item) {

        $khutCatalogNorm[ltrim((string)$barcode,'0')] = $item;

    }


    /*
    |--------------------------------------------------------------------------
    | Remove Product Without Barcode
    |--------------------------------------------------------------------------
    */
    $products = $products->filter(function($product){

        return !empty($product->product_barcode);

    });


    /*
    |--------------------------------------------------------------------------
    | Sort Products
    |--------------------------------------------------------------------------
    */
    $sorted = $products->sortByDesc(function($product) use ($khutCatalogNorm) {

        $allBarcodes = [];

        /*
        |--------------------------------------------------------------------------
        | Main Barcode
        |--------------------------------------------------------------------------
        */
        if (!empty($product->product_barcode)) {

            $allBarcodes[] = $product->product_barcode;

        }


        /*
        |--------------------------------------------------------------------------
        | Thumbnail Barcode
        |--------------------------------------------------------------------------
        */
        foreach ($product->thumbnails as $thumb) {

            if (!empty($thumb->thumb_barcode)) {

                $allBarcodes[] = $thumb->thumb_barcode;

            }
        }


        /*
        |--------------------------------------------------------------------------
        | Option Barcode
        |--------------------------------------------------------------------------
        */
        foreach ($product->options as $option) {

            if (!empty($option->barcode)) {

                $allBarcodes[] = $option->barcode;

            }
        }


        /*
        |--------------------------------------------------------------------------
        | Remove Duplicate
        |--------------------------------------------------------------------------
        */
        $allBarcodes = array_unique($allBarcodes);


        /*
        |--------------------------------------------------------------------------
        | Default
        |--------------------------------------------------------------------------
        */
        $inStock = false;


        /*
        |--------------------------------------------------------------------------
        | Check Stock
        |--------------------------------------------------------------------------
        */
        foreach ($allBarcodes as $barcode) {

            $barcodeNorm = ltrim($barcode, '0');

            $stock = (int)($khutCatalogNorm[$barcodeNorm]['stock'] ?? 0);

            if ($stock > 0) {

                $inStock = true;

                break;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Priority
        |--------------------------------------------------------------------------
        |
        | In Stock Product => Large Priority
        | Sold Out Product => Low Priority
        |
        */

        return ($inStock ? 1000000000 : 0) + $product->id;

    })->values();


    /*
    |--------------------------------------------------------------------------
    | Manual Pagination
    |--------------------------------------------------------------------------
    */
    $currentPage = request()->get('page', 1);

    $perPage = 50;

    $products = new \Illuminate\Pagination\LengthAwarePaginator(

        $sorted->forPage($currentPage, $perPage)->values(),

        $sorted->count(),

        $perPage,

        $currentPage,

        [
            'path' => request()->url(),
            'query' => request()->query(),
        ]

    );


    /*
    |--------------------------------------------------------------------------
    | Banner
    |--------------------------------------------------------------------------
    */
    $bannerRecord = CategoryBanner::inRandomOrder()->first();

    if ($bannerRecord && !empty($bannerRecord->banner_image)) {

        $cleanPath = preg_replace('#^/?storage/#', '', $bannerRecord->banner_image);

        $bannerPath  = '/storage/' . $cleanPath;

        $bannerTitle = $bannerRecord->title ?? 'Shop';

    } else {

        $bannerPath  = '/storage/category_banners/product_banner.jpg';

        $bannerTitle = 'Shop';
    }


    /*
    |--------------------------------------------------------------------------
    | Category Object
    |--------------------------------------------------------------------------
    */
    $category = (object)[

        'name'   => 'Shop',

        'banner' => $bannerPath,

        'title'  => $bannerTitle

    ];


    return view(
        'product-categories.index',
        compact('mainMenus', 'products', 'category')
    );
}



    /**
     * Generate proper banner URL from DB path
     */
    private function getBannerUrl($bannerRecord)
    {
        $path = ltrim($bannerRecord->banner_image, '/');

        // Case 1: DB has full URL
        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        // Case 2: DB has storage path (with or without leading 'storage/')
        $path = preg_replace('#^storage/#', '', $path);

        $baseUrl = rtrim(env('ADMIN_BASE_URL'), '/');

        return $baseUrl . '/storage/' . $path;
    }
   
            




    // ✅ Main Category (slug-based)
    public function listBySlug($slug)
    {
        $mainMenus = MainMenu::with('subMenus.childMenus')->get();
        $category = MainMenu::where('name', $slug)->firstOrFail();

        $subIds = $category->subMenus->pluck('id')->toArray();
        $childIds = \App\Models\ChildMenu::whereIn('sub_menu_id', $subIds)->pluck('id')->toArray();

        $products = Product::where('published_site','Y')
            ->where(function($q) use($category, $subIds, $childIds){
                $q->where('main_menu_id', $category->id)
                  ->orWhereIn('sub_menu_id', $subIds)
                  ->orWhereIn('child_menu_id', $childIds);
            })
            ->orderByRaw("CASE WHEN stock_status = 'N' THEN 1 ELSE 0 END")
            ->latest()
            ->paginate(12);

        $banner = CategoryBanner::where('main_menu_id', $category->id)->first();

        $stocks = app(StockController::class)->getStocksForSkus(
            $products->pluck('product_barcode')->filter()->values()->all()
        );

        return view('product-categories.index', compact('mainMenus','products','category','banner','stocks'));
    }

    // ✅ Sub / Child Category (id-based)
    public function listById($id)
    {
        $mainMenus = MainMenu::with('subMenus.childMenus')->get();
        $products = [];
        $category = null;
        $banner = null;
        $mainMenu = null;
        $subMenu = null;
        $childMenu = null;

        $routeName = request()->route()->getName();

        if ($routeName == 'subcategory.list') {
            $category = \App\Models\SubMenu::findOrFail($id);
            $childIds = $category->childMenus->pluck('id')->toArray();

            $products = Product::where('published_site','Y')
                ->where(function($q) use($id, $childIds){
                    $q->where('sub_menu_id', $id)
                      ->orWhereIn('child_menu_id', $childIds);
                })
                ->orderByRaw("CASE WHEN stock_status = 'N' THEN 1 ELSE 0 END")
                ->latest()
                ->paginate(12);

            $banner = CategoryBanner::where('main_menu_id', $category->main_menu_id)->first();
            $mainMenu = MainMenu::find($category->main_menu_id);
            $subMenu = $category;
        } 
        elseif ($routeName == 'childcategory.list') {
            $category = \App\Models\ChildMenu::findOrFail($id);

            $products = Product::where('child_menu_id', $id)
                ->where('site_view_status','Y')
                ->orderByRaw("CASE WHEN stock_status = 'N' THEN 1 ELSE 0 END")
                ->latest()
                ->paginate(12);

            $banner = CategoryBanner::where('main_menu_id', $category->main_menu_id)->first();
            $mainMenu = MainMenu::find($category->main_menu_id);
            $subMenu = \App\Models\SubMenu::find($category->sub_menu_id);
            $childMenu = $category;
        }

        $stocks = $products->isNotEmpty()
            ? app(StockController::class)->getStocksForSkus(
                $products->pluck('product_barcode')->filter()->values()->all()
            )
            : [];

        return view('product-categories.index', compact(
            'mainMenus','products','category','banner','mainMenu','subMenu','childMenu','stocks'
        ));
    }
}
