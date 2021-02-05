<template>
  <v-card-text>
    <v-form
      ref="form"
      v-model="valid"
    >
      <template v-if="!$auth.loggedIn && (!name || edit_info)">
        <v-text-field
          v-model="info.name"
          label="Họ tên"
          :rules="[v => !!v || 'Không được bỏ trống']"
        />
        <v-text-field
          v-model="info.phone"
          label="Số điện thoại"
        />
        <v-text-field
          v-model="info.email"
          label="Email liên hệ"
          :rules="[v => !!v || 'Không được bỏ trống']"
        />
      </template>
      <span v-if="!$auth.loggedIn && name && !edit_info">Bình luận dưới tư cách <span class="primary--text">{{ name }}</span>(<span
        class="pointer"
        @click="edit_info=true"
      >Thay đổi</span>)</span>
      <v-textarea
        v-model="content"
        name="comment"
        placeholder="Xin mời để lại câu hỏi"
        type="text"
        outlined
        autocomplete="off"
        autocorrect="off"
        autocapitalize="off"
        spellcheck="false"
        :rules="[
          v => !!v || 'Không được bỏ trống',
          v => v.length >= 10 || 'Tối thiểu 10 ký tự',
          v => v.length <= 2000 || 'Tối đa 2000 ký tự'
        ]"
        counter
        @focus="onFocus()"
      />
    </v-form>
    <transition name="slide-x-reverse-transition">
      <div
        v-if="is_commenting"
        class="d-flex flex"
      >
        <v-spacer />
        <v-btn
          text
          @click="unfocus()"
        >
          Huỷ
        </v-btn>
        <v-btn
          class="action_btn"
          color="primary"
          :disabled="!valid"
          @click="submit()"
        >
          <span>Bình Luận</span>
        </v-btn>
      </div>
    </transition>
  </v-card-text>
</template>
<script>
import { mapGetters } from "vuex";
export default{
  name: 'CommentForm',
  props: {
    productId: {
      type: Number,
      default: null
    },
    parent: {
      type: Number,
      default: null
    }
  },
  data(){
    return{
      content: '',
      info: {
        name: '',
        phone: '',
        email: ''
      },
      is_commenting: false,
      valid: false,
      edit_info: false
    }
  },
  computed: {
    ...mapGetters('customer', ['name', 'phone', 'email']),
    requireValidation(v) {
      return this.$auth.loggedIn
        ? true
        : !!v || 'Không được bỏ trống';
    }
  },
  created() {
    this.info = {
      name: this.name,
      phone: this.phone,
      email: this.email
    }
  },
  methods: {
    async submit() {
      let config = {
        content: this.content,
        name: this.info.name,
        phone: this.info.phone,
        email: this.info.email,
        product_id: this.productId,
        parent_id: this.parent,
      }
      // console.log(config);
      try {
        if (!this.name || this.edit_info) {
          this.$store.dispatch('customer/setInfo', this.info);
          this.edit_info = false;
        }
        let { comment } = await this.$axios.$post('/comments', config);
        let total = this.$store.getters['comment/total'];
        this.$store.dispatch('comment/addOne', comment);
        this.$store.dispatch('comment/setTotal', ++total);
        this.unfocus();
      } catch(err) {
        console.error('add comment', err)
      }
    },
    onFocus: function() {
      this.is_commenting = true
    },
    unfocus: function() {
      this.content = "";
      this.is_commenting = false;
      this.$refs.form.resetValidation()
      this.$emit("cancel")
    },
    commentValidation: function() {
      var t = this.content;
      return 0 == t.length || (t.length < 10 ? "Tối thiểu 10 ký tự." : !(t.length > 2000) || "Bình luận quá dài. Tối đa 2000 ký tự.")
    }
  }
}
</script>
