<template>
  <div class='add-lesson-popup'>
    <transition name='show'>
      <div
        v-if='opened'
        class='add-lesson-popup__backdrop'
        @click.prevent='closePopup'
      ></div>
    </transition>

    <transition name='scalein'>
      <div v-if='opened' class='add-lesson-popup__popup-container'>
        <div v-scroll-lock='opened' class='add-lesson-popup__popup' @click.stop>
          <close-button
            class='add-lesson-popup__close'
            @click='opened = false'
          />
          <div class='add-lesson-popup__content'>
            <h3 class='add-lesson-popup__heading'>
              <span v-if='editMode'>Edit Lesson</span>
              <span v-else>Add Lesson</span>
            </h3>

            <span class='add-lesson-popup__row-title'>Lesson Title</span>
            <input
              ref='titleInput'
              v-model='fields.title'
              class='add-lesson-popup__input-text'
              placeholder='Enter Lesson Title'
              type='text'
            />
            <field-errors v-model='errors.title'/>

            <span class='add-lesson-popup__row-title'>Genre</span>
            <select
              v-model='fields.genre'
              class='add-lesson-popup__input-text'
              name='genre'
            >
              <option value></option>
              <option
                v-for='(genre, key) in formGenres'
                :key='key'
                :value='genre.id'
              >
                {{ genre.title }}
              </option>
            </select>
            <field-errors v-model='errors.genre'/>

            <span class='add-lesson-popup__row-title'>Lesson video</span>
            <div
              :class="{
                'add-lesson-popup__upload': true,
                'add-lesson-popup__upload--uploaded': videoFileName,
              }"
            >
              <input
                id='upload-video-input'
                ref='uploadVideoInput'
                accept='video/mp4'
                name='upload-video-input'
                type='file'
                @change='videoFileChanged($event)'
              />
              <label for='upload-video-input'>Upload a file</label>
              <div class='add-lesson-popup__upload-image'>
                <img
                  ref='uploadVideoImage'
                  alt='Upload Lesson file'
                  src='/images/upload-cloud.svg'
                />
                <span v-if='videoFileName'>{{ fileExtFromUrl(videoFileName) }}</span>
              </div>
              <span class='add-lesson-popup__upload-title'>
                {{ videoFileName || 'Upload Lesson file' }}
              </span>
              <span class='add-lesson-popup__upload-formats'>
                MP4, AVI up to 5GB
              </span>
              <div
                v-if='editMode && videoFileName'
                class='add-lesson-popup__upload-actions'
              >
                <button title='Reupload file' @click='reuploadVideoFile()'>
                  <img alt='Reupload file' src='/images/upload-cloud-outline.svg'/>
                  Reupload file
                </button>
                <button title='Remove file' @click='clearVideoFile()'>
                  <img alt='Remove file' src='/images/upload-trash.svg'/>
                  Remove file
                </button>
              </div>
            </div>
            <upload-progress-bar v-model='uploadVideoProgress'/>
            <field-errors v-model='errors.video'/>

            <!-- <span class="add-lesson-popup__or">- OR -</span> -->

            <span class='add-lesson-popup__row-title'>Documents</span>
            <documents-uploader
              ref='documentsUploader'
              :documents='documents'
              @document-uploaded='documentFileUploaded($event)'
            />
            <upload-progress-bar v-model='uploadDocumentProgress'/>

            <span class='add-lesson-popup__row-title'>Lesson preview</span>
            <div
              :class="{
                'add-lesson-popup__upload': true,
                'add-lesson-popup__upload--uploaded': previewFileName,
              }"
            >
              <input
                id='upload-preview-input'
                ref='uploadPreviewInput'
                accept='image/png, image/jpeg'
                name='upload-preview-input'
                type='file'
                @change='previewFileChanged($event)'
              />
              <label for='upload-preview-input'>Upload a file</label>
              <div class='add-lesson-popup__upload-image'>
                <img
                  ref='uploadPreviewImage'
                  alt='Upload Lesson preview file'
                  src='/images/upload-image.svg'
                />
              </div>
              <span class='add-lesson-popup__upload-title'>{{
                  previewFileName || 'Upload Lesson preview file'
                                                           }}</span>
              <span class='add-lesson-popup__upload-formats'>PNG, JPG up to 10MB</span>
              <div
                v-if='editMode && previewFileName'
                class='add-lesson-popup__upload-actions'
              >
                <button title='Reupload file' @click='reuploadPreviewFile()'>
                  <img alt='Reupload file' src='/images/upload-cloud-outline.svg'/>
                  Reupload file
                </button>
                <button title='Remove file' @click='clearPreviewFile()'>
                  <img alt='Remove file' src='/images/upload-trash.svg'/>
                  Remove file
                </button>
              </div>
            </div>
            <upload-progress-bar v-model='uploadPreviewProgress'/>
            <field-errors v-model='errors.preview'/>

            <span class='add-lesson-popup__row-title'>Price</span>
            <div class='add-lesson-popup__input-price'>
              <input v-model='fields.price' type='number'/>
              <span class='add-lesson-popup__input-price-info'>Per lesson</span>
            </div>
            <field-errors v-model='errors.price'/>

            <p class='add-lesson-popup__note'>
              When a client purchases a lesson from you, they will have
              unlimited access to it on the Skillective platform. They will not
              be able to download the video.
            </p>

            <span class='add-lesson-popup__row-title'>Note</span>
            <text-editor
              v-model='fields.description'
              class='add-lesson-popup__input-desc'
              placeholder='Enter note'
            />
            <field-errors v-model='errors.description'/>

            <loader-button
              v-if='editMode'
              :disabled='computedIsUploading'
              :isLoading='isLoading'
              class='add-lesson-popup__button'
              text='Save'
              @click='editLesson()'
            />
            <loader-button
              v-else
              :disabled='computedIsUploading'
              :isLoading='isLoading'
              class='add-lesson-popup__button'
              text='Add Lesson'
              @click='addLesson()'
            />
            <field-errors v-model='errors.message'/>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import CloseButton from '../../student/CloseButton/CloseButton.vue'
import LoaderButton from '../../cart/LoaderButton/LoaderButton.vue'
import FieldErrors from '../FieldErrors/FieldErrors.vue'
import instructorService from '../../../services/instructorService'
import UploadProgressBar from '../UploadProgressBar/UploadProgressBar.vue'
import DocumentsUploader from '../DocumentsUploader/DocumentsUploader.vue'
import urlHelper from '../../../helpers/urlHelper'
import TextEditor from '../../profile/TextEditor/TextEditor.vue'

export default {
  name: 'AddLessonPopup',
  components: {
    CloseButton,
    LoaderButton,
    FieldErrors,
    UploadProgressBar,
    DocumentsUploader,
    TextEditor,
  },
  props: {
    userGenres: {
      type: Array,
      default: () => {
        return []
      },
    },
  },
  data() {
    return {
      opened: false,
      editMode: false,
      videoFileName: null,
      previewFileName: null,
      uploadVideoProgress: 0,
      uploadPreviewProgress: 0,
      fields: {
        genre: '',
        title: '',
        price: 0,
        description: '',
        video: '',
        preview: '',
      },
      errors: {
        message: [],
        genre: [],
        title: [],
        price: [],
        description: [],
        video: [],
        preview: [],
      },
      formGenres: [],
      isLoading: false,
      lessonId: null,
      documents: [null, null, null, null, null],
      uploadDocumentProgress: 0,
    }
  },
  created() {
    this.formGenres = this.userGenres
  },
  computed: {
    computedIsUploading() {
      return (this.uploadVideoProgress > 0 && this.uploadVideoProgress < 101) ||
        (this.uploadPreviewProgress > 0 && this.uploadPreviewProgress < 101) ||
        (this.uploadDocumentProgress > 0 && this.uploadDocumentProgress < 101)
    },
  },
  methods: {
    closePopup() {
      if (!this.computedIsUploading) this.opened = false
    },
    fileExtFromUrl(url) {
      return urlHelper.fileExtFromUrl(url)
    },
    reuploadVideoFile() {
      document.querySelector('#upload-video-input + label')
        .click()
    },
    reuploadPreviewFile() {
      document.querySelector('#upload-preview-input + label')
        .click()
    },
    clearVideoFile() {
      this.videoFileName = null
      this.fields.video = ''
      this.$refs.uploadVideoImage.src = '/images/upload-cloud.svg'
      document.getElementById('upload-video-input').value = ''
    },
    clearPreviewFile() {
      this.previewFileName = null
      this.fields.preview = ''
      this.$refs.uploadPreviewImage.src = '/images/upload-image.svg'
      document.getElementById('upload-preview-input').value = ''
    },
    async documentFileUploaded({file, index}) {
      const data = await instructorService.uploadVideo(file, (percent) => {
        this.uploadDocumentProgress = percent
      })
      this.documents.splice(index, 1, data)
      this.uploadDocumentProgress = 101
    },
    async videoFileChanged(event) {
      const fileName =
        event.target.files.length > 0 && event.target.files[0].name
      if (fileName) {
        this.videoFileName = fileName
      }
      const data = await instructorService.uploadVideo(event.target.files[0], (percent) => {
        this.uploadVideoProgress = percent
      })
      this.fields.video = urlHelper.fileNameFromUrl(data)
      this.uploadVideoProgress = 101
    },
    async previewFileChanged(event) {
      const fileName =
        event.target.files.length > 0 && event.target.files[0].name
      if (fileName) {
        this.previewFileName = fileName
      }
      const data = await instructorService.uploadVideo(event.target.files[0], (percent) => {
        this.uploadPreviewProgress = percent
      })
      this.fields.preview = urlHelper.fileNameFromUrl(data)

      this.$refs.uploadPreviewImage.src = data
      this.uploadPreviewProgress = 101
    },
    showPopup(
      editMode = false,
      fields = null,
      lessonId = null,
      preview = null,
      documents = null,
    ) {
      this.editMode = editMode
      this.documents = documents || [null, null, null, null, null]
      this.fields = fields || {
        genre: '',
        title: '',
        price: 0,
        description: '',
        video: '',
        preview: '',
      }
      this.errors = {
        message: [],
        genre: [],
        title: [],
        price: [],
        description: [],
        video: [],
        preview: [],
      }
      this.lessonId = lessonId
      this.opened = true

      this.videoFileName = this.fields.video
      this.previewFileName = this.fields.preview

      setTimeout(() => {
        this.$refs.titleInput.focus()

        if (this.fields.preview) {
          this.$refs.uploadPreviewImage.src = preview
        }
      }, 0)
    },
    async addLesson() {
      this.isLoading = true
      const result = await instructorService.addLesson(this.fields, urlHelper.fileNameFromUrl(this.documents, true))
      this.isLoading = false
      if (result.errors) {
        this.errors = result.errors
        this.errors.message = [result.message]
      } else {
        location.reload()
      }
    },
    async editLesson() {
      this.isLoading = true
      const result = await instructorService.editLesson(this.lessonId, this.fields, urlHelper.fileNameFromUrl(this.documents, true))
      this.isLoading = false
      if (result.errors) {
        this.errors = result.errors
        this.errors.message = [result.message]
      } else {
        location.reload()
      }
    },
  },
}
</script>

<style lang='scss' scoped>
@import './AddLessonPopup.scss';
</style>