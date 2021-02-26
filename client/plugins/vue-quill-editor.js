import Vue from "vue";
import VueQuillEditor, { Quill } from "vue-quill-editor";
import ImageResize from "quill-image-resize-module";
import { ImageDrop } from "quill-image-drop-module";
import ImageUploader from "quill-image-uploader";

import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";

Quill.register("modules/imageResize", ImageResize);
Quill.register("modules/imageDrop", ImageDrop);
Quill.register("modules/imageUploader", ImageUploader);
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
    ],
    imageUploader: {
      upload: file => {
        return new Promise((resolve, reject) => {
          const formData = new FormData();
          formData.append("image", file);

          fetch(
            "https://api.imgbb.com/1/upload?key=3fc620748fdf8882c4f8efe20d975efb",
            {
              method: "POST",
              body: formData
            }
          )
            .then(response => response.json())
            .then(result => {
              console.log(result);
              resolve(result.data.url);
            })
            .catch(error => {
              reject("Upload failed");
              console.error("Error:", error);
            });
        });
      }
    }
  },
  placeholder: "Điền nội dung vào đây ..."
});
