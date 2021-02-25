<template>
  <v-container>
    <v-form
      ref="form"
      v-model="valid"
    >
      <v-card>
        <v-card-title>
          <span>Sửa bình luận</span>
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
            color="error"
            class="ml-2"
            @click="beforeDelete"
          >
            Xoá bình luận
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
          <template v-if="!data.user_id">
            <v-text-field
              v-model="data.name"
              label="Tên người đăng"
              flat
              dense
              outlined
              required
              class="d-flex"
              :rules="[rules.required]"
            />
            <v-text-field
              v-model="data.email"
              label="Địa chỉ email"
              flat
              dense
              outlined
              required
              class="d-flex"
              :rules="[rules.required]"
            />
            <v-text-field
              v-model="data.phone"
              label="Số điện thoại"
              flat
              dense
              outlined
              required
              class="d-flex"
            />
          </template>
          <v-textarea
            v-model="data.content"
            outlined
            label="Nội dung"
            :rules="[rules.required]"
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
      permission: "comment.update"
    }
  },
  async asyncData({ app, params }) {
    let { comment } = await app.$axios.$get("/admin/comments/" + params.id);
    return {
      data: comment,
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
        email: null,
        phone: null,
        content: null,
      },
    };
  },
  methods: {
    save() {
      this.$axios.patch("/admin/comments/" + this.$route.params.id, this.data)
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
    async beforeDelete() {
      let confirm = await this.$refs.confirm.open('Xoá bình luận', 'Bạn có chắc muốn xoá bình luận này? Đây là hành động vĩnh viễn và không thể thay đổi!', { color: 'red' });
      if (confirm) {
        this.deleteItem()
      }
    },
    deleteItem() {
      this.$axios.delete("/admin/comments/" + this.data.id)
      .then(() => {
        this.$router.push({
          name: "admin-comments"
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
