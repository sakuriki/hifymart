<template>
  <v-container fluid>
    <v-card>
      <v-card-title>
        Danh sách nhãn hiệu
        <v-spacer />
        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          label="Tìm kiếm"
          single-line
          hide-details
        />
      </v-card-title>
      <v-data-table
        v-model="selected"
        :headers="headers"
        :items="data"
        :options.sync="options"
        :server-items-length="pagination.total"
        :loading="loading"
        show-select
        class="elevation-1"
        :footer-props="{
          itemsPerPageOptions: [10, 20, 30, 50],
          showFirstLastPage: true,
        }"
      >
        <template #[`item.products_count`]="{ item }">
          <span><v-chip color="primary">{{ item.products_count }}</v-chip></span>
        </template>
        <template #[`item.actions`]="{ item }">
          <v-btn
            v-if="canUpdate"
            color="success"
            icon
            :to="{ name: 'admin-brands-id',params: { id: item.id }}"
          >
            <v-icon>mdi-pencil-outline</v-icon>
          </v-btn>
          <v-btn
            v-if="canDelete"
            color="error"
            icon
            @click="beforeDelete"
          >
            <v-icon>mdi-delete-outline</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
  </v-container>
</template>
<script>
export default {
  layout: "admin",
  middleware: "auth",
  meta: {
    auth: {
      permission: "brand.access"
    }
  },
  data () {
    return {
      pagination: {
        total: 0,
        count: 0,
        per_page: 20
      },
      search: "",
      data: [],
      loading: false,
      options: {},
      selected: [],
      headers: [
        { text: 'Tên', align: 'start', value: 'name' },
        { text: 'Giới thiệu', value: 'description' },
        { text: 'Số sản phẩm', value: 'products_count' },
        { text: 'Actions', value: 'actions', sortable: false }
      ],
    }
  },
  computed: {
    canUpdate() {
      return this.$auth.user.permissions.includes("brand.update")
    },
    canDelete() {
      return this.$auth.user.permissions.includes("brand.delete")
    }
  },
  watch: {
    options: {
      handler () {
        this.fetchData()
      },
      deep: true,
    },
    search() {
      if(this.loading == true) return;
      this.fetchData()
    },
  },
  mounted () {
    this.fetchData = this.$debounce(this.fetchData, 500);
  },
  methods: {
    async fetchData() {
      this.loading = true;
      let config = {
        params: {
          q: this.search,
          page: this.options.page,
          per_page: this.options.itemsPerPage,
          sortBy: this.options.sortBy[0],
          sortDesc: this.options.sortDesc[0]
        }
      }
      try {
        let { brands, pagination } = await this.$axios.$get("/admin/brands", config);
        this.data = brands;
        this.pagination = pagination;
        this.loading = false;
        this.$vuetify.goTo(0)
      } catch(err) {
        console.error('loi fetch: ', err);
      }
    },
    beforeDelete: function(item) {
      console.log("xác nhận xoá", item)
    }
  },
}
</script>
