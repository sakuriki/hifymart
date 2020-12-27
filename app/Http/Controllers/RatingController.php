<?php

namespace App\Http\Controllers;

use App\Http\Resources\RatingCollection;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Product::id  $id
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function show($id, Request $request)
  {
    $ratings = Rating::where('product_id', $id)
      ->where('approved', 1)
      ->with('user:id,name')
      ->select(['id', 'rating', 'review', 'user_id', 'product_id'])
      ->paginate($request->input('per_page', 5));
    return response()->json(new RatingCollection($ratings));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Rating  $rating
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Rating $rating)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Rating  $rating
   * @return \Illuminate\Http\Response
   */
  public function destroy(Rating $rating)
  {
    //
  }
}
