<template>
  <v-card class="mt-2">
    <v-card-title>Hỏi và đáp ({{ total }} Bình luận):</v-card-title>
    <CommentForm :product-id="productId" />
    <v-divider />
    <v-card-text>
      <span v-if="total<=0">Để lại câu hỏi đầu tiên</span>
      <Comment
        v-for="comment in comments"
        :key="comment.id"
        :comment="comment"
        :product-id="productId"
      />
      <v-btn
        v-if="loading || (total > (data.page_size + data.offset))"
        color="primary"
        class="mt-4"
        outlined
        block
        :loading="loading"
        @click="showMore"
      >
        <span>Tải thêm bình luận</span>
      </v-btn>
    </v-card-text>
  </v-card>
</template>
<script>
  import { mapGetters } from "vuex";
  export default{
    name: 'Comments',
    props: {
      productId: {
        type: Number,
        default: null
      },
      order: {
        type: String,
        default: null
      },
    },
    data() {
      return {
        loading: false,
        data: {
          offset: 0,
          page_size: 12,
        }
      }
    },
    computed: {
      ...mapGetters('comment', [
        'comments',
        'total'
      ])
    },
    methods: {
      showMore() {
        this.data.offset += this.data.page_size;
        this.loadComments();
      },
      async loadComments() {
        this.loading = true;
        let config = {
          params: this.data
        }
        try {
          let { comments, total } = await this.$axios.$get(`/comments/${this.productId}`, config);
          this.$store.dispatch('comment/addMultiple', comments);
          this.$store.dispatch('comment/setTotal', total);
        } catch(err) {
          this.$notifier.showMessage({
            content: 'Có lỗi, vui lòng thử lại',
            color: 'error',
            right: false
          })
        }
        this.loading = false;
      },
    },
  }
</script>
