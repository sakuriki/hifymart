<?php

namespace App\Http\Controllers;

use App\Models\AddressBook;
use Illuminate\Http\Request;

class AddressBookController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $addressBooks = AddressBook::where('user_id', auth()->user()->id)
      ->select(['id', 'name', 'address', 'phone', 'email', 'user_id', 'province_id', 'district_id'])
      ->with(['province', 'district'])
      ->get();
    return response()->json([
      'addresses' => $addressBooks
    ]);
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
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\AddressBook  $addressBook
   * @return \Illuminate\Http\Response
   */
  public function show(AddressBook $addressBook)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\AddressBook  $addressBook
   * @return \Illuminate\Http\Response
   */
  public function edit(AddressBook $addressBook)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\AddressBook  $addressBook
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, AddressBook $addressBook)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\AddressBook  $addressBook
   * @return \Illuminate\Http\Response
   */
  public function destroy(AddressBook $addressBook)
  {
    //
  }
}
