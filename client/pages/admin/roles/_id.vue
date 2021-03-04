<template>
  <v-container>
    <v-form
      ref="form"
      v-model="valid"
    >
      <v-card>
        <v-card-title>
          <span>Sửa Role</span>
          <v-spacer />
          <v-btn
            color="primary"
            :disabled="!valid && !loading"
            @click="saveRole"
          >
            Lưu thay đổi
          </v-btn>
          <v-btn
            v-if="canDelete"
            color="error"
            class="ml-2"
            @click="beforeDelete"
          >
            Xoá vai trò
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
            label="Tên role"
            flat
            dense
            outlined
            required
            :rules="[v => !!v || 'Tên role không được để trống']"
          />
          <v-textarea
            v-model="data.description"
            hide-details
            outlined
            label="Giới thiệu ngắn"
          />
        </v-card-text>
      </v-card>
      <v-card class="mt-3">
        <v-card-title>Phân quyền</v-card-title>
        <v-card-text>
          <v-treeview
            v-model="data.permissions"
            shaped
            hoverable
            selectable
            open-on-click
            :open="open_selected"
            :items="permissions"
          >
            <template #label="{ item }">
              {{ item.name }} {{ item.description ? ': ' + item.description : '' }}
            </template>
          </v-treeview>
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
      permission: "role.update"
    }
  },
  async asyncData({ app, params, error }) {
    try {
      const [permissions, role] = await Promise.all([
        app.$axios.$get("/admin/permissions"),
        app.$axios.$get("/admin/roles/" + params.id)
      ])
      return {
        permissions: permissions.permissions,
        data: role.role,
        selected_permissions: role.role.permissions
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
      data: {
        name: null,
        slug: null,
        description: null
      },
      loading: false,
    }
  },
  computed: {
    open_selected() {
      let arr = [];
      this.permissions.forEach(item => {
        if(item.children) {
          item.children.forEach(i => {
            if(this.data.permissions.includes(i.id))
              arr.push(item.id)
          })
        }
      });
      return arr
    },
    canDelete() {
      return this.$auth.user.permissions.includes("role.delete")
    }
  },
  methods: {
    saveRole() {
      this.loading = true;
      this.$axios.patch("/admin/roles/" + this.$route.params.id, this.data)
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
    async beforeDelete() {
      if (!this.canDelete) {
        return this.$notifier.showMessage({
          content: 'Bạn không có quyền thực hiện hành động này!',
          color: 'error',
          right: false
        })
      }
      let confirm = await this.$refs.confirm.open('Xoá vai trò', 'Bạn có chắc muốn xoá vai trò này? Đây là hành động vĩnh viễn và không thể thay đổi!', { color: 'red' });
      if (confirm) {
        this.deleteItem()
      }
    },
    deleteItem() {
      this.$axios.delete("/admin/roles/" + this.$route.params.id)
      .then(() => {
        this.$router.push({
          name: "admin-roles"
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
  },
}
</script>
