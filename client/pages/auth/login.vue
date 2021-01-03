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
            <span>Đăng nhập</span>
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
                  v-model="data.email"
                  label="Enter your e-mail address"
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
                <v-layout justify-space-between>
                  <v-btn
                    :disabled="!valid"
                    color="primary"
                    @click="login"
                  >
                    Đăng nhập
                  </v-btn>
                  <NuxtLink
                    class="text-decoration-none"
                    :to="{name: 'auth-register'}"
                  >
                    Chưa có tài khoản? Đăng ký
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
        email: "",
        password: ""
      },
      rules: {
        email: v => /.+@.+\..+/.test(v) || 'Địa chỉ email chưa hợp lệ',
        required: v => !!v || 'Không được bỏ trống',
        min: v => v.length >= 8 || 'Tối thiểu 8 ký tự'
      },
    }
  },
  computed: {
    errors() {
      return this.$store.getters.errors
    }
  },
  methods: {
    async login() {
      try {
        await this.$auth.login({ data: this.data });
      } catch (e) {
        return;
      }
      this.$router.push(
        this.$route.query.redirect ? this.$route.query.redirect : "/"
      );
    },
  },
}
</script>
