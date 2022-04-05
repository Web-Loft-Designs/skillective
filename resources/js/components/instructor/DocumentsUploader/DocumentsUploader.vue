<template>
  <div :class="{
    'documents-uploader': true,
    'documents-uploader--cannot-upload': cannotUpload,
  }">
    <input
      :disabled="cannotUpload"
      type="file"
      class="documents-uploader__input"
      @change="documentChanged($event)"
      ref="uploadDocumentInput"
      accept="application/pdf, image/png, image/jpeg"
      id="upload-document-input"
      name="upload-document-input"
    />
    <ul class="documents-uploader__list">
      <li 
        v-for="(doc, index) in documents" 
        :key="index" 
        :class="{
          'documents-uploader__doc': true,
          'documents-uploader__doc--empty': doc == null,
        }"
        :title="fileNameFromUrl(doc)"
      >
        <a target="_blank" :href="doc" v-if="doc">
          <img :src="doc" alt="Document preview" v-if="!fileExtFromUrl(doc)" />
          <span v-else>{{ fileExtFromUrl(doc) }}</span>
        </a>
        <button
          v-if="doc" 
          class="documents-uploader__remove" 
          @click="removeDocument(index)"
        >Remove</button>
      </li>
    </ul>
  </div>
</template>

<script>
import urlHelper from "../../../helpers/urlHelper";

export default {
  name: "DocumentsUploader",
  props: {
    documents: {
      type: Array,
      default: () => {
        return [ null, null, null, null, null ];
      },
    }
  },
  methods: {
    documentChanged(event) {
      const fileName = event.target.files.length > 0 && event.target.files[0].name;
      if (fileName) {
        this.previewFileName = fileName;
      }
      
      let index = -1;
      for (let i = 0; i < this.documents.length; i++) {
        if (this.documents[i] == null) {
          index = i;
          break;
        }
      }

      this.$emit("document-uploaded", {
        file: event.target.files[0],
        index,
      });
    },
    removeDocument(index) {
      this.documents.splice(index, 1);
      this.documents.push(null);
    },
    fileNameFromUrl(url) {
      return url ? urlHelper.fileNameFromUrl(url) : "Empty slot";
    },
    fileExtFromUrl(url) {
      if (url) {
        const ext = urlHelper.fileExtFromUrl(url);
        if (ext == "PNG" || ext == "JPG" || ext == "JPEG") {
          return null;
        }
        return ext;
      }
      return null;
    },
  },
  computed: {
    cannotUpload() {
      return this.documents[this.documents.length - 1] != null;
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./DocumentsUploader.scss";
</style>