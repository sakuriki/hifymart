<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Http\Requests\SubscriberRequest;

class SubscriberController extends Controller
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
  public function store(SubscriberRequest $request)
  {
    try {
      Subscriber::create($request->validated());
      return response()->noContent();
    } catch (\Exception $exception) {
      return response()->json([
        'errors' => [
          'error' => [
            $exception->getMessage()
          ]
        ]
      ], 422);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Subscriber  $subscriber
   * @return \Illuminate\Http\Response
   */
  public function show(Subscriber $subscriber)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Subscriber  $subscriber
   * @return \Illuminate\Http\Response
   */
  public function edit(Subscriber $subscriber)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Subscriber  $subscriber
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Subscriber $subscriber)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Subscriber  $subscriber
   * @return \Illuminate\Http\Response
   */
  public function destroy(Subscriber $subscriber)
  {
    try {
      $subscriber->delete();
      return response()->noContent();
    } catch (\Exception $exception) {
      return response()->json([
        'errors' => [
          'error' => [
            $exception->getMessage()
          ]
        ]
      ], 422);
    }
  }
}
