<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $taxes = Tax::select(['id', 'name'])->get();
    return response()->json([
      'taxes' => $taxes
    ]);
  }
}
