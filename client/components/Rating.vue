<template>
  <div>
    <div
      v-for="rating in ratings"
      :key="rating.id"
    >
      <v-card-text>
        <div class="d-flex">
          <b>{{ rating.user.name }}</b>
          <span
            v-if="rating.is_purchased"
            class="d-flex primary--text"
          >
            <v-icon
              small
              color="primary"
            >mdi-check-circle</v-icon>
            Đã mua
          </span>
          <span class="ml-1">{{ cal_time_ago(rating.created_at) }}</span>
        </div>
        <div>
          <v-rating
            :value="rating.rating"
            color="amber"
            class="user-rating d-inline"
            readonly
            small
          />
          {{ rating.review }}
        </div>
      </v-card-text>
      <v-divider />
    </div>
  </div>
</template>
<script>
export default {
  props: {
    ratings: {
      type: Array,
      default: null
    }
  },
  methods: {
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
  },
}
</script>
