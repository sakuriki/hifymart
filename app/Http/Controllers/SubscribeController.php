<?php

namespace App\Http\Controllers;

use App\Models\Subscribe;
use Illuminate\Http\Request;
use App\Http\Requests\SubscribeRequest;

class SubscribeController extends Controller
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
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(SubscribeRequest $request)
  {
    try {
      Subscribe::create($request->validated());
      return response()->noContent();
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Subscribe  $subscribe
   * @return \Illuminate\Http\Response
   */
  public function show(Subscribe $subscribe)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Subscribe  $subscribe
   * @return \Illuminate\Http\Response
   */
  public function edit(Subscribe $subscribe)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Subscribe  $subscribe
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Subscribe $subscribe)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Subscribe  $subscribe
   * @return \Illuminate\Http\Response
   */
  public function destroy(Subscribe $subscribe)
  {
    try {
      $subscribe->delete();
      return response()->noContent();
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }
}
