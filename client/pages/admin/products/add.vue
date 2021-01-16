<template>
  <v-container>
    <v-form
      ref="form"
      v-model="valid"
    >
      <v-row>
        <v-col
          cols="12"
          sm="12"
          md="8"
        >
          <v-card class="mb-3">
            <v-card-text>
              <v-text-field
                v-model="data.name"
                label="Tên sản phẩm"
                flat
                dense
                outlined
                required
                :rules="[rules.required]"
              />
              <v-text-field
                v-model="data.slug"
                :label="(!slug||slugFocus)?'URL tuỳ chỉnh':slug"
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
                outlined
                label="Giới thiệu"
                :rules="[rules.required]"
              />
            </v-card-text>
          </v-card>
          <v-card class="mb-3">
            <v-card-text>
              <v-card-title>Thông tin chi tiết</v-card-title>
              <v-row>
                <v-col cols="6">
                  <v-text-field
                    v-model="data.price"
                    label="Giá sản phẩm"
                    prepend-icon="mdi-cash"
                    :rules="[rules.required]"
                  />
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="data.quantity"
                    label="Hàng sẵn có"
                    type="number"
                    prepend-icon="mdi-package-variant"
                    :rules="[rules.required]"
                  />
                </v-col>
              </v-row>
              <v-card-title>Khuyến mãi</v-card-title>
              <v-row>
                <v-col cols="6">
                  <v-text-field
                    v-model="data.sale_off_price"
                    label="Giá khuyến mãi"
                    prepend-icon="mdi-cash"
                  />
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="data.sale_off_quantity"
                    label="Giới hạn số sản phẩm"
                    type="number"
                    prepend-icon="mdi-package-variant"
                  />
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="6">
                  <v-menu
                    v-model="sale_off.menu_start"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template #activator="{ on, attrs }">
                      <v-text-field
                        v-model="sale_off.start"
                        label="Bắt đầu"
                        prepend-icon="mdi-calendar"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                      />
                    </template>
                    <v-date-picker
                      v-model="sale_off.start"
                      @input="sale_off.menu_start = false"
                    />
                  </v-menu>
                </v-col>
                <v-col cols="6">
                  <v-menu
                    v-model="sale_off.menu_end"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template #activator="{ on, attrs }">
                      <v-text-field
                        v-model="sale_off.end"
                        label="Kết thúc"
                        prepend-icon="mdi-calendar"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                      />
                    </template>
                    <v-date-picker
                      v-model="sale_off.end"
                      @input="sale_off.menu_end = false"
                    />
                  </v-menu>
                </v-col>
              </v-row>
              <v-card-title>Ảnh sản phẩm</v-card-title>
              <v-row class="ma-0">
                <v-col
                  v-for="image in productImages"
                  :key="image.url"
                  cols="3"
                  md="2"
                  class="pa-1"
                >
                  <v-hover v-slot="{ hover }">
                    <div
                      class="d-flex align-center"
                      style="position: relative;cursor: pointer"
                    >
                      <v-img
                        :src="image.url"
                        aspect-ratio="1"
                      />
                      <v-overlay
                        absolute
                        :value="hover"
                      >
                        <div class="align-self-center">
                          <v-tooltip bottom>
                            <template #activator="{ on, attrs }">
                              <v-icon
                                class="mx-2"
                                v-bind="attrs"
                                v-on="on"
                                @click.prevent="removeProductImage(image)"
                              >
                                mdi-close
                              </v-icon>
                            </template>
                            <span>Xoá ảnh</span>
                          </v-tooltip>
                        </div>
                      </v-overlay>
                    </div>
                  </v-hover>
                </v-col>
              </v-row>
              <v-file-input
                v-model="product_image"
                accept="image/*"
                label="Ảnh sản phẩm"
                show-size
                counter
                multiple
                small-chips
                prepend-icon="mdi-camera"
                :rules="[rules.imageMultiple, rules.sizeMultiple]"
              />
            </v-card-text>
          </v-card>
        </v-col>
        <v-col
          cols="12"
          md="4"
        >
          <v-card>
            <v-card-text>
              <v-img
                :src="featuredImage"
                max-height="300px"
                contain
              />
              <v-file-input
                v-model="preview"
                accept="image/*"
                label="Ảnh đại diện"
                show-size
                prepend-icon="mdi-camera"
                :rules="[rules.image, rules.size]"
              />
              <v-autocomplete
                v-model="data.brand_id"
                :items="brands"
                item-text="name"
                item-value="id"
                label="Nhãn hiệu"
                prepend-icon="mdi-alpha-n-box-outline"
                hint="Sản phẩm thuộc nhãn hiệu nào?"
                persistent-hint
                required
                :rules="[rules.required]"
              />
              <v-autocomplete
                v-model="data.category_id"
                :items="categories"
                item-text="name"
                item-value="id"
                label="Danh mục"
                prepend-icon="mdi-alpha-d-box-outline"
                hint="Sản phẩm thuộc danh mục nào?"
                persistent-hint
                required
                :rules="[rules.required]"
              />
              <v-combobox
                v-model="data.tags"
                :items="tags"
                label="Tags"
                multiple
                chips
                deletable-chips
                clearable
                prepend-icon="mdi-tag-outline"
                hint="Hash tag hỗ trợ SEO"
                persistent-hint
              />
              <v-btn
                :disbaled="!valid"
                color="primary"
                block
                @click="save"
              >
                Thêm sản phẩm
                <v-icon right>
                  mdi-content-save
                </v-icon>
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-form>
  </v-container>
</template>
<script>
export default {
  layout: "admin",
  middleware: "auth",
  meta: {
    auth: {
      permission: "product.create"
    }
  },
  async asyncData({ app }) {
    let { categories } = await app.$axios.$get("/categories");
    let { brands } = await app.$axios.$get("/brands");
    let { tags } = await app.$axios.$get("/tags");
    return {
      tags: tags,
      brands: brands,
      categories: categories
    }
  },
  data() {
    return {
      sale_off: {
        menu_start: false,
        menu_end: false,
        start: null,
        end: null
      },
      valid: false,
      rules: {
        image: v => !v || /^image\/(jpg|png|jpeg)$/i.test(v.type) || 'Ảnh không hợp lệ',
        imageMultiple: v => !v || !v.some(file => !(/^image\/(jpg|png|jpeg)$/i.test(file.type))) || 'Ảnh không hợp lệ',
        size: v => !v || v.size < 5000000 || 'Ảnh phải nhỏ hơn 5 MB',
        sizeMultiple: v => !v || !v.some(file => file.size > 5000000) || 'Ảnh phải nhỏ hơn 5 MB',
        required: v => !!v || 'Không được bỏ trống',
      },
      preview: null,
      product_image: [],
      data: {
        name: null,
        slug: null,
        description: null,
        brand_id: null,
        category_id: null,
        tags: [],
        price: null,
        quantity: null,
        sale_off_price: null,
        sale_off_quantity: null,
        sale_off_start: null,
        sale_off_end: null,
        featured_image: null,
        images: [],
      },
      categories: [],
      search_category: "",
      slug: null,
      slugFocus: false
    };
  },
  computed: {
    watchTitle() {
      return this.data.name
    },
    baseUrl() {
      return process.env.baseUrl + "/"
    },
    apiUrl() {
      return process.env.apiUrl
    },
    featuredImage() {
      return this.preview ? URL.createObjectURL(this.preview) : null
    },
    productImages() {
      return this.product_image.map((image, index) => {
        return {
          index: index,
          url: URL.createObjectURL(image)
        }
      });
    },
  },
  watch: {
    'sale_off.start': {
      handler() {
        this.updateStart()
      }
    },
    'sale_off.end': {
      handler() {
        this.updateEnd()
      }
    },
    watchTitle: {
      handler() {
        this.generate_slug()
      }
    }
  },
  mounted() {
    this.generate_slug = this.$debounce(this.generate_slug, 200);
  },
  methods: {
    save() {
      if(!this.$refs.form.validate()) return;
      let formData = new FormData();
      for (let key of Object.keys(this.data)) {
        formData.append(key, this.data[key]);
      }
      formData.delete('featured_image');
      formData.delete('images');
      formData.delete('tags');
      for (let tag of this.data.tags) {
        formData.append("tags[]", tag);
      }
      if(this.preview) formData.append("featured_image", this.preview);
      for (let file of this.product_image) {
        formData.append("images[]", file);
      }
      this.$axios.post("/admin/products", formData)
      .then(res => {
        this.$router.push({
          name: "admin-products-id",
          params: {
            id: res.data.product.id
          }
        })
      })
    },
    generate_slug() {
      this.slug = this.slugify(this.data.name);
    },
    updateStart() {
      this.data.sale_off_start = new Date(this.sale_off.start).toISOString()
    },
    updateEnd() {
      this.data.sale_off_end = new Date(this.sale_off.end).toISOString()
    },
    slugify(string) {
      const a = 'àáäâãåăæąçćčđďèéěėëêęğǵḧìíïîįłḿǹńňñòóöôœøṕŕřßşśšșťțùúüûǘůűūųẃẍÿýźžż·/_,:;'
      const b = 'aaaaaaaaacccddeeeeeeegghiiiiilmnnnnooooooprrsssssttuuuuuuuuuwxyyzzz------'
      const p = new RegExp(a.split('').join('|'), 'g')
      return string.toString().toLowerCase()
      .replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a')
      .replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e')
      .replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i')
      .replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o')
      .replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u')
      .replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y')
      .replace(/đ/gi, 'd')
      .replace(/\s+/g, '-')
      .replace(p, c => b.charAt(a.indexOf(c)))
      .replace(/&/g, '-and-')
      .replace(/[^\w-]+/g, '')
      .replace(/--+/g, '-')
      .replace(/^-+/, '')
      .replace(/-+$/, '')
    },
    removeProductImage(data) {
      this.product_image.splice(data.index, 1)
    }
  },
};
</script>
