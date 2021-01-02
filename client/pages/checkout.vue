<template>
  <v-app>
    <v-main>
      <v-container>
        <v-layout wrap>
          <v-col
            md="4"
            cols="12"
            order="1"
            order-md="0"
          >
            <v-card>
              <v-flex class="d-flex flex-wrap align-center">
                <v-card-title>Thông tin nhận hàng</v-card-title>
                <v-spacer />
                <div class="pa-4">
                  <span>Đã có tài khoản?</span>
                  <NuxtLink
                    :to="{ name: 'auth-login', query: { redirect: $route.fullPath } }"
                    class="pl-1"
                  >
                    Đăng nhập
                  </NuxtLink>
                </div>
              </v-flex>
              <v-card-text>
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
                    :rules="[rules.required, rules.phone]"
                  />
                  <v-text-field
                    v-model="data.address"
                    label="Địa chỉ"
                    outlined
                    dense
                    :rules="[rules.required]"
                  />
                  <v-autocomplete
                    v-model="data.province_id"
                    :items="provinces"
                    label="Tỉnh/Thành"
                    item-text="name"
                    item-value="id"
                    outlined
                    dense
                    :rules="[rules.required]"
                  />
                  <v-autocomplete
                    v-if="data.province_id"
                    v-model="data.district_id"
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
              </v-card-text>
            </v-card>
          </v-col>
          <v-col
            md="4"
            cols="12"
            order="2"
            order-md="1"
          >
            <div class="pt-4">
              <span class="text-h5">Thanh toán</span>
              <v-radio-group
                v-model="data.payment_type"
                mandatory
              >
                <v-sheet
                  class="pa-4 elevation-3"
                  style="border-radius: 4px 4px 0 0"
                >
                  <v-radio
                    label="Thanh toán khi giao hàng (COD)"
                    value="cod"
                  />
                  <v-expand-transition>
                    <div v-if="data.payment_type=='cod'">
                      <p>Bạn sẽ nhận hàng và thanh toán với nhân viên giao hàng.</p>
                      <p class="mb-0">
                        Nếu có dấu hiệu bất thường xin vui lòng thông báo qua hotline 0123 456 789.
                      </p>
                    </div>
                  </v-expand-transition>
                </v-sheet>
                <v-sheet class="pa-4 elevation-3">
                  <v-radio
                    label="Chuyển khoản"
                    value="banking"
                  />
                  <v-expand-transition>
                    <div v-if="data.payment_type=='banking'">
                      <p>Lưu ý: Quý khách vui lòng đợi nhân viên liên hệ xác nhận đơn hàng trước khi thực hiện thanh toán.</p>
                      <p>Công Ty TNHH VietShop:</p>
                      <p>BIDV</p>
                      <p>Số tài khoản : 36210000123456</p>
                      <p class="mb-0">
                        Ngân hàng TMCP Đầu tư và Phát triển Việt Nam – Hà Nội
                      </p>
                    </div>
                  </v-expand-transition>
                </v-sheet>
                <v-sheet
                  class="pa-4 elevation-3"
                  style="border-radius: 0 0 4px 4px"
                >
                  <v-radio
                    label="Thanh toán qua VNPay"
                    value="vnpay"
                  />
                  <v-expand-transition>
                    <div v-if="data.payment_type=='vnpay'">
                      <p class="mb-0">
                        Thực hiện thanh toá nhanh qua cổng thanh toán VNPay
                      </p>
                    </div>
                  </v-expand-transition>
                </v-sheet>
              </v-radio-group>
            </div>
          </v-col>
          <v-col
            md="4"
            cols="12"
            order="0"
            order-md="2"
          >
            <v-card>
              <v-card-text>
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
                          v-model="data.coupon"
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
                        v-text="$moneyFormat(amount)"
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
                          v-text="'-'+$moneyFormat(discount)"
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
                        v-text="$moneyFormat(total_amount)"
                      />
                    </v-col>
                  </v-row>
                </v-list>
              </v-card-text>
            </v-card>
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
      is_valid: false,
      is_fee: false,
      formValid: false,
      data: {
        email: null,
        name: null,
        phone: null,
        district_id: null,
        province_id: null,
        address: null,
        note: null,
        payment_type: null,
        coupon: "",
      },
      rules: {
        required: v => !!v || 'Không được bỏ trống',
        email: v => {
          const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
          return pattern.test(v) || 'Địa chỉ email không hợp lệ'
        },
        phone: v => !isNaN(v) || 'Số điện thoại không hợp lệ'
      },
    }
  },
  computed: {
    ...mapGetters('cart', ['cart', 'total', 'amount', 'discount', 'total_amount', 'is_percent', 'coupon_value']),
    shippingFee() {
      return this.is_fee ? this.$moneyFormat(10000) : "Miễn phí"
    },
    districtList() {
      let t = this;
      let index = this.provinces.findIndex(p => p.id == t.data.province_id);
      return this.provinces[index].districts;
    }
  },
  methods: {
    onCoupon() {
      this.is_visible = this.data.coupon.length > 0 ? true : false
    },
    show() {
      this.cart_drawer = !this.cart_drawer
    },
    async verifyCoupon() {
      this.is_loading = true;
      // let { coupon } = await this.$axios.$post("/checkcoupon", this.data.counpon);
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
      this.$store.dispatch("cart/addCoupon", this.data.coupon == "a" ? couponA : couponB);
      this.is_valid = true;
      this.is_loading = false;
    },
    removeCoupon() {
      this.is_valid = false;
      this.data.coupon = "";
      this.is_visible = false;
      return this.$store.dispatch("cart/removeCoupon");
    },
    saveOrder() {
      let data = this.data;
      let product = {};
      Object.keys(this.cart).map(i => product[i] = this.cart[i].count);
      data.product = product;
      this.$axios.post("/checkout", data)
      .then(res => {
        if(res.data.payment_url) {
          window.location = res.data.payment_url;
        }
        this.$sotre.dispatch("cart/clearCart");
      });
    }
  },
}
</script>
