<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriberRequest;
use App\Http\Resources\SubscriberCollection;

class SubscriberController extends Controller
{
  /**
   * Return the subscribers.
   */
  public function index(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('subscriber.access')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $subscribers = Subscriber::where('email', 'LIKE', "%{$request->input('q')}%");
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $subscribers = $subscribers->orderBy("id", $sortDesc);
        break;
      case "email":
        $subscribers = $subscribers->orderBy("email", $sortDesc);
        break;
      case "created_at":
        $subscribers = $subscribers->orderBy("created_at", $sortDesc);
        break;
      default:
        $subscribers = $subscribers->latest();
    }
    return response()->json(new SubscriberCollection($subscribers->paginate($request->input("per_page", 12))));
  }
  /**
   * Update the specified resource in storage.
   */
  public function update(SubscriberRequest $request, Subscriber $subscriber)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('subscriber.update')) {
      return response()->json([
        'code' => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $subscriber->update($request->validated());
    return $subscriber;
  }
  /**
   * Store a newly created resource in storage.
   */
  public function store(SubscriberRequest $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('subscriber.create')) {
      return response()->json([
        'code' => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $subscriber = Subscriber::create($request->validated());
    return $subscriber;
  }
  /**
   * Return the specified resource.
   */
  public function show(Subscriber $subscriber)
  {

  }
  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Subscriber $subscriber): Response
  {
    $user = auth()->user();
    if (!$user || $user->cannot('subscriber.delete')) {
      return response()->json([
        'code' => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $subscriber->delete();
    return response()->noContent();
  }

  public function bulkDestroy(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('subscriber.delete')) {
      return response()->json([
        'code' => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    Subscriber::whereIn('id', $request->listIds)->delete();
    return response()->noContent();
  }
}
