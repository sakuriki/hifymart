<template>
  <v-container>
    <v-form
      ref="form"
      v-model="valid"
    >
      <v-card>
        <v-card-title>
          <span>Thêm loại thuế</span>
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
            label="Tên loại thuế"
            flat
            dense
            outlined
            required
            :rules="[rules.required]"
          />
          <v-text-field
            v-model="data.value"
            label="Giá trị"
            placeholder="Chỉ nhập số, không thêm cần '%'"
            flat
            dense
            outlined
            required
            :rules="[rules.required]"
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
      permission: "tax.create"
    }
  },
  data() {
    return {
      valid: false,
      rules: {
        required: v => !!v || 'Không được bỏ trống',
      },
      data: {
        name: null,
        value: null
      },
    };
  },
  methods: {
    save() {
      this.$axios.post("/admin/taxes", this.data)
      .then(res => {
        this.$router.push({
          name: "admin-taxes-id",
          params: {
            id: res.data.tax.id
          }
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
  }
};
</script>
