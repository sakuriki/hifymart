<template>
  <v-container>
    <v-form
      ref="form"
      v-model="valid"
    >
      <v-card>
        <v-card-title>
          <span>Sửa Slide</span>
          <v-spacer />
          <v-btn
            :disbaled="!valid"
            color="primary"
            @click="beforeAddImage"
          >
            Thêm slide
            <v-icon right>
              mdi-shape-rectangle-plus
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
          <v-simple-table>
            <template #default>
              <thead>
                <tr>
                  <th>
                    Ảnh
                  </th>
                  <th>
                    Title
                  </th>
                  <th>
                    Order
                  </th>
                  <th>
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!data || data.length <= 0">
                  <td
                    colspan="4"
                    class="text-center"
                  >
                    Chưa có slide nào
                  </td>
                </tr>
                <tr
                  v-for="(image, i) in data"
                  :key="i"
                >
                  <td>
                    <img
                      :src="apiUrl+image.url"
                      :alt="image.title"
                      width="100"
                      height="100"
                    >
                  </td>
                  <td>{{ image.title }}</td>
                  <td>{{ image.order }}</td>
                  <td>
                    <v-tooltip bottom>
                      <template #activator="{ on, attrs }">
                        <v-btn
                          v-bind="attrs"
                          color="success"
                          icon
                          v-on="on"
                          @click="beforeEditImage(image)"
                        >
                          <v-icon>mdi-pencil-outline</v-icon>
                        </v-btn>
                      </template>
                      <span>Sửa</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template #activator="{ on, attrs }">
                        <v-btn
                          color="error"
                          v-bind="attrs"
                          icon
                          v-on="on"
                          @click="removeImage(image)"
                        >
                          <v-icon>mdi-delete-outline</v-icon>
                        </v-btn>
                      </template>
                      <span>Xoá</span>
                    </v-tooltip>
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
      </v-card>
    </v-form>
    <v-dialog
      v-model="dialog"
      persistent
      max-width="600px"
    >
      <v-card>
        <v-card-title>
          <span class="headline">Slide ảnh</span>
        </v-card-title>
        <v-card-text>
          <v-file-input
            v-if="!edit"
            v-model="dialogData.image"
            accept="image/*"
            label="Ảnh đại diện*"
            show-size
            prepend-icon="mdi-camera"
            :rules="[rules.image, rules.size]"
            required
          />
          <v-text-field
            v-model="dialogData.title"
            label="Title*"
            :rules="[rules.required]"
            required
          />
          <v-text-field
            v-model="dialogData.order"
            label="Order*"
            :rules="[rules.required]"
            required
          />
          <small>*indicates required field</small>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="blue darken-1"
            text
            @click="refreshDialog()"
          >
            Huỷ
          </v-btn>
          <v-btn
            color="blue darken-1"
            text
            @click="processImage()"
          >
            Lưu
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
      let { slides } = await app.$axios.$get("/admin/slides");
      return {
        data: slides,
      }
    } catch (err) {
      return error({ statusCode: err.response.status, message: err.message })
    }
  },
  data() {
    return {
      valid: false,
      rules: {
        image: v => !v || /^image\/(jpg|jpeg|png|bmp|gif|svg|webp)$/i.test(v.type) || 'Ảnh không hợp lệ',
        size: v => !v || v.size < 5000000 || 'Ảnh phải nhỏ hơn 5 MB',
        required: v => !!v || 'Không được bỏ trống',
      },
      slide_image: [],
      dialog: false,
      edit: false,
      dialogData: {
        title: null,
        image: null,
        order: null
      }
    };
  },
  computed: {
    apiUrl() {
      return process.env.apiUrl
    },
  },
  methods: {
    beforeEditImage(image) {
      this.edit = true;
      this.dialogData.url = image.url;
      this.dialogData.title = image.title;
      this.dialogData.order = image.order;
      this.dialog = true;
    },
    beforeAddImage() {
      this.edit = false;
      this.dialog = true;
    },
    processImage() {
      if (this.edit) {
        let index = this.data.findIndex(p => p.url == this.dialogData.url);
        this.data.splice(index, 1, {
          title: this.dialogData.title,
          url: this.dialogData.url,
          order: this.dialogData.order,
        })
        this.updateImage()
      } else {
        this.addImage()
      }
    },
    refreshDialog() {
      this.dialog = false;
      this.dialogData = {
        title: null,
        image: null,
        order: null
      }
    },
    updateImage() {
      let data = {
        slides: this.data
      }
      this.$axios.patch("/admin/slides", data)
      .then(() => {
        this.refreshDialog();
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
    addImage() {
      let formData = new FormData();
      for (let key of Object.keys(this.dialogData)) {
        formData.append(key, this.dialogData[key]);
      }
      this.$axios.post("/admin/slides", formData)
      .then((res) => {
        this.data = res.data.slides;
        this.refreshDialog();
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
    removeImage(data) {
      if (data.url) {
        this.$axios.delete("/admin/slides", {
          url: data.url
        })
        .then(() => {
          let index = this.data.findIndex(p => p.url == data.url);
          if (index > -1) {
            this.data.splice(index, 1);
          }
        })
        .catch(() => {
          this.$notifier.showMessage({
            content: 'Có lỗi, vui lòng thử lại',
            color: 'error',
            right: false
          })
        });
      } else {
        this.slide_image.splice(data.index, 1)
      }
    },
  }
};
</script>
