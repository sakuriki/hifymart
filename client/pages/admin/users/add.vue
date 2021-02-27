<template>
  <v-container>
    <v-form
      ref="form"
      v-model="valid"
    >
      <v-card>
        <v-card-title>
          <span>Thêm người dùng</span>
          <v-spacer />
          <v-btn
            :disabled="!valid"
            color="primary"
            @click="save"
          >
            Thêm mới
            <v-icon right>
              mdi-content-save
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
          <v-text-field
            v-model="data.password"
            label="Nhập mật khẩu"
            min="8"
            :append-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
            :type="showPassword ? 'text' : 'password'"
            :rules="[rules.required, rules.min]"
            counter
            required
            autocomplete="new-password"
            @click:append="() => (showPassword = !showPassword)"
          />
          <v-text-field
            v-model="data.password_confirmation"
            label="Nhập lại mật khẩu"
            min="8"
            :append-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
            :type="showPassword ? 'text' : 'password'"
            :rules="[rules.required, rules.min, passwordConfirmationRule]"
            counter
            required
            autocomplete="new-password"
            @click:append="() => (showPassword = !showPassword)"
          />
        </v-card-text>
      </v-card>
    </v-form>
  </v-container>
</template>
<script>
export default {
  layout: "admin",
  middleware: "authorized",
  meta: {
    auth: {
      permission: "user.create"
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
    passwordConfirmationRule() {
      return () => (this.data.password === this.data.password_confirmation) || 'Mật khẩu nhập lại chưa khớp'
    }
  },
  methods: {
    async save() {
      try {
        let res = await this.$axios.$post("/admin/users", this.data);
        this.$router.push({
          name: "admin-users-id",
          params: {
            id: res.user.id
          }
        })
      } catch (e) {
        this.$notifier.showMessage({
          content: 'Có lỗi, vui lòng thử lại',
          color: 'error',
          right: false
        })
      }
    }
  }
};
</script>
