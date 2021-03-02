<template>
  <v-app>
    <v-main>
      <v-container
        fill-height
        justify-center
      >
        <div class="justify-center align-center d-flex flex-column">
          <h1 v-if="error.statusCode === 404">
            {{ pageNotFound }}
          </h1>
          <h1 v-else>
            {{ otherError }}
          </h1>
          <NuxtLink
            to="/"
            class="text-decoration-none"
          >
            Về trang chủ
          </NuxtLink>
        </div>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
  export default {
    layout: 'empty',
    props: {
      error: {
        type: Object,
        default: null,
      },
    },
    data() {
      return {
        pageNotFound: '404 Not Found',
        otherError: 'An error occurred',
      }
    },
    head() {
      const title =
      this.error.statusCode === 404 ? this.pageNotFound : this.otherError
      return {
        title,
      }
    },
  }
</script>

<style scoped>
  h1 {
  font-size: 20px;
  }
</style>
