<template>
  <v-navigation-drawer
    v-model="cart_drawer"
    app
    right
    fixed
    temporary
    hide-overlay
    width="340"
  >
    <simplebar
      style="height: 100%"
    >
      <div class="d-flex align-center justify-space-between py-4 px-4">
        <span class="text-h6 black--text">
          Giỏ hàng
        </span>
        <v-btn
          fab
          small
          depressed
          @click="cart_drawer = false"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </div>

      <v-divider />

      <v-list two-line>
        <ClientOnly placeholder="Đang tải...">
          <CartItem
            v-for="item in cart"
            :key="item.slug"
            :title="item.title"
            :product="item"
          />
          <div
            v-if="total<=0"
            class="ma-4"
          >
            <v-list-item class="mb-4">
              <v-list-item-title class="text-center body-2">
                Giỏ hàng trống
              </v-list-item-title>
            </v-list-item>
            <v-btn
              block
              color="accent"
              @click="cart_drawer = false"
            >
              Tiếp tục shopping
            </v-btn>
          </div>
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
                max-width="230"
              >
                <v-text-field
                  v-model="coupon"
                  :disabled="is_loading"
                  :readonly="is_valid"
                  label="Coupon giảm giá"
                  dense
                  solo
                  flat
                  background-color="grey lighten-3"
                  @input="onCoupon"
                >
                  <template #append>
                    <v-btn
                      v-if="is_visible && !is_valid"
                      depressed
                      small
                      color="primary"
                      class="mr-n4"
                      @click="verifyCoupon"
                    >
                      Thêm
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
            <v-expand-transition>
              <v-col
                v-if="is_valid"
                cols="12"
                class="text-right py-0 d-flex align-center justify-end flex-wrap"
              >
                <span class="body-2 font-weight-light grey--text">
                  Tổng sản phẩm:
                </span>
                <v-responsive
                  min-width="100"
                  class="grey--text text--darken-1 font-weight-medium shrink d-inline-flex justify-end"
                  v-text="moneyFormat(amount)"
                />
                <v-col
                  cols="12"
                  class="pa-0"
                />
                <span class="body-2 font-weight-light grey--text">
                  Được giảm{{ is_percent && `(${coupon_value}%)` }}:
                </span>
                <v-responsive
                  min-width="100"
                  class="grey--text text--darken-1 font-weight-medium shrink d-inline-flex justify-end"
                  v-text="'-'+moneyFormat(discount)"
                />
              </v-col>
            </v-expand-transition>
            <v-col
              cols="12"
              class="text-right pt-0 d-flex align-center justify-end"
            >
              <span class="body-2 font-weight-light grey--text">
                Tạm tính({{ total }} sản phẩm):
              </span>
              <v-responsive
                min-width="100"
                class="red--text title text--darken-4 font-weight-medium shrink d-inline-flex justify-end"
                v-text="moneyFormat(total_amount)"
              />
            </v-col>
            <v-col
              cols="12"
            >
              <v-btn
                :disabled="is_loading"
                :loading="is_loading"
                block
                color="primary"
                large
              >
                Thanh toán
              </v-btn>
            </v-col>
          </v-row>
        </ClientOnly>
      </v-list>
    </simplebar>
  </v-navigation-drawer>
</template>
<script>
import simplebar from "simplebar-vue";
import "simplebar/dist/simplebar.min.css";
import { mapGetters } from "vuex";
export default {
  components: {
    simplebar,
  },
  data() {
    return {
      cart_drawer: null,
      is_visible: false,
      is_loading: false,
      coupon: "",
      is_valid: false,
    }
  },
  computed: {
    ...mapGetters('cart', ['cart', 'total', 'amount', 'discount', 'total_amount', 'is_percent', 'coupon_value'])
  },
  mounted: function() {
    this.$nuxt.$on("open-side-cart", this.show)
  },
  beforeDestroy: function() {
    this.$nuxt.$off("open-side-cart")
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
      let coupon = {
        coupon: "SOMETHING",
        coupon_value: 20,
        is_percent: true
      };
      this.$store.dispatch("cart/addCoupon", coupon);
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
