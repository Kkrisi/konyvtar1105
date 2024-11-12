<?php

namespace App\Http\Controllers;

use App\Models\Lending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\map;

class LendingController extends Controller
{
    public function index()
    {
        return Lending::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $record = new Lending();
        $record->fill($request->all());
        $record->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $user_id, $copy_id, $start)
    {
        $lending = Lending::where('user_id', $user_id)
        ->where('copy_id', $copy_id)
        ->where('start', $start)
        //listát ad vissza:
        ->get();
        return $lending[0];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $user_id, $copy_id, $start)
    {
        $record = $this->show($user_id, $copy_id, $start);
        $record->fill($request->all());
        $record->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id, $copy_id, $start)
    {
        $this->show($user_id, $copy_id, $start)->delete();
    }

    public function lendingsWithCopies(){
        $user = Auth::user();
        return Lending::with('copies')
        -> where('user_id', '=', $user->id)
        -> get();
    }


    public function lendingCount(){
        $user = Auth::user();
        $lendings = DB::table('lendings as 1')
        ->where('user_id', $user->id)
        ->count();
        return $lendings;
    }


    public function activeLendingCount(){
        $user = Auth::user();
        $lendings = DB::table('lendings as 1')
        ->where('user_id', $user->id)
        ->whereNull('end')
        ->count();
        return $lendings;
    }


    public function lendingBooksCount(){
        $user = Auth::user();
        $lendings = DB::table('lendings as 1')
        ->join('copies as c', 'l.copy_id', 'c.copy_id')
        ->where('user_id', $user->id)
        ->distinct('book_id')
        ->whereNull('end')
        ->count();
        return $lendings;
    }

    public function lendingsBooksData(){
        $user = Auth::user();
        $books = DB::table('lendings as l')
        ->join('copies as c', 'l.copy_id', 'c.copy_id')
        ->join('books as b', 'c.book_id', 'b.book_id')
        ->select('book_id', 'author', 'title')
        ->where('user_id', $user->id)
        ->distinct('book_id')
        ->count();
        return $books;
    }


    // 3.C
    public function lendingsBooksMax1() {
        $user = Auth::user();
        $books = DB::table('lendings as l')
            ->join('copies as c', 'l.copy_id', 'c.copy_id')
            ->join('books as b', 'c.book_id', 'b.book_id')
            ->select('b.book_id', 'b.author', 'b.title')
            ->where('l.user_id', $user->id)
            ->groupBy('b.book_id', 'b.author', 'b.title')
            ->having('COUNT(b.book_id)',  '<', 2)
            ->get();

        return $books;
    }


    public function lendingsHardcoverBooksMax1(){
        $books = DB::table('books as b')    
            ->join('copies as c', 'b.book_id', 'c.book_id')
            ->select('b.book_id', 'b.author', 'b.title')
            ->where('c.hardcovered', $value=0)
            ->distinct('book_id')
            ->get();

    return $books;
}
}
