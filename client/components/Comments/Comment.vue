<template>
  <div>
    <v-divider v-if="!comment.replies" />
    <v-card-title>
      <v-avatar
        size="36"
        color="indigo"
      >
        <span class="white--text">{{ name.slice(0,1) }}</span>
      </v-avatar>
      <span class="ml-2">{{ name }}</span>
    </v-card-title>
    <v-card-text class="pb-0">
      <span>{{ comment.content }}</span>
    </v-card-text>
    <v-card-actions>
      <v-btn
        text
        small
        color="primary"
        @click="replyForm = true"
      >
        <v-icon left>
          mdi-reply
        </v-icon>
        <span>Trả lời</span>
      </v-btn>
      <v-divider vertical />
      <span class="pl-2 text-caption">{{ cal_time_ago(comment.created_at) }}</span>
    </v-card-actions>
    <CommentForm
      v-if="replyForm"
      :product-id="productId"
      :parent="comment.id"
      @cancel="replyForm = false"
    />
    <Comment
      v-for="child in comment.replies"
      :key="child.id"
      class="comment-reply"
      :comment="child"
      :product-id="productId"
      :parent="comment.id"
    />
    <v-divider v-if="comment.replies" />
  </div>
</template>
<script>
  export default{
    name: 'Comment',
    props: {
      productId: {
        type: Number,
        default: null
      },
      comment: {
        type: Object,
        default: null
      },
      depth: {
        type: Number,
        default: null
      },
      parent: {
        type: Number,
        default: null
      }
    },
    data(){
      return {
        replyToComment: false,
        child_comments: [],
        offset: 0,
        page_size: 12,
        is_replies_visible: false,
        is_loading: false,
        replyForm: false,
        childForm: false
      }
    },
    computed: {
      user() {
        return this.$auth.user
      },
      name() {
        return this.comment.user ? this.comment.user.name : this.comment.name
      },
      time_ago: function() {
        let edited = this.comment.created_at != this.comment.updated_at ? ` (đã sửa)` : "";
        return this.cal_time_ago + edited
      },
    },
    methods: {
      toggleShowHideReplies: function() {
        this.is_replies_visible ? (this.offset = -1 * this.page_size, this.child_comments=[]) : this.showMore();
        this.is_replies_visible = !this.is_replies_visible
      },
      showMore: function() {
        let t = this;
        this.$axios.get("/replies/"+this.comment.id).then((res) => {
          t.child_comments = res.data.comments;
          t.total = res.data.total;
        })
      },
      timeFormat: function (time) {
        let hour = time.getHours(),
          minute = time.getMinutes(),
          second = time.getSeconds(),
          day = time.getDate(),
          month = time.getMonth()+1,
          year = time.getFullYear()
        return `${year}-${month}-${day} ${hour}:${minute}:${second}`
      },
      cal_time_ago: function(time) {
        var timeDiff = new Date() - new Date(time);
        if (timeDiff <= 60*1000)
          return "vài giây trước";
        if (timeDiff <= 60*60*1000) {
          var min = parseInt(timeDiff / (60*1000));
          return min < 2 ? "một phút trước" : "".concat(min, " phút trước")
        }
        if (timeDiff <= 60*60*24*1000) {
          var hour = parseInt(timeDiff / (60*60*1000));
          return hour < 2 ? "một giờ trước" : "".concat(hour, " giờ trước")
        }
        if (timeDiff <= 60*60*24*7*1000) {
          var day = parseInt(timeDiff / (60*60*24*1000));
          return day < 2 ? "hôm qua" : "".concat(day, " ngày trước")
        }
        if (timeDiff <= 60*60*24*30*1000) {
          var week = parseInt(timeDiff / (60*60*24*7*1000));
          return week < 2 ? "tuần trước" : "".concat(week, " tuần trước")
        }
        timeDiff = parseInt(timeDiff / (60*60*24*30*1000));
        return "".concat(timeDiff, " tháng trước")
      }
    }
  }
</script>
<style scoped>
.v-btn--icon {
    margin: 6px 16px !important;
}
.v-icon {
    font-size: 16px !important;
}
.primary-color {
    color: #4caf50;
}
.comment-reply {
  padding-left: 40px;
}
@media (min-width: 600px) {
  .comment-reply {
    padding-left: 60px;
  }
}
</style>
