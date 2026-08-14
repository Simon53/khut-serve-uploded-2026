<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use App\Models\VisitorTable;
use App\Models\Slider;
use App\Models\SitePage;
use App\Models\SiteMenu;
use App\Models\Product;
use App\Models\MainMenu;
use App\Models\ChildMenu;
use App\Models\CategoryBanner;
use App\Models\KhutStory;


class HomeController extends Controller
{
    /**
     * ============================================================
     * HOME PAGE
     * ============================================================
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Visitor tracking
        |--------------------------------------------------------------------------
        | Homepage response-এর critical path থেকে visitor insert সরিয়ে
        | queue-তে পাঠানো ভালো।
        |
        | Queue setup না থাকলে আপাতত নিচের code ব্যবহার করছি।
        |--------------------------------------------------------------------------
        */

        try {
            dispatch(function () use ($request) {
                VisitorTable::create([
                    'ip_address' => $request->ip(),
                    'visit_time' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            // Visitor tracking fail হলেও homepage যেন বন্ধ না হয়
        }

        return view('home.home');
    }


    /**
     * ============================================================
     * HOME INDEX DATA
     * ============================================================
     */
    public function homeIndex()
    {
        /*
        |--------------------------------------------------------------------------
        | Admin Base URL
        |--------------------------------------------------------------------------
        */
        $adminBaseUrl = rtrim(env('ADMIN_BASE_URL'), '/');


        /*
        |--------------------------------------------------------------------------
        | SLIDERS
        |--------------------------------------------------------------------------
        */
        $sliders = Cache::remember(
            'home_sliders',
            now()->addHour(),
            function () {
                return Slider::where('is_active', 'Yes')->get();
            }
        );

        $sliders->transform(function ($item) use ($adminBaseUrl) {
            $item->full_image =
                $adminBaseUrl . '/storage/' . ltrim($item->slider_location, '/');

            return $item;
        });


        /*
        |--------------------------------------------------------------------------
        | MAIN MENUS
        |--------------------------------------------------------------------------
        */
        $mainMenus = Cache::remember(
            'home_main_menus',
            now()->addHour(),
            function () {
                return MainMenu::with('subMenus')->get();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | COTTON / ENDI-SILK / HALF-SILK MENU
        |--------------------------------------------------------------------------
        */
        $cottonMenu = Cache::remember(
            'child_menu_cotton',
            now()->addHour(),
            function () {
                return ChildMenu::where('name', 'Cotton')->first();
            }
        );

        $endiSilkMenu = Cache::remember(
            'child_menu_endi_silk',
            now()->addHour(),
            function () {
                return ChildMenu::where('name', 'Endi-Silk')->first();
            }
        );

        $halfSilkMenu = Cache::remember(
            'child_menu_half_silk',
            now()->addHour(),
            function () {
                return ChildMenu::where('name', 'Half-Silk')->first();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | FESTIVE PRODUCTS
        |--------------------------------------------------------------------------
        */
        $festiveProducts = Cache::remember(
            'home_festive_products',
            now()->addMinutes(10),
            function () {
                return Product::whereIn(
                        'bottom_fastive',
                        ['festive-left', 'festive-right']
                    )
                    ->where('published_site', 'Y')
                    ->where('site_view_status', 'Y')
                    ->latest()
                    ->take(2)
                    ->get();
            }
        );

        $festiveLeft = $festiveProducts->firstWhere(
            'bottom_fastive',
            'festive-left'
        );

        $festiveRight = $festiveProducts->firstWhere(
            'bottom_fastive',
            'festive-right'
        );


        /*
        |--------------------------------------------------------------------------
        | PAGES
        |--------------------------------------------------------------------------
        */
        $pages = Cache::remember(
            'home_site_pages',
            now()->addHour(),
            function () {

                return SitePage::all()->map(function ($page) {

                    $cleanText = strip_tags($page->details);

                    $page->first_paragraph = Str::limit(
                        $cleanText,
                        600
                    );

                    preg_match(
                        '/<img.*?src=["\'](.*?)["\'].*?>/i',
                        $page->details,
                        $img_matches
                    );

                    $page->first_image = $img_matches[1] ?? '';

                    /*
                    |--------------------------------------------------------------------------
                    | menu relation
                    |--------------------------------------------------------------------------
                    */
                    $page->menu_slug =
                        optional($page->menu)->slug ?? '';

                    return $page;
                });
            }
        );


        /*
        |--------------------------------------------------------------------------
        | NEW ARRIVAL PRODUCTS
        |--------------------------------------------------------------------------
        */
        $products = Cache::remember(
            'home_new_arrival_products',
            now()->addMinutes(10),
            function () {

                return Product::where('new_arrivals', 'Y')
                    ->where('published_site', 'Y')
                    ->where('site_view_status', 'Y')
                    ->latest()
                    ->take(12)
                    ->get();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | FEATURE ONE
        |--------------------------------------------------------------------------
        */
        $featureProduct = Cache::remember(
            'home_feature_one',
            now()->addMinutes(10),
            function () {

                return Product::with([
                    'childMenu',
                    'subMenu',
                    'mainMenu'
                ])
                ->where('feature', 'Feature One')
                ->where('published_site', 'Y')
                ->where('site_view_status', 'Y')
                ->latest()
                ->first();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | FEATURE TWO
        |--------------------------------------------------------------------------
        */
        $featureTwoProduct = Cache::remember(
            'home_feature_two',
            now()->addMinutes(10),
            function () {

                return Product::with([
                    'childMenu',
                    'subMenu',
                    'mainMenu'
                ])
                ->where('feature', 'Feature Two')
                ->where('published_site', 'Y')
                ->where('site_view_status', 'Y')
                ->latest()
                ->first();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | HIGHLIGHT PRODUCTS
        |--------------------------------------------------------------------------
        */
        $highlightProducts = Cache::remember(
            'home_highlight_products',
            now()->addMinutes(10),
            function () {

                return Product::where('site_view_status', 'Y')
                    ->where('published_site', 'Y')
                    ->whereIn(
                        'highlight',
                        [
                            'highlight-one',
                            'highlight-two',
                            'highlight-three',
                            'highlight-four'
                        ]
                    )
                    ->get()
                    ->keyBy('highlight');
            }
        );


        /*
        |--------------------------------------------------------------------------
        | ART GALLERY
        |--------------------------------------------------------------------------
        */
        $ArtGellery = Cache::remember(
            'home_art_gallery',
            now()->addMinutes(10),
            function () {

                return Product::where(
                        'bottom_fastive',
                        'art-gallery'
                    )
                    ->where('published_site', 'Y')
                    ->where('site_view_status', 'Y')
                    ->latest()
                    ->take(12)
                    ->get();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | STORIES
        |--------------------------------------------------------------------------
        */
        $stories = Cache::remember(
            'home_stories',
            now()->addHour(),
            function () {

                return KhutStory::where('is_active', 'Y')
                    ->latest()
                    ->take(4)
                    ->get();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | COTTON PRODUCTS
        |--------------------------------------------------------------------------
        */
        $cottonProducts = $cottonMenu
            ? Cache::remember(
                'home_cotton_products_' . $cottonMenu->id,
                now()->addMinutes(10),
                function () use ($cottonMenu) {

                    return Product::where(
                            'child_menu_id',
                            $cottonMenu->id
                        )
                        ->where('published_site', 'Y')
                        ->where('site_view_status', 'Y')
                        ->latest('id')
                        ->take(12)
                        ->get();
                }
            )
            : collect();


        /*
        |--------------------------------------------------------------------------
        | ENDI SILK PRODUCTS
        |--------------------------------------------------------------------------
        */
        $endiSilkProducts = $endiSilkMenu
            ? Cache::remember(
                'home_endi_silk_products_' . $endiSilkMenu->id,
                now()->addMinutes(10),
                function () use ($endiSilkMenu) {

                    return Product::where(
                            'child_menu_id',
                            $endiSilkMenu->id
                        )
                        ->where('published_site', 'Y')
                        ->where('site_view_status', 'Y')
                        ->latest('id')
                        ->take(12)
                        ->get();
                }
            )
            : collect();


        /*
        |--------------------------------------------------------------------------
        | HALF SILK PRODUCTS
        |--------------------------------------------------------------------------
        */
        $halfSilkProducts = $halfSilkMenu
            ? Cache::remember(
                'home_half_silk_products_' . $halfSilkMenu->id,
                now()->addMinutes(10),
                function () use ($halfSilkMenu) {

                    return Product::where(
                            'child_menu_id',
                            $halfSilkMenu->id
                        )
                        ->where('published_site', 'Y')
                        ->where('site_view_status', 'Y')
                        ->latest('id')
                        ->take(12)
                        ->get();
                }
            )
            : collect();


        /*
        |--------------------------------------------------------------------------
        | PATCHWORK PRODUCTS
        |--------------------------------------------------------------------------
        */
        $patchworkProducts = Cache::remember(
            'home_patchwork_products',
            now()->addMinutes(10),
            function () {

                return Product::where('patchwork', 'Y')
                    ->where('published_site', 'Y')
                    ->where('site_view_status', 'Y')
                    ->latest()
                    ->take(10)
                    ->get();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | HO-JO-BO-RO-LO
        |--------------------------------------------------------------------------
        */
        $mainMenu = Cache::remember(
            'main_menu_hojoborolo',
            now()->addHour(),
            function () {

                return MainMenu::whereRaw(
                    'LOWER(name) = ?',
                    ['hojoborolo']
                )->first();
            }
        );

        $HozoboroloProducts = $mainMenu
            ? Cache::remember(
                'home_hojoborolo_products_' . $mainMenu->id,
                now()->addMinutes(10),
                function () use ($mainMenu) {

                    return Product::where(
                            'main_menu_id',
                            $mainMenu->id
                        )
                        ->where('published_site', 'Y')
                        ->where('site_view_status', 'Y')
                        ->latest()
                        ->take(4)
                        ->get();
                }
            )
            : collect();


        /*
        |--------------------------------------------------------------------------
        | BOTTOM CONTENT
        |--------------------------------------------------------------------------
        */
        $bottomContent = Cache::remember(
            'home_bottom_content',
            now()->addMinutes(10),
            function () {

                return Product::where(
                        'bottom_fastive',
                        'bottom-image'
                    )
                    ->where('published_site', 'Y')
                    ->where('site_view_status', 'Y')
                    ->select([
                        'details',
                        'main_image'
                    ])
                    ->first();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | FOOTER MENUS
        |--------------------------------------------------------------------------
        */
        $footerMenus = Cache::remember(
            'home_footer_menus',
            now()->addHour(),
            function () {

                return SiteMenu::where('status', 1)->get();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | CATEGORY BANNERS
        |--------------------------------------------------------------------------
        */
        $menuBanners = Cache::remember(
            'home_category_banners',
            now()->addHour(),
            function () {

                return CategoryBanner::with('mainMenu')->get();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | RETURN HOME VIEW
        |--------------------------------------------------------------------------
        */
        return view(
            'home.home',
            compact(
                'sliders',
                'products',
                'mainMenus',
                'featureProduct',
                'featureTwoProduct',
                'highlightProducts',
                'menuBanners',
                'pages',
                'stories',
                'footerMenus',
                'cottonProducts',
                'endiSilkProducts',
                'halfSilkProducts',
                'festiveLeft',
                'festiveRight',
                'patchworkProducts',
                'bottomContent',
                'HozoboroloProducts',
                'adminBaseUrl',
                'ArtGellery'
            )
        );
    }


    /**
     * ============================================================
     * PATCHWORK PRODUCTS
     * ============================================================
     */
    public function patchworkProducts()
    {
        $products = Product::where('patchwork', 'Y')
            ->where('published_site', 'Y')
            ->where('site_view_status', 'Y')
            ->reorder()
            ->orderByDesc('id')
            ->paginate(50);

        $mainMenus = MainMenu::with(
            'subMenus.childMenus'
        )->get();

        $category = (object) [
            'name' => 'Patchwork',
            'title' => 'Patchwork'
        ];

        $banner = CategoryBanner::first();

        if ($banner && !empty($banner->banner_image)) {

            $bannerImage = $banner->banner_image;

            $bannerImage = preg_replace(
                '#^https?://[^/]+/#',
                '',
                $bannerImage
            );

            $bannerImage = preg_replace(
                '#^/?storage/#',
                '',
                $bannerImage
            );

            $category->banner =
                '/storage/' . ltrim($bannerImage, '/');

        } else {

            $category->banner =
                '/storage/category_banners/product_banner.jpg';
        }

        $stocks = app(
            \App\Http\Controllers\StockController::class
        )->getStocksForSkus(
            $products
                ->pluck('product_barcode')
                ->filter()
                ->values()
                ->all()
        );

        return view(
            'product-categories.patchwork',
            compact(
                'products',
                'mainMenus',
                'category',
                'stocks'
            )
        );
    }


    /**
     * ============================================================
     * NEW ARRIVALS PRODUCTS
     * ============================================================
     */
    public function newArrivalsProducts()
    {
        $products = Product::where('new_arrivals', 'Y')
            ->where('published_site', 'Y')
            ->where('site_view_status', 'Y')
            ->reorder()
            ->orderByDesc('id')
            ->paginate(50);

        $mainMenus = MainMenu::with(
            'subMenus.childMenus'
        )->get();

        $category = (object) [
            'name' => 'New Arrivals',
            'title' => 'New Arrivals'
        ];

        $banner = CategoryBanner::first();

        if ($banner && !empty($banner->banner_image)) {

            $bannerImage = $banner->banner_image;

            $bannerImage = preg_replace(
                '#^https?://[^/]+/#',
                '',
                $bannerImage
            );

            $bannerImage = preg_replace(
                '#^/?storage/#',
                '',
                $bannerImage
            );

            $category->banner =
                '/storage/' . ltrim($bannerImage, '/');

        } else {

            $category->banner =
                '/storage/category_banners/product_banner.jpg';
        }

        $stocks = app(
            \App\Http\Controllers\StockController::class
        )->getStocksForSkus(
            $products
                ->pluck('product_barcode')
                ->filter()
                ->values()
                ->all()
        );

        return view(
            'product-categories.new-arrivals',
            compact(
                'products',
                'mainMenus',
                'category',
                'stocks'
            )
        )->with(
            'activeMenu',
            'New Arrivals'
        );
    }
}