<template>
  <v-container>
    <v-form
      ref="form"
      v-model="valid"
    >
      <v-card>
        <v-card-title>
          <span>Sửa thông tin người dùng</span>
          <v-spacer />
          <v-btn
            :disabled="!valid"
            color="primary"
            @click="save"
          >
            Lưu thay đổi
            <v-icon right>
              mdi-content-save
            </v-icon>
          </v-btn>
          <v-btn
            v-if="canDelete"
            color="error"
            class="ml-2"
            @click="beforeDelete"
          >
            Xoá người dùng
            <v-icon right>
              mdi-delete-outline
            </v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <div class="mb-2">
            <v-slide-x-transition
              v-for="error in $store.getters.errors"
              :key="error[0]"
            >
              <v-alert
                dense
                dismissible
                type="error"
              >
                {{ error[0] }}
              </v-alert>
            </v-slide-x-transition>
          </div>
          <v-text-field
            v-model="data.name"
            label="Tên hiển thị"
            :rules="[rules.required, rules.min]"
            required
          />
          <v-text-field
            v-model="data.email"
            label="Địa chỉ emal"
            :rules="[rules.required, rules.email]"
            required
          />
          <v-text-field
            v-model="data.phone"
            label="Số điện thoại"
          />
          <v-switch
            v-model="changePass"
            label="Đổi mật khẩu"
          />
          <v-text-field
            v-show="changePass"
            v-model="data.password"
            label="Nhập mật khẩu"
            min="8"
            :append-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
            :type="showPassword ? 'text' : 'password'"
            :rules="[passwordRequired, passwordMin]"
            counter
            autocomplete="new-password"
            @click:append="() => (showPassword = !showPassword)"
          />
          <v-text-field
            v-show="changePass"
            v-model="data.password_confirmation"
            label="Nhập lại mật khẩu"
            min="8"
            :append-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
            :type="showPassword ? 'text' : 'password'"
            :rules="[passwordRequired, passwordMin, passwordConfirmationRule]"
            counter
            autocomplete="new-password"
            @click:append="() => (showPassword = !showPassword)"
          />
        </v-card-text>
      </v-card>
    </v-form>
    <ConfirmDialog ref="confirm" />
  </v-container>
</template>
<script>
export default {
  layout: "admin",
  middleware: "authorized",
  meta: {
    auth: {
      permission: "user.update"
    }
  },
  async asyncData({ app, params, error }) {
    try {
      let { user } = await app.$axios.$get("/admin/users/" + params.id);
      return {
        data: user,
      }
    } catch (err) {
      return error({ statusCode: err.response ? err.response.status : 422, message: err.message || 'Có lỗi xảy ra' })
    }
  },
  data() {
    return {
      valid: false,
      rules: {
        email: v => /.+@.+\..+/.test(v) || 'Địa chỉ email chưa hợp lệ',
        required: v => !!v || 'Không được bỏ trống',
        min: v => !v || v.length >= 8 || 'Tối thiểu 8 ký tự'
      },
      showPassword: false,
      changePass: false,
      data: {
        name: null,
        email: null,
        phone: null,
        password: null,
        password_confirmation: null
      },
    };
  },
  computed: {
    canDelete() {
      return this.$auth.user.permissions.includes("user.delete")
    },
    passwordConfirmationRule() {
      return () => (this.data.password === this.data.password_confirmation) || 'Mật khẩu nhập lại chưa khớp'
    }
  },
  watch: {
    changePass: {
      handler() {
        this.$refs.form.validate()
      }
    }
  },
  methods: {
    save() {
      let data = JSON.parse(JSON.stringify(this.data));
      if (!this.changePass) {
        delete data.password;
        delete data.password_confirmation;
      }
      this.$axios.patch("/admin/users/" + this.$route.params.id, data)
      .then(() => {
        this.$notifier.showMessage({
          content: 'Lưu thành công!',
          color: 'success',
          right: false
        })
      })
      .catch(() => {
        this.$notifier.showMessage({
          content: 'Có lỗi, vui lòng thử lại',
          color: 'error',
          right: false
        })
      })
    },
    passwordMin(v) {
      return !this.changePass || (!v || v.length >= 8 || 'Tối thiểu 8 ký tự');
    },
    passwordRequired(v) {
      return !this.changePass || (!!v || 'Không được bỏ trống');
    },
    async beforeDelete() {
      if (!this.canDelete) {
        return this.$notifier.showMessage({
          content: 'Bạn không có quyền thực hiện hành động này!',
          color: 'error',
          right: false
        })
      }
      let confirm = await this.$refs.confirm.open('Xoá người dùng', 'Bạn có chắc muốn xoá người dùng này? Đây là hành động vĩnh viễn và không thể thay đổi!', { color: 'red' });
      if (confirm) {
        this.deleteItem()
      }
    },
    deleteItem() {
      this.$axios.delete("/admin/users/" + this.$route.params.id)
      .then(() => {
        this.$router.push({
          name: "admin-users"
        });
        this.$notifier.showMessage({
          content: 'Xoá thành công',
          color: 'success',
          right: false
        })
      })
      .catch(() => {
        this.$notifier.showMessage({
          content: 'Có lỗi khi xoá, vui lòng thử lại',
          color: 'error',
          right: false
        })
      });
    },
  }
};
</script>
