<template>
  <div class="text-editor">
    <button
      :class="{
        'text-editor__emoji-button': true,
        'text-editor__emoji-button--active': showEmojiPopup,
      }"
      @click.prevent="showEmojiPopup = !showEmojiPopup"
    >
      😀
    </button>

    <transition name="scale">
      <div class="text-editor__emoji-container" v-if="showEmojiPopup">
        <v-emoji-picker @select="selectEmoji" :emojiWithBorder="false" />
      </div>
    </transition>

    <transition name="scale">
      <div class="text-editor__color-container" v-if="showColorPopup">
        <chrome v-model="colors" />
      </div>
    </transition>

    <VueTrix
      :name="name"
      :placeholder="placeholder"
      v-model="content"
      ref="trix"
      @trix-initialize="trixInit"
    />
  </div>
</template>

<script>
import VueTrix from "vue-trix";
// import Trix from "trix";
import { VEmojiPicker } from "v-emoji-picker";
import { Chrome } from "vue-color";

export default {
  name: "TextEditor",
  components: {
    VueTrix,
    VEmojiPicker,
    Chrome,
  },
  model: {
    prop: "value",
  },
  props: {
    name: {
      type: String,
      default: "",
    },
    placeholder: {
      type: String,
      default: "",
    },
    value: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      content: this.value,
      showEmojiPopup: false,
      showColorPopup: false,
      colors: "#00000",
    };
  },
  watch: {
    value(newValue) {
      this.content = newValue;
    },
    content(newValue) {
      this.$emit("input", newValue);
    },
    colors: {
      deep: true,
      handler(newValue) {
        this.$el.querySelector(
          ".trix-button-group--text-tools .trix-button--icon-palette"
        ).style.backgroundColor = newValue.hex;
        // Trix.config.textAttributes.colored = {
        //   style: { color: newValue.hex },
        //   parser(element) {
        //     return element.style.color === newValue.hex;
        //   },
        //   inheritable: true,
        // };
      },
    },
  },
  computed: {
    elm() {
      return this.$refs.trix.$el.querySelector("[contenteditable]");
    },
  },
  mounted() {
    // Trix.config.textAttributes.underline = {
    //   tagName: "u",
    //   inheritable: true,
    // };
    // Trix.config.textAttributes.colored = {
    //   style: { color: this.colors },
    //   parser(element) {
    //     return element.style.color === this.colors;
    //   },
    //   inheritable: true,
    // };
  },
  methods: {
    trixInit() {
      const buttonHTML = `
              <button title="Underline" type="button" class="trix-button trix-button--icon trix-button--icon-underline" data-trix-key="u" data-trix-attribute="underline" tabindex="-1">Underline</button>
              <button title="Choose color" type="button" class="trix-button trix-button--icon trix-button--icon-palette" tabindex="-1">Choose color</button>
              <button title="Text color" type="button" class="trix-button trix-button--icon trix-button--icon-colored" data-trix-attribute="colored" tabindex="-1">Text color</button>
            `;
      this.$el
        .querySelector(".trix-button-group--text-tools")
        .insertAdjacentHTML("beforeend", buttonHTML);
      this.$el
        .querySelector(
          ".trix-button-group--text-tools .trix-button--icon-palette"
        )
        .addEventListener("click", () => {
          this.showColorPopup = !this.showColorPopup;
        });
    },
    selectEmoji(emoji) {
      this.elm.focus();
      this.pasteHtmlAtCaret(`<b>${emoji.data}</b>`);
      this.showEmojiPopup = false;
    },
    pasteHtmlAtCaret(html) {
      let sel, range;
      if (window.getSelection) {
        // IE9 and non-IE
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
          range = sel.getRangeAt(0);
          range.deleteContents();

          // Range.createContextualFragment() would be useful here but is
          // non-standard and not supported in all browsers (IE9, for one)
          const el = document.createElement("div");
          el.innerHTML = html;
          let frag = document.createDocumentFragment(),
            node,
            lastNode;
          while ((node = el.firstChild)) {
            lastNode = frag.appendChild(node);
          }
          range.insertNode(frag);

          // Preserve the selection
          if (lastNode) {
            range = range.cloneRange();
            range.setStartAfter(lastNode);
            range.collapse(true);
            sel.removeAllRanges();
            sel.addRange(range);
          }
        }
      } else if (document.selection && document.selection.type != "Control") {
        // IE < 9
        document.selection.createRange().pasteHTML(html);
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./TextEditor.scss";
</style>
