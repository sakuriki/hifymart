<template>
  <v-container>
    <v-form
      ref="form"
      v-model="valid"
    >
      <v-card>
        <v-card-title>
          <span>Sửa mã giảm giá</span>
          <v-spacer />
          <v-btn
            :disbaled="!valid"
            color="primary"
            @click="save"
          >
            Lưu thay đổi
            <v-icon right>
              mdi-content-save-edit
            </v-icon>
          </v-btn>
          <v-btn
            v-if="canDelete"
            color="error"
            class="ml-2"
            @click="beforeDelete"
          >
            Xoá danh mục
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
            v-model="data.code"
            label="Mã giảm giá"
            placeholder="Để trống để tạo mã tự động"
            flat
            dense
            outlined
            required
            autocomplete="off"
          />
          <v-text-field
            v-model="data.value"
            label="Giá trị"
            placeholder="Chỉ nhập số, không thêm '%' hay 'đ' ở đây"
            :suffix="data.is_percent?'%':'đ'"
            flat
            dense
            outlined
            required
            autocomplete="off"
            type="number"
            :rules="[rules.required, ruleValue]"
          />
          <v-switch
            v-model="data.is_percent"
            :label="percentLabel"
          />
        </v-card-text>
        <v-card-title>Cài đặt</v-card-title>
        <v-card-text>
          <v-text-field
            v-model="data.number"
            label="Giới hạn số lượng"
            placeholder="Để trống nếu không có giới hạn"
            flat
            dense
            outlined
            required
            autocomplete="off"
          />
          <v-row>
            <v-col cols="6">
              <v-text-field
                v-model="data.min"
                label="Đơn hàng tối thiểu"
                placeholder="Để trống nếu không có giới hạn"
                flat
                dense
                outlined
                required
                hint="Giá trị đơn hàng tối thiểu để áp dụng mã giả giá"
                autocomplete="off"
              />
            </v-col>
            <v-col cols="6">
              <v-text-field
                v-model="data.max"
                label="Giảm tối đa"
                placeholder="Để trống nếu không có giới hạn"
                flat
                dense
                outlined
                required
                hint="Chỉ có tác dụng với mã giảm theo %, là số lượng tiền giảm tối đa"
                autocomplete="off"
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="6">
              <v-menu
                v-model="date.menu_start"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template #activator="{ on, attrs }">
                  <v-text-field
                    v-model="date.start"
                    label="Ngày bắt đầu"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  />
                </template>
                <v-date-picker
                  v-model="date.start"
                  @input="date.menu_start = false"
                />
              </v-menu>
            </v-col>
            <v-col cols="6">
              <v-menu
                v-model="date.menu_end"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template #activator="{ on, attrs }">
                  <v-text-field
                    v-model="date.end"
                    label="Ngày hết hạn"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  />
                </template>
                <v-date-picker
                  v-model="date.end"
                  @input="date.menu_end = false"
                />
              </v-menu>
            </v-col>
          </v-row>
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
      permission: "coupon.update"
    }
  },
  async asyncData({ app, params, error }) {
    try {
      let { coupon } = await app.$axios.$get("/admin/coupons/" + params.id);
      return {
        data: coupon
      }
    } catch (err) {
      return error({ statusCode: err.response ? err.response.status : 422, message: err.message || 'Có lỗi xảy ra' })
    }
  },
  data() {
    return {
      valid: false,
      rules: {
        required: v => !!v || 'Không được bỏ trống',
      },
      data: {
        code: null,
        value: null,
        number: null,
        min: null,
        max: null,
        is_percent: false,
        starts_at: null,
        expires_at: null,
      },
      date: {
        menu_start: false,
        menu_end: false,
        start: null,
        end: null
      },
    };
  },
  computed: {
    percentLabel() {
      let text = this.data.is_percent ? "Giảm theo phần trăm" : "Giảm cố định";
      return `Loại: ${text}`
    },
    canDelete() {
      return this.$auth.user.permissions.includes("coupon.delete")
    },
  },
  watch: {
    'date.start': {
      handler() {
        this.updateStart()
      }
    },
    'date.end': {
      handler() {
        this.updateEnd()
      }
    },
    'data.is_percent': {
      handler() {
        this.$nextTick(() => {
          this.$refs.form.validate();
        });
      }
    }
  },
  mounted() {
    this.date.start = this.data.starts_at
      ? new Date(this.data.starts_at).toISOString().slice(0,10)
      : null;
    this.date.end = this.data.expires_at
      ? new Date(this.data.expires_at).toISOString().slice(0,10)
      : null;
  },
  methods: {
    save() {
      this.$axios.patch("/admin/coupons/" + this.$router.params.id, this.data)
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
    updateStart() {
      this.data.starts_at = new Date(this.date.start).toISOString()
    },
    updateEnd() {
      this.data.expires_at = new Date(this.date.end).toISOString()
    },
    ruleValue(value) {
      if (this.data.is_percent) {
        return value <= 100 || "Giám theo phần trăm tối đa 100%"
      }
      return true;
    },
    async beforeDelete() {
      if (!this.canDelete) {
        return this.$notifier.showMessage({
          content: 'Bạn không có quyền thực hiện hành động này!',
          color: 'error',
          right: false
        })
      }
      let confirm = await this.$refs.confirm.open('Xoá mã giảm giá', 'Bạn có chắc muốn xoá mã giảm giá này? Đây là hành động vĩnh viễn và không thể thay đổi!', { color: 'red' });
      if (confirm) {
        this.deleteItem()
      }
    },
    deleteItem() {
      this.$axios.delete("/admin/coupons/" + this.$route.params.id)
      .then(() => {
        this.$router.push({
          name: "admin-coupons"
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
