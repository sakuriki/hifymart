<?php

namespace App\Models;

use App\Concern\Helper;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  use Helper;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['name', 'description'];
  protected $hidden = ['pivot', 'created_at', 'updated_at'];

  public function user()
  {
    return $this->belongsToMany(Role::class);
  }

  public function permissions()
  {
    return $this->belongsToMany(Permission::class);
  }
}
