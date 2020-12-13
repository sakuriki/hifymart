<?php

namespace App\Concern;

use App\Models\Role;
use App\Models\Permission;

trait HasPermissionsTrait
{
  public function givePermissionsTo(...$permissions)
  {
    $permissions = $this->getAllPermissions($permissions);
    if ($permissions === null) {
      return $this;
    }
    $this->permissions()->saveMany($permissions);
    return $this;
  }

  public function withdrawPermissionsTo(...$permissions)
  {
    $permissions = $this->getAllPermissions($permissions);
    $this->permissions()->detach($permissions);
    return $this;
  }

  public function refreshPermissions(...$permissions)
  {
    $this->permissions()->detach();
    return $this->givePermissionsTo($permissions);
  }

  public function hasPermissionTo($permission)
  {
    return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
  }

  public function hasPermissionThroughRole($permission)
  {
    foreach ($permission->roles as $role) {
      if ($this->roles->contains($role)) {
        return true;
      }
    }
    return false;
  }

  public function hasRole(...$roles)
  {
    foreach ($roles as $role) {
      if ($this->roles->contains('slug', $role)) {
        return true;
      }
    }
    return false;
  }

  public function roles()
  {
    return $this->belongsToMany(Role::class);
  }

  public function rolesWithPer()
  {
    return $this->belongsToMany(Role::class)->with(["permissions" => function ($q) {
      $q->select(["slug"]);
    }]);
  }

  public function permissions()
  {
    return $this->belongsToMany(Permission::class);
  }

  protected function hasPermission($permission)
  {
    return (bool) $this->permissions->where('slug', $permission->slug)->count();
  }

  protected function getAllPermissions(array $permissions)
  {
    return Permission::whereIn('slug', $permissions)->get();
  }
}
