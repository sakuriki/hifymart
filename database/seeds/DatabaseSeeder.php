<?php

use App\Models\Tax;
use App\Models\Role;
use App\Models\User;
use App\Models\Setting;
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
      array("name" => "dashboard", "group" => "dashboard", "description" => "Truy cập dashboard"),
      // quyền thay đổi thông tin app
      array("name" => "setting", "group" => "setting", "description" => "Thay đổi thông tin app"),
      // phân quyền
      array("name" => "permission", "group" => "permission", "description" => "Truy cập danh sách quyền"),
      // quyền nhãn hiệu/brand
      array("name" => "brand.access", "group" => "brand", "description" => "Truy cập danh sách nhãn hiệu"),
      array("name" => "brand.create", "group" => "brand", "description" => "Thêm nhãn hiệu"),
      array("name" => "brand.update", "group" => "brand", "description" => "Cập nhật nhãn hiệu"),
      array("name" => "brand.view", "group" => "brand", "description" => "Xem chi tiết nhãn hiệu"),
      array("name" => "brand.delete", "group" => "brand", "description" => "Xoá nhãn hiệu"),
      // quyền danh mục/category
      array("name" => "category.access", "group" => "category", "description" => "Truy cập danh sách danh mục"),
      array("name" => "category.create", "group" => "category", "description" => "Thêm danh mục"),
      array("name" => "category.update", "group" => "category", "description" => "Cập nhật danh mục"),
      array("name" => "category.view", "group" => "category", "description" => "Xem chi tiết danh mục"),
      array("name" => "category.delete", "group" => "category", "description" => "Xoá danh mục"),
      // quyền danh bình luận
      array("name" => "comment.access", "group" => "comment", "description" => "Truy cập danh sách bình luận"),
      array("name" => "comment.create", "group" => "comment", "description" => "Thêm bình luận"),
      array("name" => "comment.update", "group" => "comment", "description" => "Cập nhật bình luận"),
      array("name" => "comment.view", "group" => "comment", "description" => "Xem chi tiết bình luận"),
      array("name" => "comment.delete", "group" => "comment", "description" => "Xoá bình luận"),
      // quyền danh mã giảm giá/coupon
      array("name" => "coupon.access", "group" => "coupon", "description" => "Truy cập danh sách mã giảm giá"),
      array("name" => "coupon.create", "group" => "coupon", "description" => "Thêm mã giảm giá"),
      array("name" => "coupon.update", "group" => "coupon", "description" => "Cập nhật mã giảm giá"),
      array("name" => "coupon.view", "group" => "coupon", "description" => "Xem chi tiết mã giảm giá"),
      array("name" => "coupon.delete", "group" => "coupon", "description" => "Xoá mã giảm giá"),
      // quyền đơn hàng/order
      array("name" => "order.access", "group" => "order", "description" => "Truy cập danh đơn hàng"),
      array("name" => "order.create", "group" => "order", "description" => "Thêm đơn hàng"),
      array("name" => "order.update", "group" => "order", "description" => "Cập nhật đơn hàng"),
      array("name" => "order.view", "group" => "order", "description" => "Xem chi tiết đơn hàng"),
      array("name" => "order.delete", "group" => "order", "description" => "Xoá đơn hàng"),
      // quyền sản phẩm/product
      array("name" => "product.access", "group" => "product", "description" => "Truy cập danh sách sản phẩm"),
      array("name" => "product.create", "group" => "product", "description" => "Thêm sản phẩm"),
      array("name" => "product.update", "group" => "product", "description" => "Cập nhật sản phẩm"),
      array("name" => "product.view", "group" => "product", "description" => "Xem chi tiết sản phẩm"),
      array("name" => "product.delete", "group" => "product", "description" => "Xoá sản phẩm"),
      // quyền đánh giá/rating
      array("name" => "rating.access", "group" => "rating", "description" => "Truy cập danh đánh giá"),
      array("name" => "rating.create", "group" => "rating", "description" => "Thêm đánh giá"),
      array("name" => "rating.update", "group" => "rating", "description" => "Cập nhật đánh giá"),
      array("name" => "rating.view", "group" => "rating", "description" => "Xem chi tiết đánh giá"),
      array("name" => "rating.delete", "group" => "rating", "description" => "Xoá đánh giá"),
      // quyền vai trò/role
      array("name" => "role.access", "group" => "role", "description" => "Truy cập danh vai trò"),
      array("name" => "role.create", "group" => "role", "description" => "Thêm vai trò"),
      array("name" => "role.update", "group" => "role", "description" => "Cập nhật vai trò"),
      array("name" => "role.view", "group" => "role", "description" => "Xem chi tiết vai trò"),
      array("name" => "role.delete", "group" => "role", "description" => "Xoá vai trò"),
      // quyền thẻ tag
      array("name" => "tag.access", "group" => "tag", "description" => "Truy cập danh sách thẻ"),
      array("name" => "tag.create", "group" => "tag", "description" => "Thêm thẻ"),
      array("name" => "tag.update", "group" => "tag", "description" => "Cập nhật thẻ"),
      array("name" => "tag.view", "group" => "tag", "description" => "Xem chi tiết thẻ"),
      array("name" => "tag.delete", "group" => "tag", "description" => "Xoá thẻ"),
      // quyền loại thuế
      array("name" => "tax.access", "group" => "tax", "description" => "Truy cập danh sách loại thuế"),
      array("name" => "tax.create", "group" => "tax", "description" => "Thêm loại thuế"),
      array("name" => "tax.update", "group" => "tax", "description" => "Cập nhật loại thuế"),
      array("name" => "tax.view", "group" => "tax", "description" => "Xem chi tiết loại thuế"),
      array("name" => "tax.delete", "group" => "tax", "description" => "Xoá loại thuế"),
      // người dùng
      array("name" => "user.access", "group" => "user", "description" => "Truy cập danh người dùng"),
      array("name" => "user.create", "group" => "user", "description" => "Thêm người dùng"),
      array("name" => "user.update", "group" => "user", "description" => "Cập nhật người dùng"),
      array("name" => "user.view", "group" => "user", "description" => "Xem chi tiết người dùng"),
      array("name" => "user.delete", "group" => "user", "description" => "Xoá người dùng"),
    );
    Permission::insert($permissions);
    $permissions_ids = array();
    for ($i = 0; $i < count($permissions); ++$i) {
      $permissions_ids[] = $i + 1;
    }
    // Roles
    $role_admin = Role::firstOrCreate(['name' => 'Quản trị viên', 'slug' => 'admin']);
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
    // Thuế
    Tax::firstOrCreate(['name' => 'None', 'value' => '0']);
    Tax::firstOrCreate(['name' => 'VAT', 'value' => '10']);
    /**
     * Cài đặt
     */
    // Cài đặt chung
    $settings = [
      ['name' => 'logo', 'value' => null],
      ['name' => 'favicon', 'value' => null],
      ['name' => 'app-name', 'value' => 'HifyMart'],
      ['name' => 'working-time', 'value' => '9h đến 20h, chủ nhật 10h đến 19h'],
      ['name' => 'contact-mail', 'value' => null],
      ['name' => 'contact-phone', 'value' => null],
      ['name' => 'contact-mobile', 'value' => null],
      ['name' => 'contact-address', 'value' => null],
      ['name' => 'facebook', 'value' => null],
      ['name' => 'twitter', 'value' => null],
      ['name' => 'instagram', 'value' => null],
      ['name' => 'youtube', 'value' => null],
      ['name' => 'bank-info', 'value' => '<p>Lưu ý: Quý khách vui lòng đợi nhân viên liên hệ xác nhận đơn hàng trước khi thực hiện thanh toán.</p><p>Công Ty TNHH HifyMart:</p><p>BIDV</p><p>Số tài khoản : 36210000123456</p><p>Ngân hàng TMCP Đầu tư và Phát triển Việt Nam – Hà Nội</p>'],
      ['name' => 'delivery', 'value' => '<p>Phí vận chuyển: 19.000đ</p><p>Miễn phí với đơn hàng từ 200.000đ trở lên</p><p>Chỉ hỗ trợ vận chuyển đối với đơn hàng từ 100.000đ. Với đơn hàng có giá trị thấp hơn, quý khách vui lòng mua tại showroom</p>'],
    ];
    Setting::insert($settings);
  }
}
