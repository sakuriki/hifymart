<template>
  <v-app>
    <v-main>
      <v-container>
        <v-layout wrap>
          <v-col
            md="8"
            cols="12"
          >
            <v-stepper
              v-model="cartStep"
              style="width:100%"
              alt-labels
            >
              <v-stepper-header>
                <v-stepper-step
                  :complete="cartStep > 1"
                  step="1"
                >
                  Nhập địa chỉ
                </v-stepper-step>
                <v-divider />
                <v-stepper-step
                  :complete="cartStep > 2"
                  step="2"
                >
                  Thanh toán
                </v-stepper-step>
                <v-divider />
                <v-stepper-step step="3">
                  Xác nhận
                </v-stepper-step>
              </v-stepper-header>
              <v-stepper-items>
                <v-stepper-content step="1">
                  <v-flex class="d-flex flex-wrap">
                    <span class="text-h5">Thông tin nhận hàng</span>
                    <v-spacer />
                    <span>Đã có tài khoản? <NuxtLink :to="{ name: 'auth-login', query: { redirect: $route.fullPath } }" class="pl-1">Đăng nhập</NuxtLink></span>
                  </v-flex>
                  <v-form
                    v-model="formValid"
                    class="pt-1"
                  >
                    <v-text-field
                      v-model="data.email"
                      label="Email liên hệ"
                      outlined
                      dense
                      :rules="[rules.required, rules.email]"
                    />
                    <v-text-field
                      v-model="data.name"
                      label="Họ tên"
                      outlined
                      dense
                      :rules="[rules.required]"
                    />
                    <v-text-field
                      v-model="data.phone"
                      label="Điện thoại"
                      outlined
                      dense
                      :rules="[rules.required]"
                    />
                    <v-text-field
                      v-model="data.address"
                      label="Địa chỉ"
                      outlined
                      dense
                      :rules="[rules.required]"
                    />
                    <v-autocomplete
                      v-model="data.province"
                      :items="provinces"
                      label="Tỉnh/Thành"
                      item-text="name"
                      item-value="id"
                      outlined
                      dense
                      :rules="[rules.required]"
                    />
                    <v-autocomplete
                      v-if="data.province"
                      v-model="data.district"
                      :items="districtList"
                      label="Quận/Huyện"
                      item-text="name"
                      item-value="id"
                      outlined
                      dense
                      :rules="[rules.required]"
                    />
                    <v-textarea
                      v-model="data.note"
                      label="Ghi chú (tuỳ chọn)"
                      outlined
                      dense
                    />
                    <v-btn
                      color="primary"
                      :disabled="!formValid"
                      @click="cartStep = 2"
                    >
                      Tiếp theo
                    </v-btn>
                  </v-form>
                </v-stepper-content>
                <v-stepper-content step="2">
                  <v-card
                    class="mb-12"
                    color="grey lighten-1"
                    height="200px"
                  />
                  <v-btn
                    color="primary"
                    @click="cartStep = 3"
                  >
                    Tiếp theo
                  </v-btn>
                </v-stepper-content>
                <v-stepper-content step="3">
                  <v-card
                    class="mb-12"
                    color="grey lighten-1"
                    height="200px"
                  />
                </v-stepper-content>
              </v-stepper-items>
            </v-stepper>
          </v-col>
          <v-col
            md="4"
            cols="12"
          >
            <h3 class="pa-4">
              Đơn hàng ({{ total }} sản phẩm)
            </h3>
            <v-divider />
            <v-list>
              <ClientOnly placeholder="Đang tải...">
                <CheckOutItem
                  v-for="item in cart"
                  :key="item.slug"
                  :title="item.title"
                  :product="item"
                />
              </ClientOnly>
              <v-divider />
              <v-row
                v-if="total>0"
                align="center"
                class="px-3 pt-2"
              >
                <v-col
                  cols="12"
                  class="pb-0 text-right"
                >
                  <v-responsive
                    class="ml-auto overflow-visible"
                  >
                    <v-text-field
                      v-model="coupon"
                      :disabled="is_loading"
                      :readonly="is_valid"
                      label="Mã giảm giá"
                      solo
                      flat
                      background-color="grey lighten-3"
                      @input="onCoupon"
                    >
                      <template #append>
                        <v-btn
                          v-if="is_visible && !is_valid"
                          depressed
                          color="primary"
                          class="mr-n2"
                          @click="verifyCoupon"
                        >
                          Áp dụng
                        </v-btn>
                        <v-btn
                          v-if="is_valid"
                          icon
                          small
                          @click="removeCoupon"
                        >
                          <v-icon small>
                            mdi-close
                          </v-icon>
                        </v-btn>
                      </template>
                    </v-text-field>
                  </v-responsive>
                </v-col>
                <v-col
                  cols="12"
                  class="text-right py-0 d-flex align-center"
                >
                  <span class="body-2 font-weight-light grey--text">
                    Tạm tính:
                  </span>
                  <v-spacer />
                  <v-responsive
                    min-width="100"
                    class="grey--text text--darken-1 font-weight-medium shrink d-inline-flex justify-end"
                    v-text="moneyFormat(amount)"
                  />
                </v-col>
                <v-expand-transition>
                  <v-col
                    v-if="is_valid"
                    cols="12"
                    class="text-right py-0 d-flex align-center"
                  >
                    <span class="body-2 font-weight-light grey--text">
                      Được giảm{{ is_percent ? `(${coupon_value}%)` : '' }}:
                    </span>
                    <v-spacer />
                    <v-responsive
                      min-width="100"
                      class="grey--text text--darken-1 font-weight-medium shrink d-inline-flex justify-end"
                      v-text="'-'+moneyFormat(discount)"
                    />
                  </v-col>
                </v-expand-transition>
                <v-col
                  cols="12"
                  class="text-right py-0 d-flex align-center"
                >
                  <span class="body-2 font-weight-light grey--text">
                    Phí ship:
                  </span>
                  <v-spacer />
                  <v-responsive
                    min-width="100"
                    class="grey--text text--darken-1 font-weight-medium shrink d-inline-flex justify-end"
                    v-text="shippingFee"
                  />
                </v-col>
                <v-col
                  cols="12"
                  class="text-right pt-0 d-flex align-center"
                >
                  <span class="body-2 font-weight-light grey--text">
                    Thành tiền:
                  </span>
                  <v-spacer />
                  <v-responsive
                    min-width="100"
                    class="red--text title text--darken-4 font-weight-medium shrink d-inline-flex justify-end"
                    v-text="moneyFormat(total_amount)"
                  />
                </v-col>
              </v-row>
            </v-list>
          </v-col>
        </v-layout>
      </v-container>
    </v-main>
  </v-app>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  layout: "empty",
  async asyncData({ app }) {
    let { provinces } = await app.$axios.$get("/provinces");
    return {
      provinces: provinces
    }
  },
  data () {
    return {
      cartStep: 1,
      is_visible: false,
      is_loading: false,
      coupon: "",
      is_valid: false,
      is_fee: false,
      formValid: false,
      data: {
        email: null,
        name: null,
        phone: null,
        province: null,
        district: null,
        address: null,
        note: null
      },
      rules: {
        required: v => !!v || 'Không được bỏ trống',
        email: value => {
          const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
          return pattern.test(value) || 'Địa chỉ email không hợp lệ'
        },
      },
    }
  },
  computed: {
    ...mapGetters('cart', ['cart', 'total', 'amount', 'discount', 'total_amount', 'is_percent', 'coupon_value']),
    shippingFee() {
      return this.is_fee ? this.moneyFormat(10000) : "Miễn phí"
    },
    districtList() {
      let t = this;
      let index = this.provinces.findIndex(p => p.id == t.data.province);
      return this.provinces[index].districts;
    }
  },
  methods: {
    onCoupon() {
      this.is_visible = this.coupon.length > 0 ? true : false
    },
    show() {
      this.cart_drawer = !this.cart_drawer
    },
    moneyFormat(number) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number)
    },
    async verifyCoupon() {
      this.is_loading = true;
      // let { coupon } = await this.$axios.$post("/checkcoupon", this.counpon);
      let couponA = {
        coupon: "SOMETHING",
        coupon_value: 20,
        is_percent: true
      };
      let couponB = {
        coupon: "SOMETHING",
        coupon_value: 200000,
        is_percent: false
      };
      this.$store.dispatch("cart/addCoupon", this.coupon == "a" ? couponA : couponB);
      this.is_valid = true;
      this.is_loading = false;
    },
    removeCoupon() {
      this.is_valid = false;
      this.coupon = "";
      this.is_visible = false;
      return this.$store.dispatch("cart/removeCoupon");
    }
  },
}
</script>
