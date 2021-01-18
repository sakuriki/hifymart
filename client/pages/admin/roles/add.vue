<template>
  <v-container>
    <v-form
      ref="form"
      v-model="valid"
    >
      <v-card>
        <v-card-title>
          <span>Thêm Role Mới</span>
          <v-spacer />
          <v-btn
            color="primary"
            :disabled="!valid"
            @click="saveRole"
          >
            Lưu Role
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
          <v-text-field
            v-model="data.slug"
            :label="(!slug||slugFocus)?'Slug':slug"
            :placeholder="slug"
            flat
            dense
            outlined
            required
            :prefix="baseUrl"
            @focus="slugFocus=true"
            @blur="slugFocus=false"
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
            v-model="selected_permissions"
            shaped
            hoverable
            selectable
            open-on-click
            :items="permissions"
          >
            <template #label="{ item }">
              {{ item.name }} {{ item.description ? ': ' + item.description : '' }}
            </template>
          </v-treeview>
        </v-card-text>
      </v-card>
    </v-form>
  </v-container>
</template>
<script>
export default {
  layout: "admin",
  middleware: "auth",
  meta: {
    auth: {
      permission: "role.add"
    }
  },
  async asyncData({ app }) {
    let { permissions } = await app.$axios.$get("/admin/permissions");
    return {
      permissions: permissions
    }
  },
  data() {
    return {
      valid: false,
      permissions: [],
      selected_permissions: [],
      data: [],
      // data: {
      //   name: null,
      //   slug: null,
      //   description: null
      // },
      slug: null,
      slugFocus: false
    }
  },
  computed: {
    watchName() {
      return this.data.name
    },
    baseUrl() {
      return process.env.baseUrl + "/"
    },
  },
  watch: {
    watchName: {
      handler() {
        this.generate_slug()
      }
    }
  },
  mounted() {
    this.generate_slug = this.$debounce(this.generate_slug, 200);
  },
  methods: {
    saveRole() {
      this.$axios.post("/admin/roles", this.data)
      .then(res => {
        if(res.status) {
          this.$notifier.showMessage({
            content: 'Lưu thành công',
            color: 'success',
            right: false
          })
        }
      })
      .catch((err) => {
        console.error(err)
        this.$notifier.showMessage({
          content: 'Có lỗi, vui lòng thử lại',
          color: 'error',
          right: false
        })
      });
    },
    generate_slug() {
      this.slug = this.$slugify(this.data.name);
    },
  },
}
</script>
