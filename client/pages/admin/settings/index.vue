<template>
  <v-container>
    <v-form
      ref="form"
      v-model="valid"
    >
      <v-card>
        <v-card-title>
          <span>Cài đặt</span>
          <v-spacer />
          <v-btn
            color="primary"
            :disabled="!valid && !loading"
            @click="save"
          >
            Lưu thay đổi
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
          <v-card-subtitle>Chung</v-card-subtitle>
          <v-text-field
            v-model="data['app-name']"
            label="Tên ứng dụng"
            flat
            dense
            outlined
            required
            :rules="[rules.required]"
          />
          <v-text-field
            v-model="data['working-time']"
            label="Thời gian hoạt động"
            flat
            dense
            outlined
            required
            :rules="[rules.required]"
          />
          <v-card-subtitle>Liên hệ</v-card-subtitle>
          <v-text-field
            v-model="data['contact-mail']"
            label="Địa chỉ email"
            flat
            dense
            outlined
            required
            :rules="[rules.required]"
          />
          <v-text-field
            v-model="data['contact-phone']"
            label="Hotline"
            flat
            dense
            outlined
            required
            :rules="[rules.required]"
          />
          <v-text-field
            v-model="data['contact-mobile']"
            label="Số di động"
            flat
            dense
            outlined
            required
            :rules="[rules.required]"
          />
          <v-text-field
            v-model="data['contact-address']"
            label="Địa chỉ"
            flat
            dense
            outlined
            required
            :rules="[rules.required]"
          />
          <v-card-subtitle>Mạng xã hội</v-card-subtitle>
          <v-text-field
            v-model="data.facebook"
            label="Facebook"
            flat
            dense
            outlined
            required
          />
          <v-text-field
            v-model="data.twitter"
            label="Twitter"
            flat
            dense
            outlined
            required
          />
          <v-text-field
            v-model="data.instagram"
            label="Instagram"
            flat
            dense
            outlined
            required
          />
          <v-text-field
            v-model="data.youtube"
            label="Youtube"
            flat
            dense
            outlined
            required
          />
          <v-card-subtitle>Thông tin bank:</v-card-subtitle>
          <client-only>
            <quill-editor
              ref="editor"
              v-model="data['bank-info']"
            />
          </client-only>
          <v-card-subtitle>Thông tin vận chuyển:</v-card-subtitle>
          <client-only>
            <quill-editor
              ref="editor"
              v-model="data.delivery"
            />
          </client-only>
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
      permission: "setting"
    }
  },
  async asyncData({ app, error }) {
    try {
      let { data } = await app.$axios.$get("/settings");
      return {
        data
      }
    } catch (err) {
      return error({ statusCode: err.response.status, message: err.message })
    }
  },
  data() {
    return {
      valid: false,
      permissions: [],
      selected_permissions: [],
      data: {},
      loading: false,
      rules: {
        image: v => !v || /^image\/(jpg|png|jpeg)$/i.test(v.type) || 'Ảnh không hợp lệ',
        imageMultiple: v => !v || !v.some(file => !(/^image\/(jpg|png|jpeg)$/i.test(file.type))) || 'Ảnh không hợp lệ',
        size: v => !v || v.size < 5000000 || 'Ảnh phải nhỏ hơn 5 MB',
        sizeMultiple: v => !v || !v.some(file => file.size > 5000000) || 'Ảnh phải nhỏ hơn 5 MB',
        required: v => !!v || 'Không được bỏ trống',
      },
    }
  },
  methods: {
    save() {
      this.loading = true;
      this.$axios.patch("/admin/settings", this.data)
      .then(res => {
        if(res.status) {
          this.$notifier.showMessage({
            content: 'Lưu thành công',
            color: 'success',
            right: false
          })
        }
      })
      .catch(() => {
        this.$notifier.showMessage({
          content: 'Có lỗi, vui lòng thử lại',
          color: 'error',
          right: false
        })
      })
      .then(() => {
        this.loading = false
      });
    },
  },
}
</script>
<style>
.ql-container.ql-snow {
  min-height: 200px;
}
</style>
