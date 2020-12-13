<?php

namespace App\Models;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
  protected $guarded = [];
  protected $hidden = ['pivot'];

  public function roles()
  {
    return $this->belongsToMany(Role::class);
  }
}
