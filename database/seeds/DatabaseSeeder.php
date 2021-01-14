<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    // Permissions
    $permissions = array(
      // quyền truy cập dashboard
      array("name" => "dashboard", "slug" => "dashboard", "group" => "dashboard", "description" => "Truy cập dashboard"),
      // quyền sản phẩm/product
      array("name" => "product.access", "slug" => "product.access", "group" => "product", "description" => "Truy cập danh sách sản phẩm"),
      array("name" => "product.create", "slug" => "product.create", "group" => "product", "description" => "Thêm sản phẩm"),
      array("name" => "product.update", "slug" => "product.update", "group" => "product", "description" => "Cập nhật sản phẩm"),
      array("name" => "product.view", "slug" => "product.view", "group" => "product", "description" => "Xem chi tiết sản phẩm"),
      array("name" => "product.delete", "slug" => "product.delete", "group" => "product", "description" => "Xoá sản phẩm"),
      // quyền danh mục/category
      array("name" => "category.access", "slug" => "category.access", "group" => "category", "description" => "Truy cập danh sách danh mục"),
      array("name" => "category.create", "slug" => "category.create", "group" => "category", "description" => "Thêm danh mục"),
      array("name" => "category.update", "slug" => "category.update", "group" => "category", "description" => "Cập nhật danh mục"),
      array("name" => "category.view", "slug" => "category.view", "group" => "category", "description" => "Xem chi tiết danh mục"),
      array("name" => "category.delete", "slug" => "category.delete", "group" => "category", "description" => "Xoá danh mục"),
      // quyền nhãn hiệu/brand
      array("name" => "brand.access", "slug" => "brand.access", "group" => "brand", "description" => "Truy cập danh sách nhãn hiệu"),
      array("name" => "brand.create", "slug" => "brand.create", "group" => "brand", "description" => "Thêm nhãn hiệu"),
      array("name" => "brand.update", "slug" => "brand.update", "group" => "brand", "description" => "Cập nhật nhãn hiệu"),
      array("name" => "brand.view", "slug" => "brand.view", "group" => "brand", "description" => "Xem chi tiết nhãn hiệu"),
      array("name" => "brand.delete", "slug" => "brand.delete", "group" => "brand", "description" => "Xoá nhãn hiệu"),
      // quyền thẻ tag
      array("name" => "tag.access", "slug" => "tag.access", "group" => "tag", "description" => "Truy cập danh sách thẻ"),
      array("name" => "tag.create", "slug" => "tag.create", "group" => "tag", "description" => "Thêm thẻ"),
      array("name" => "tag.update", "slug" => "tag.update", "group" => "tag", "description" => "Cập nhật thẻ"),
      array("name" => "tag.view", "slug" => "tag.view", "group" => "tag", "description" => "Xem chi tiết thẻ"),
      array("name" => "tag.delete", "slug" => "tag.delete", "group" => "tag", "description" => "Xoá thẻ"),
      // quyền đơn hàng/order
      array("name" => "order.access", "slug" => "order.access", "group" => "order", "description" => "Truy cập danh đơn hàng"),
      array("name" => "order.create", "slug" => "order.create", "group" => "order", "description" => "Thêm đơn hàng"),
      array("name" => "order.update", "slug" => "order.update", "group" => "order", "description" => "Cập nhật đơn hàng"),
      array("name" => "order.view", "slug" => "order.view", "group" => "order", "description" => "Xem chi tiết đơn hàng"),
      array("name" => "order.delete", "slug" => "order.delete", "group" => "order", "description" => "Xoá đơn hàng"),
      // phân quyền
      array("name" => "permission", "slug" => "permission", "group" => "permission", "description" => "Truy cập danh sách quyền"),
      // quyền vai trò/role
      array("name" => "role.access", "slug" => "role.access", "group" => "role", "description" => "Truy cập danh vai trò"),
      array("name" => "role.create", "slug" => "role.create", "group" => "role", "description" => "Thêm vai trò"),
      array("name" => "role.update", "slug" => "role.update", "group" => "role", "description" => "Cập nhật vai trò"),
      array("name" => "role.view", "slug" => "role.view", "group" => "role", "description" => "Xem chi tiết vai trò"),
      array("name" => "role.delete", "slug" => "role.delete", "group" => "role", "description" => "Xoá vai trò"),
      // người dùng
      array("name" => "user.access", "slug" => "user.access", "group" => "user", "description" => "Truy cập danh người dùng"),
      array("name" => "user.create", "slug" => "user.create", "group" => "user", "description" => "Thêm người dùng"),
      array("name" => "user.update", "slug" => "user.update", "group" => "user", "description" => "Cập nhật người dùng"),
      array("name" => "user.view", "slug" => "user.view", "group" => "user", "description" => "Xem chi tiết người dùng"),
      array("name" => "user.delete", "slug" => "user.delete", "group" => "user", "description" => "Xoá người dùng"),
    );
    Permission::insert($permissions);
    $permissions_ids = array();
    for ($i = 0; $i < count($permissions); ++$i) {
      $permissions_ids[] = $i + 1;
    }
    // Roles
    $role_admin = Role::firstOrCreate(['name' => 'admin', 'slug' => 'admin']);
    $role_admin->permissions()->sync($permissions_ids);
    $role_customer = Role::firstOrCreate(['name' => 'customer', 'slug' => 'customer']);
    // Users
    $user = User::firstOrCreate(
      ['email' => 'admin@admin.admin'],
      [
        'name' => 'Admin',
        'password' => Hash::make('12345678'),
        'email_verified_at' => now()
      ]
    );
    $user->roles()->sync([$role_admin->id]);

    $user = User::firstOrCreate(
      ['email' => 'test@test.test'],
      [
        'name' => 'Customer',
        'password' => Hash::make('12345678'),
        'email_verified_at' => now()
      ]
    );
    $user->roles()->sync([$role_customer->id]);
  }
}
