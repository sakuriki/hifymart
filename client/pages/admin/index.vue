<template>
  <v-container>
    <v-row>
      <v-col
        cols="12"
        md="4"
      >
        <v-card>
          <div class="d-flex flex-no-wrap justify-space-between">
            <div clas="d-flex align-center">
              <v-card-text>
                <p>Tổng số sản phẩm</p>
                <p class="text-h6 font-weight-bold">
                  {{ total_count.products }}
                </p>
                <NuxtLink
                  :to="{ name: 'admin-products' }"
                  class="primary--text"
                >
                  <span>Xem thêm</span>
                  <v-icon
                    color="primary"
                    small
                  >
                    mdi-chevron-right-circle-outline
                  </v-icon>
                </NuxtLink>
              </v-card-text>
            </div>
            <v-avatar
              class="ma-3"
              size="94"
              tile
              color="info"
            >
              <v-icon dark>
                mdi-tag-multiple
              </v-icon>
            </v-avatar>
          </div>
        </v-card>
      </v-col>
      <v-col
        cols="12"
        md="4"
      >
        <v-card>
          <div class="d-flex flex-no-wrap justify-space-between">
            <div clas="d-flex align-center">
              <v-card-text>
                <p>Tổng số đơn hàng</p>
                <p class="text-h6 font-weight-bold">
                  {{ total_count.orders }}
                </p>
                <NuxtLink
                  :to="{ name: 'admin-orders' }"
                  class="primary--text"
                >
                  <span>Xem thêm</span>
                  <v-icon
                    color="primary"
                    small
                  >
                    mdi-chevron-right-circle-outline
                  </v-icon>
                </NuxtLink>
              </v-card-text>
            </div>
            <v-avatar
              class="ma-3"
              size="94"
              tile
              color="success"
            >
              <v-icon dark>
                mdi-cart
              </v-icon>
            </v-avatar>
          </div>
        </v-card>
      </v-col>
      <v-col
        cols="12"
        md="4"
      >
        <v-card>
          <div class="d-flex flex-no-wrap justify-space-between">
            <div clas="d-flex align-center">
              <v-card-text>
                <p>Tổng số người dùng</p>
                <p class="text-h6 font-weight-bold">
                  {{ total_count.users }}
                </p>
                <NuxtLink
                  :to="{ name: 'admin-users' }"
                  class="primary--text"
                >
                  <span>Xem thêm</span>
                  <v-icon
                    color="primary"
                    small
                  >
                    mdi-chevron-right-circle-outline
                  </v-icon>
                </NuxtLink>
              </v-card-text>
            </div>
            <v-avatar
              class="ma-3"
              size="94"
              tile
              color="warning"
            >
              <v-icon dark>
                mdi-account-multiple
              </v-icon>
            </v-avatar>
          </div>
        </v-card>
      </v-col>
    </v-row>
    <v-card class="mt-2">
      <v-card-text>
        <Chart
          :name="'30 ngày qua'"
          :data="last_month"
          :xaxis-type="'datetime'"
        />
      </v-card-text>
    </v-card>
    <v-card class="mt-2">
      <v-card-text>
        <Chart
          :name="'Năm qua'"
          :data="last_year"
        />
      </v-card-text>
    </v-card>
  </v-container>
</template>
<script>
export default {
  layout: "admin",
  middleware: "auth",
  async asyncData({ app }) {
    let { total_count, last_month, last_year } = await app.$axios.$get('/admin/dashboard');
    return {
      total_count: total_count,
      last_month: last_month,
      last_year: last_year
    }
  },
}
</script>
<style scoped>
p {
  margin: unset!important
}
p.primary--text {
  cursor: pointer;
}
</style>
