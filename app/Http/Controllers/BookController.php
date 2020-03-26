<?php

namespace App\Http\Controllers;

use DB;
use App\Book;
use App\Http\Requests\BookRequest;
use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $isbn = $request->input('isbn');
        $title = $request->input('title');
        $year = $request->input('year');
        $author = $request->input('author');
        $publisher = $request->input('publisher');

        $books = Book::with(['authors', 'publisher'])
            ->whereHas('authors', function($query) use($author) {
                return $query->where('name', 'like', "%$author%");
            })
            ->whereHas('publisher', function($query) use($publisher) {
                return $query->where('name', 'like', "%$publisher%");
            })
            ->when($isbn, function($query) use($isbn) {
                return $query->where('isbn', $isbn);
            })
            ->when($title, function($query) use($title) {
                return $query->where('title', 'like', "%$title%");
            })
            ->when($year, function($query) use($year) {
                return $query->where('year', $year);
            })
            ->paginate(10);

        return new BookCollection($books);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BookRequest $request)
    {
        try {
            $book = new Book;
            $book->fill($request->all());
            $book->publisher_id = $request->publisher_id;

            DB::transaction(function() use($book, $request) {
                $book->saveOrFail();
                $book->authors()->sync($request->authors);
            });

            return response()->json([
                'id' => $book->id,
                'created_at' => $book->created_at,
            ], 201);
        }
        catch(QueryException $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
        }
        catch(\Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $book = Book::with('authors')->with('publisher')->find($id);
            if(!$book) throw new ModelNotFoundException;

            return new BookResource($book);
        }
        catch(ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BookRequest $request, $id)
    {
        try {
            $book = Book::with('authors')->with('publisher')->find($id);
            if(!$book) throw new ModelNotFoundException;

            $book->fill($request->all());
            $book->publisher_id = $request->publisher_id;

            DB::transaction(function() use($book, $request) {
                $book->saveOrFail();
                $book->authors()->sync($request->authors);
            });

            return response()->json(null, 204);
        }
        catch(ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 404);
        }
        catch(QueryException $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
        }
        catch(\Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
