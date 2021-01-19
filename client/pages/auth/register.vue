<template>
  <v-container fill-height>
    <v-layout
      align-center
      justify-center
      fill-height
    >
      <v-flex
        xs12
        sm4
        elevation-6
      >
        <v-card>
          <v-sheet
            dark
            class="pa-5 primary"
          >
            <span>Đăng ký</span>
          </v-sheet>
          <v-card-text class="pt-4">
            <div>
              <v-form
                ref="form"
                v-model="valid"
              >
                <div
                  v-for="error in errors"
                  :key="error[0]"
                  class="d-flex"
                >
                  <span
                    class="flex error pa-2 ma-1 white--text"
                  >
                    {{ error[0] }}
                  </span>
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
                  v-model="data.password"
                  label="Nhập mật khẩu"
                  min="8"
                  :append-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                  :type="showPassword ? 'text' : 'password'"
                  :rules="[rules.required, rules.min]"
                  counter
                  required
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
                  @click:append="() => (showPassword = !showPassword)"
                />
                <v-layout justify-space-between>
                  <v-btn
                    :disabled="!valid"
                    color="primary"
                    @click="register"
                  >
                    Đăng ký
                  </v-btn>
                  <NuxtLink
                    class="text-decoration-none"
                    :to="{name: 'auth-login'}"
                  >
                    Đã có tài khoản? Đăng nhập
                  </NuxtLink>
                </v-layout>
              </v-form>
            </div>
          </v-card-text>
        </v-card>
      </v-flex>
    </v-layout>
  </v-container>
</template>
<script>
export default {
  middleware: "guest",
  data() {
    return {
      valid: false,
      showPassword: false,
      data: {
        name: "",
        email: "",
        password: "",
        password_confirmation: ""
      },
      rules: {
        email: v => /.+@.+\..+/.test(v) || 'Địa chỉ email chưa hợp lệ',
        required: v => !!v || 'Không được bỏ trống',
        min: v => !v || v.length >= 8 || 'Tối thiểu 8 ký tự'
      },
    }
  },
  computed: {
    passwordConfirmationRule() {
      return () => (this.data.password === this.data.password_confirmation) || 'Mật khẩu nhập lại chưa khớp'
    },
    errors() {
      return this.$store.getters.errors
    }
  },
  methods: {
    async register() {
      try {
        await this.$axios.post("/auth/register", this.data);
      } catch (e) {
        this.$notifier.showMessage({
          content: 'Có lỗi, vui lòng thử lại',
          color: 'error',
          right: false
        })
      }
      this.$auth.login({ data: this.data });
      this.$router.push({ name: "index" });
    }
  },
}
</script>
