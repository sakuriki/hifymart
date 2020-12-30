<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $provinces = Province::select(['id', 'name', 'gso_id'])
      ->with('districts:id,name,province_id')
      ->get();
    return response()->json([
      'provinces' => $provinces
    ]);
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
   * @param  \App\Province  $province
   * @return \Illuminate\Http\Response
   */
  public function show(Province $province)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Province  $province
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Province $province)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Province  $province
   * @return \Illuminate\Http\Response
   */
  public function destroy(Province $province)
  {
    //
  }
}
