<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\MainMenu;        
use App\Models\SubMenu;
use App\Models\ChildMenu;
use App\Models\CategoryBanner;   
use App\Models\Status;
use App\Models\Iron;
use App\Models\DryWash;
use App\Http\Controllers\StockController;


class ProductController extends Controller{


   
    
    // public function list($name){
    //     $decodedName = str_replace('-', ' ', $name);
    //     $mainMenu = MainMenu::where('name', $decodedName)->firstOrFail();
    //     $products = Product::where('main_menu_id', $mainMenu->id)
    //         ->where('published_site', 'Y')
    //         ->orderBy('id', 'desc')
    //         ->paginate(500);
    //         $banner = CategoryBanner::where('main_menu_id', $mainMenu->id)->first();
    //         return view('product-categories.index', compact('products', 'mainMenu', 'banner'));
    //     }
    
         public function list($name){
            $decodedName = str_replace('-', ' ', $name);
            $mainMenu = MainMenu::where('name', $decodedName)->firstOrFail();
            $products = Product::where('main_menu_id', $mainMenu->id)
            ->where('published_site', 'Y')
            ->orderBy('id', 'desc')
            ->get();
            $banner = CategoryBanner::where('main_menu_id', $mainMenu->id)->first();
            return view('product-categories.index', compact('products', 'mainMenu', 'banner'));
        }

        public function categoryProducts($id){
            $products = Product::with(['mainMenu', 'subMenu', 'childMenu'])
            ->where('main_menu_id', $id)
            ->orWhere('sub_menu_id', $id)
            ->orWhere('child_menu_id', $id)
            ->orderBy('id', 'desc')
            ->paginate(50);

            return view('product-categories.index', compact('products'));
        }

    
   

    // Product Details দেখাবে
   public function details($slug)
{
    $product = Product::with([
        'thumbnails.color',
        'thumbnails.bodySize',
        'thumbnails.commonSize',
        'thumbnails.options.commonSize',
        'thumbnails.options.bodySize',
        'mainMenu',
        'subMenu',
        'childMenu',
        'statuses',
        'irons',
        'dryWashes',
    ])->where('slug', $slug)->firstOrFail();

    $thumbnails = $product->thumbnails;
    $colors = $thumbnails->pluck('color')->filter()->unique('id');
    $bodySizes = $thumbnails->pluck('bodySize')->filter()->unique('id');
    $commonSizes = $thumbnails->pluck('commonSize')->filter()->unique('id');

    // ✅ Related products properly filtered by category hierarchy
    if ($product->child_menu_id) {
        $relatedProducts = Product::where('child_menu_id', $product->child_menu_id)
            ->where('id', '!=', $product->id)
            ->where('site_view_status', 'Y')
            ->orderBy('id', 'desc')
            ->limit(12)
            ->get();
    } elseif ($product->sub_menu_id) {
        $relatedProducts = Product::where('sub_menu_id', $product->sub_menu_id)
            ->where('id', '!=', $product->id)
            ->where('site_view_status', 'Y')
            ->orderBy('id', 'desc')
            ->limit(12)
            ->get();
    } else {
        $relatedProducts = Product::where('main_menu_id', $product->main_menu_id)
            ->where('id', '!=', $product->id)
            ->where('site_view_status', 'Y')
            ->orderBy('id', 'desc')
            ->limit(12)
            ->get();
    }

    return view('product-details.index', compact(
        'product',
        'thumbnails',
        'colors',
        'bodySizes',
        'commonSizes',
        'relatedProducts'
    ));
    
    

}




        
      

public function allProductsList($type) 
    {
        $type = strtolower($type);
        
        // Image base path define kora holo
        $baseImagePath = rtrim(env('ADMIN_BASE_URL'), '/') . '/storage/';

        $typeMap = [
            'new-arrivals' => 'new_arrivals',
            'patchwork' => 'patchwork',
            'festive-collection' => 'festive_collection',
        ];

        /* ======================================================
           ✅ Random Banner Logic
        ====================================================== */
        $bannerRecord = CategoryBanner::inRandomOrder()->first();

        if ($bannerRecord && !empty($bannerRecord->banner_image)) {
            $cleanPath = preg_replace('#^/?storage/#', '', $bannerRecord->banner_image);
            $bannerPath = '/storage/' . $cleanPath;
            $bannerTitle = $bannerRecord->title ?? 'Shop';
        } else {
            $bannerPath = '/storage/category_banners/product_banner.jpg';
            $bannerTitle = 'Shop';
        }

        $category = (object)[
            'name'   => ucwords(str_replace('-', ' ', $type)),
            'banner' => $bannerPath,
            'title'  => $bannerTitle
        ];

        /* ===============================
           ✅ Data Fetching
        =============================== */
        if (array_key_exists($type, $typeMap)) {
            $column = $typeMap[$type];

            $products = Product::where($column, 'Y')
                ->where('published_site', 'Y')
                ->where('site_view_status', 'Y')
                ->latest()
                ->paginate(50);

            $displayType = $category->name;
            $urlType = $type;
            $showBanner = true;

            return view('all-products.all-products-list', compact(
                'products', 'displayType', 'urlType', 'showBanner', 'category', 'baseImagePath'
            ));
        }

        // Category Based Filter
        $decodedType = str_replace('-', ' ', $type);
        $mainMenu = MainMenu::whereRaw('LOWER(name) = ?', [$decodedType])->first();

        if ($mainMenu) {
            $subIds = $mainMenu->subMenus->pluck('id')->toArray();
            $childIds = ChildMenu::whereIn('sub_menu_id', $subIds)->pluck('id')->toArray();

            $products = Product::where('published_site', 'Y')
                ->where('site_view_status', 'Y')
                ->where(function($q) use ($mainMenu, $subIds, $childIds) {
                    $q->where('main_menu_id', $mainMenu->id)
                    ->orWhereIn('sub_menu_id', $subIds)
                    ->orWhereIn('child_menu_id', $childIds);
                })->latest()->paginate(50);

            $displayType = $mainMenu->name;
            $urlType = $type;
            $showBanner = true;

            return view('all-products.all-products-list', compact(
                'products', 'displayType', 'urlType', 'showBanner', 'category', 'baseImagePath'
            ));
        }

        return redirect('/');
    }
        


}
