<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainMenu;
use App\Models\SubMenu;
use App\Models\ChildMenu;
use App\Models\Product;
use App\Models\CategoryBanner;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\StockController;
use App\Services\KhutCatalogService;

class CategoryController extends Controller
{

    public function productsByMenu()
    {
        $products = Product::where('published_site', 'Y')
            ->reorder()
            ->orderByDesc('id')
            ->paginate(50);

        $stocks = [];

        foreach ($products as $product) {
            $sku = trim((string) $product->product_barcode);

            if ($sku === '') continue;

            $stocks[$sku] = app(\App\Http\Controllers\StockController::class)
                ->getStock($sku);
        }

        return view('product-categories.partials.products', compact('products', 'stocks'));
    }

    public function categoryProducts($id){
        $products = Product::with(['mainMenu', 'subMenu', 'childMenu'])
            ->where('published_site', 'Y')
            ->where(function ($q) use ($id) {
                $q->where('main_menu_id', $id)
                  ->orWhere('sub_menu_id', $id)
                  ->orWhere('child_menu_id', $id);
            })
            ->reorder()
            ->orderByDesc('id')
            ->paginate(50);

        return view('product-categories.index', compact('products'));
    }

    public function listBySlug($slug){
        $mainMenus = MainMenu::with('subMenus.childMenus')->get();
    
        // Banner
        $banner = CategoryBanner::first();
        $bannerPath = $banner && !empty($banner->banner_image)
            ? '/storage/' . ltrim(preg_replace('#^https?://[^/]+/#', '', preg_replace('#^/?storage/#', '', $banner->banner_image)), '/')
            : '/storage/category_banners/product_banner.jpg';
    
        if (strtolower($slug) === 'new-arrivals') {
            $products = Product::where('new_arrivals', 'Y')
                ->where('published_site', 'Y')
                ->where('site_view_status', 'Y')
                ->reorder()            
                ->orderByDesc('id')
                ->paginate(50);
    
            $category = (object)[
                'name' => 'New Arrivals',
                'title' => 'New Arrivals',
                'banner' => $bannerPath
            ];
    
            return view('product-categories.new-arrivals', compact('products', 'mainMenus', 'category'));
    
        } elseif (strtolower($slug) === 'patchwork') {
            $products = Product::where('patchwork', 'Y')
                ->where('published_site', 'Y')
                ->where('site_view_status', 'Y')
                ->reorder()            
                ->orderByDesc('id')
                ->paginate(50);
    
            $category = (object)[
                'name' => 'Patchwork',
                'title' => 'Patchwork',
                'banner' => $bannerPath
            ];
    
            return view('product-categories.patchwork', compact('products', 'mainMenus', 'category'));
        }
    
        // Normal category slug
        $normalizedSlug = strtolower($slug);
        $category = $mainMenus->first(function ($menu) use ($normalizedSlug) {
            return strtolower(str_replace(' ', '-', $menu->name)) === $normalizedSlug;
        });
    
        if (!$category) abort(404);
    
        $subIds   = $category->subMenus->pluck('id')->toArray();
        $childIds = ChildMenu::whereIn('sub_menu_id', $subIds)->pluck('id')->toArray();
    
        $products = Product::where('published_site', 'Y')
            ->where(function ($q) use ($category, $subIds, $childIds) {
                $q->where('main_menu_id', $category->id)
                  ->orWhereIn('sub_menu_id', $subIds)
                  ->orWhereIn('child_menu_id', $childIds);
            })
            ->orderBy('id', 'desc')
            ->paginate(50);
        
      
    
        $category->banner = $bannerPath;
    
        return view('product-categories.index', compact('products', 'mainMenus', 'category'));
    }

    public function listById($id)
    {
        $mainMenus = MainMenu::with('subMenus.childMenus')->get();
        $routeName = request()->route()->getName();

        // SUB CATEGORY
        if ($routeName === 'subcategory.list') {

            $category = SubMenu::findOrFail($id);
            $childIds = $category->childMenus->pluck('id')->toArray();

            $products = Product::where('published_site', 'Y')
                ->where(function ($q) use ($id, $childIds) {
                    $q->where('sub_menu_id', $id)
                      ->orWhereIn('child_menu_id', $childIds);
                })
                ->reorder()
                ->orderBy('id', 'desc')
                ->paginate(50);

            $banner = CategoryBanner::where('main_menu_id', $category->main_menu_id)->first();

            // ✅ FIX START
            $catalogRaw = app(KhutCatalogService::class)->all();
            $catalog = [];
            foreach ($catalogRaw as $barcode => $item) {
                $catalog[ltrim((string)$barcode, '0')] = $item;
            }

            $stocks = [];
            foreach ($products as $product) {
                $sku = trim((string)($product->product_barcode ?? ''));
                if ($sku === '') continue;

                $skuNorm = ltrim($sku, '0');
                $stocks[$sku] = (int)($catalog[$skuNorm]['stock'] ?? 0);
            }
            // ✅ FIX END

            return view('product-categories.index', compact(
                'mainMenus',
                'products',
                'category',
                'banner',
                'stocks'
            ));
        }

        // CHILD CATEGORY
        if ($routeName === 'childcategory.list') {

            $childMenu = ChildMenu::findOrFail($id);

            $products = Product::where('published_site', 'Y')
                ->where('child_menu_id', $id)
                ->reorder()
                ->orderByDesc('id')
                ->paginate(50);

            $subMenu  = $childMenu->subMenu;
            $mainMenu = $subMenu ? $subMenu->mainMenu : null;

            $banner = $mainMenu
                ? CategoryBanner::where('main_menu_id', $mainMenu->id)->first()
                : (object)[
                    'banner_image' => asset('assets/images/product_banner.jpg'),
                    'title' => $childMenu->name
                ];

            // ✅ FIX START
            $catalogRaw = app(KhutCatalogService::class)->all();
            $catalog = [];
            foreach ($catalogRaw as $barcode => $item) {
                $catalog[ltrim((string)$barcode, '0')] = $item;
            }

            $stocks = [];
            foreach ($products as $product) {
                $sku = trim((string)($product->product_barcode ?? ''));
                if ($sku === '') continue;

                $skuNorm = ltrim($sku, '0');
                $stocks[$sku] = (int)($catalog[$skuNorm]['stock'] ?? 0);
            }
            // ✅ FIX END

            return view('product-categories.index', compact(
                'mainMenus',
                'products',
                'childMenu',
                'subMenu',
                'mainMenu',
                'banner',
                'stocks'
            ));
        }

        abort(404);
    }

    public function list($name)
    {
        $mainMenu = MainMenu::whereRaw('LOWER(name) = ?', [strtolower($name)])->firstOrFail();

        $products = Product::where('published_site', 'Y')
            ->where('main_menu_id', $mainMenu->id)
            ->reorder()
            ->orderByDesc('id')
            ->paginate(50);

        return view('category.index', compact('products', 'mainMenu'));
    }
}