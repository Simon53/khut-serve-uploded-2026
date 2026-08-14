<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SEARCH API
    |--------------------------------------------------------------------------
    */

    public function search(Request $request)
    {
        $q = trim($request->q);

        if ($q === '') {
            return response()->json([]);
        }

        /*
        |--------------------------------------------------------------------------
        | Prefix Search
        |--------------------------------------------------------------------------
        |
        | Ch  -> Chandra, Chondrolota, Chondirika...
        | C   -> C দিয়ে শুরু সব product
        | A   -> A দিয়ে শুরু সব product
        | Ba  -> Banu, Batashi...
        |
        */

        $products = Product::where('published_site', 'Y')
            ->where(function ($query) use ($q) {

                $query->where('name_en', 'LIKE', $q . '%')
                      ->orWhere('name_bn', 'LIKE', $q . '%');

            })
            ->select(
                'id',
                'name_en',
                'name_bn',
                'slug'
            )
            ->orderBy('id', 'desc')
            ->get();

        if ($products->isEmpty()) {
            return response()->json([]);
        }

        /*
        |--------------------------------------------------------------------------
        | Search Result Page URL
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'search_url' => url('/search-results') . '?search=' . urlencode($q)
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH RESULTS PAGE
    |--------------------------------------------------------------------------
    */

    public function results(Request $request)
    {
        $search = trim($request->search);

        if ($search === '') {
            return redirect('/');
        }

        /*
        |--------------------------------------------------------------------------
        | ALL MATCHING PRODUCTS
        |--------------------------------------------------------------------------
        */

        $products = Product::with([
            'thumbnails',
            'options'
        ])
        ->where('published_site', 'Y')
        ->where(function ($query) use ($search) {

            $query->where('name_en', 'LIKE', $search . '%')
                  ->orWhere('name_bn', 'LIKE', $search . '%');

        })
        ->orderByDesc('id')
        ->get();

        /*
        |--------------------------------------------------------------------------
        | No Result
        |--------------------------------------------------------------------------
        */

        if ($products->isEmpty()) {
            return redirect('/')
                ->with('error', 'Product not found');
        }

        /*
        |--------------------------------------------------------------------------
        | Main Menus
        |--------------------------------------------------------------------------
        */

        $mainMenus = \App\Models\MainMenu::with(
            'subMenus.childMenus'
        )->get();

        /*
        |--------------------------------------------------------------------------
        | Fake Category Object
        |--------------------------------------------------------------------------
        |
        | Existing product listing view যেন category page-এর মতো
        | কাজ করতে পারে।
        |
        */

        $category = (object) [
            'name' => 'Search Results',
            'title' => 'Search Results',
            'banner' => asset(
                'assets/images/product_banner.jpg'
            )
        ];

        return view(
            'product-categories.index',
            compact(
                'products',
                'mainMenus',
                'category'
            )
        );
    }
}