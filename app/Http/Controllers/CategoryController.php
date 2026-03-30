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


   /*public function productsByMenu()
{
    $products = Product::where('published_site', 'Y')
        ->latest()
        ->paginate(50);

    $stocks = []; // 🔥 অবশ্যই define করতে হবে

    foreach ($products as $product) {

        $sku = trim((string) $product->product_barcode);

        if ($sku === '') {
            continue;
        }

        $stock = app(\App\Http\Controllers\StockController::class)
                    ->getStock($sku);

        $stocks[$sku] = (int) $stock;
    }

    return view('product-categories.partials.products', compact('products', 'stocks'));
}*/
  
  public function productsByMenu()
{
    $products = Product::where('published_site', 'Y')
        ->latest()
        ->paginate(50);

    $stocks = [];

    foreach ($products as $product) {
        $sku = trim((string) $product->product_barcode);

        if ($sku === '') {
            continue;
        }

        $stocks[$sku] = app(\App\Http\Controllers\StockController::class)
            ->getStock($sku);
    }

    return view('product-categories.partials.products', compact('products', 'stocks'));
}




    /**
     * Mixed Category (Main / Sub / Child)
     */
    public function categoryProducts($id)
    {
        $products = Product::with(['mainMenu', 'subMenu', 'childMenu'])
            ->where('published_site', 'Y')
            ->where(function ($q) use ($id) {
                $q->where('main_menu_id', $id)
                  ->orWhere('sub_menu_id', $id)
                  ->orWhere('child_menu_id', $id);
            })
            //->orderByRaw("CASE WHEN stock_status = 'N' THEN 1 ELSE 0 END")
            ->latest()
            ->paginate(50);

        return view('product-categories.index', compact('products'));
    }

    /**
     * Main Category by Slug
     */
   /* public function listBySlug($slug)
    {
        $mainMenus = MainMenu::with('subMenus.childMenus')->get();

        $normalizedSlug = strtolower($slug);

        $category = $mainMenus->first(function ($menu) use ($normalizedSlug) {
            return strtolower(str_replace(' ', '-', $menu->name)) === $normalizedSlug;
        });

        if (!$category) {
            abort(404, 'Category not found');
        }

        $subIds   = $category->subMenus->pluck('id')->toArray();
        $childIds = ChildMenu::whereIn('sub_menu_id', $subIds)->pluck('id')->toArray();

        $products = Product::where('published_site', 'Y')
            ->where(function ($q) use ($category, $subIds, $childIds) {
                $q->where('main_menu_id', $category->id)
                  ->orWhereIn('sub_menu_id', $subIds)
                  ->orWhereIn('child_menu_id', $childIds);
            })
            //->orderByRaw("CASE WHEN stock_status = 'N' THEN 1 ELSE 0 END")
            ->orderBy('id', 'DESC')
            ->paginate(12);

        $banner = CategoryBanner::where('main_menu_id', $category->id)->first();

        // Build stocks map [barcode => qty] from Redis-backed KHUT catalog
        $catalog = app(KhutCatalogService::class)->all();
        $stocks = [];
        foreach ($products as $product) {
            $sku = trim((string)($product->product_barcode ?? ''));
            if ($sku === '') {
                continue;
            }
            $stocks[$sku] = (int)($catalog[$sku]['stock'] ?? 0);
        }

        return view('product-categories.index', compact(
            'mainMenus',
            'products',
            'category',
            'banner',
            'stocks'
        ));
    }*/
    
    
    
   public function listBySlug($slug)
{
    // Load all main menus with sub & child menus
    $mainMenus = MainMenu::with('subMenus.childMenus')->get();

    // ------------------------------
    // Special Case: NEW ARRIVALS
    // ------------------------------
    if ($slug === 'New-Arrivals') {

        $products = Product::where('new_arrivals', 'Y')
            ->where('published_site', 'Y')
            ->latest()
            ->paginate(12);

        $banner = CategoryBanner::latest()->first();

        // Build stocks map [barcode => qty] from Redis-backed KHUT catalog
        $catalog = app(KhutCatalogService::class)->all();
        $stocks = [];

        foreach ($products as $product) {
            $sku = trim((string)($product->product_barcode ?? ''));
            if ($sku === '') continue;

            $stocks[$sku] = (int)($catalog[$sku]['stock'] ?? 0);
        }

        return view('product-categories.index', compact(
            'mainMenus',
            'products',
            'banner',
            'stocks'
        ));
    }

    // ------------------------------
    // Normal Category Handling
    // ------------------------------
    $normalizedSlug = strtolower($slug);

    $category = $mainMenus->first(function ($menu) use ($normalizedSlug) {
        return strtolower(str_replace(' ', '-', $menu->name)) === $normalizedSlug;
    });

    if (!$category) {
        abort(404, 'Category not found');
    }

    $subIds   = $category->subMenus->pluck('id')->toArray();
    $childIds = ChildMenu::whereIn('sub_menu_id', $subIds)->pluck('id')->toArray();

    $products = Product::where('published_site', 'Y')
        ->where(function ($q) use ($category, $subIds, $childIds) {
            $q->where('main_menu_id', $category->id)
              ->orWhereIn('sub_menu_id', $subIds)
              ->orWhereIn('child_menu_id', $childIds);
        })
        ->orderBy('id', 'DESC')
        ->paginate(12);

    $banner = CategoryBanner::where('main_menu_id', $category->id)->first();

    // Build stocks map [barcode => qty] from Redis-backed KHUT catalog
    $catalog = app(KhutCatalogService::class)->all();
    $stocks = [];
    foreach ($products as $product) {
        $sku = trim((string)($product->product_barcode ?? ''));
        if ($sku === '') continue;

        $stocks[$sku] = (int)($catalog[$sku]['stock'] ?? 0);
    }

    return view('product-categories.index', compact(
        'mainMenus',
        'products',
        'category',
        'banner',
        'stocks'
    ));
} 
    
    
    
    
    
    
    
    
    
    
    
    

    /**
     * Sub / Child Category by ID
     */
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
                //->orderByRaw("CASE WHEN stock_status = 'N' THEN 1 ELSE 0 END")
                ->latest()
                ->paginate(50);

            $banner = CategoryBanner::where('main_menu_id', $category->main_menu_id)->first();

            $catalog = app(KhutCatalogService::class)->all();
            $stocks = [];
            foreach ($products as $product) {
                $sku = trim((string)($product->product_barcode ?? ''));
                if ($sku === '') {
                    continue;
                }
                $stocks[$sku] = (int)($catalog[$sku]['stock'] ?? 0);
            }

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
                //->orderByRaw("CASE WHEN stock_status = 'N' THEN 1 ELSE 0 END")
                 ->latest()
                ->paginate(50);

            $subMenu  = $childMenu->subMenu;
            $mainMenu = $subMenu ? $subMenu->mainMenu : null;


            $banner = $mainMenu
                ? CategoryBanner::where('main_menu_id', $mainMenu->id)->first()
                : (object)[
                    'banner_image' => asset('assets/images/product_banner.jpg'),
                    'title' => $childMenu->name
                ];

            $catalog = app(KhutCatalogService::class)->all();
            $stocks = [];
            foreach ($products as $product) {
                $sku = trim((string)($product->product_barcode ?? ''));
                if ($sku === '') {
                    continue;
                }
                $stocks[$sku] = (int)($catalog[$sku]['stock'] ?? 0);
            }

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

    /**
     * OLD LIST (Name Based)
     */
    public function list($name)
    {
        $mainMenu = MainMenu::whereRaw('LOWER(name) = ?', [strtolower($name)])->firstOrFail();

        $products = Product::where('published_site', 'Y')
            ->where('main_menu_id', $mainMenu->id)
            //->orderByRaw("CASE WHEN stock_status = 'N' THEN 1 ELSE 0 END")
            ->latest()
            ->paginate(50);

        return view('category.index', compact('products', 'mainMenu'));
    }
}
