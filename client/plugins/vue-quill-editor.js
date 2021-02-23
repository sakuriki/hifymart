import Vue from "vue";
import VueQuillEditor, { Quill } from "vue-quill-editor";
import ImageResize from "quill-image-resize-module";
import { ImageDrop } from "quill-image-drop-module";

import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";

Quill.register("modules/imageResize", ImageResize);
Quill.register("modules/imageDrop", ImageDrop);
Vue.use(VueQuillEditor, {
  theme: "snow",
  modules: {
    imageResize: {
      modules: ["Resize", "DisplaySize", "Toolbar"]
    },
    toolbar: [
      ["bold", "italic", "underline", "strike"],
      ["blockquote", "code-block"],
      [{ header: 1 }, { header: 2 }],
      [{ list: "ordered" }, { list: "bullet" }],
      [{ script: "sub" }, { script: "super" }],
      [{ indent: "-1" }, { indent: "+1" }],
      [{ direction: "rtl" }],
      [{ size: ["small", false, "large", "huge"] }],
      [{ header: [1, 2, 3, 4, 5, 6, false] }],
      [{ color: [] }, { background: [] }],
      [{ font: [] }],
      [{ align: [] }],
      ["clean"],
      ["link", "image", "video"]
    ]
  },
  placeholder: "Điền nội dung vào đây ..."
});
