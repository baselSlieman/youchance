<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // dd(
        //     Product::addSelect(['category' => Category::select('name')
        //     ->whereColumn('category_id', 'categories.id')
        //     ->orderByDesc('created_at')
        //     ->limit(1)
        //     ])->get()
        // );
        $categories = Category::query()->orderBy('created_at','desc')->get();
        return view('home.index',compact('categories'));
    }

    public function testRoute(Category $category)
    {
        dd(Route::current());
        dd(Route::currentRouteName());
        dd(Route::currentRouteAction());
    }
    public function test()
    {

        return view('welcome');
        //subqueries/////////////////
        //subqueries 1: allows you to pull information from related tables in a single query
        // dd(
        //     Product::addSelect(['category' => Category::select('name')
        //     ->whereColumn('category_id', 'categories.id')
        //     ->orderByDesc('created_at')
        //     ->limit(1)
        //     ])->get()
        // );
        //subqueries 2:  sort all Products based on when the last Category created
        // dd(
        //     Product::orderByDesc(
        //         Category::select('created_at')
        //             ->whereColumn('category_id', 'categories.id')
        //             ->orderByDesc('created_at')
        //             ->limit(1)
        //     )->get()
        // );

        //Retrieving Single Models & Aggregates////////////
        //dd(Category::find(1));
        //dd(Category::where('name', 'PUBG')->first());
        //dd(Category::firstWhere('name', 'PUBG'));
        //dd(Category::findOr(5, function () {return "not found";}));
        //dd(Category::where('id',5)->firstOr(function(){return "not found";}));
        //dd(Category::findOrFail(5)); // If the ModelNotFoundException is not caught, a 404 HTTP response is automatically sent back to the client:

        //firstOrCreate
        // Retrieve Category by name or create it if it doesn't exist...
        // $category = Category::firstOrCreate([
        //     'name' => 'London to Paris',
        //     'description' => 'London to Paris',
        //     'image' => 'London to Paris'
        // ]);

        //firstOrNew
        // Retrieve Category by name or instantiate a new Category instance.
        // $category = Category::firstOrNew([
        //     'name' => 'London to Paris',
        //     'description' => 'London to Paris',
        //     'image' => 'London to Paris'
        // ]);
        // $category->save();


        //Aggregates/////////////
        // $count = Product::where('category_id', 1)->count();
        // $max = Product::where('category_id', 1)->max('price');
        // $avg = Product::where('category_id', 1)->avg('price');
        // dd($max,$count,$avg);

        // inserting ///////////
        //method 1
        // $category = new Category;
        // $category->name = "categname";
        // $category->save();
        //method 2
        // $category = Category::create([
        //     'name' => 'London to Paris',
        // ]);

        //updateing/////////////
        //method 1
        // $category = Category::find(1);
        // $category->name = 'Paris to London';
        // $category->save();
        // method 2:  updateOrCreate
        // $category = Category::updateOrCreate(
        //     ['name' => 'xxxplax', 'description' => 'xxx'], //condition if false will create and save the new record
        //     ['image' => 'asdsadx']                      // updated value  and auto save
        // );
        //method 3: تعديل جماعي للبيانات
        // Category::where('image', 'asds')
        // ->where('description', 'San Diego')
        // ->update(['image' => 'asdsad']);

        //Examining Attribute Changes   تفحص تغييرات عناصر النموئج
        //  isDirty & isClean لتفحص التغيرات على المودل في الكود
        // wasChanged لفتحص التغيرات على المودل في قاعدة البيانات
        // $category = Category::create([
        //     'name' => 'Taylor',
        //     'description' => 'Otwell',
        //     'image' => 'Developer',
        // ]);
        // $category->name = 'Painter';
        // iSDirty() - isClean() -----------------------
        // $category->isDirty(); // true
        // $category->isDirty('name'); // true
        // $category->isDirty('description'); // false
        // dd($category->isDirty(['name', 'description'])); // true
        // $category->isClean(); // false
        // $category->isClean('name'); // false
        // $category->isClean('description'); // true
        // $category->isClean(['name', 'description']); // false
        // $category->save();
        // $category->isDirty(); // false
        // $category->isClean(); // true
        // wasChanged()--------------------------------
        // $category = Category::create([   //auto save
        //     'name' => 'Taylor',
        //     'description' => 'Otwell',
        //     'image' => 'Developer',
        // ]);
        // $category->name = 'Painter';
        // $category->save();
        // dd($category->wasChanged()); // true
        // $category->wasChanged('name'); // true
        // $category->wasChanged(['description', 'name']); // true
        // $category->wasChanged('description'); // false
        // $category->wasChanged(['name', 'description']); // true
        // getOriginal()--------------------------------
        // $category = Category::find(1);
        // $category->name; // John
        // $category->description; // john@example.com
        // $category->name = 'Jack';
        // $category->description; // Jack
        // $category->getOriginal('name'); // John
        // dd($category->getOriginal()); // Array of original attributes...
        // getChanges() ---------------------------------
        // $category->update([
        //     'name' => 'Jack',
        //     'description' => 'jack@example.com',
        // ]);
        // $category->getChanges();


        //Deleting
        // $category = Category::find(1);
        // $category->delete();
        //or delete the model without explicitly retrieving it
        // Category::destroy(1);
        // Category::destroy([1,2]);
        // Category::destroy(collect(1,2));
        //or If you are utilizing soft deleting models, you may permanently delete
        // Category::forceDestroy(1);
        //or Deleting Models Using Queries
        // $deleted = Category::where('active', 0)->delete();
        //or To delete all models in a table
        // $deleted = Category::query()->delete();

        //Soft Delete -> To determine if a given model instance has been soft deleted
        // if ($category->trashed()) {}
        //Restoring Soft Deleted Models
        // $category->restore();
        //restore multiple models
        // Category::withTrashed()->where('name', 'xxx')->restore();
        //when building relationship queries
        // $product->category()->restore();
        //Querying Soft Deleted Models
        // Category::withTrashed()->where('name', 'xxx')->get();
        // Category::onlyTrashed()->where('name', 'xxx')->get();
        // $product->category()->withTrashed()->get();



    }


}
