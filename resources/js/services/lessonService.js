import axios from "axios";
import urlHelper from "../helpers/urlHelper";

const lessonService = {
    async lessons(params) {
        const response = await axios.get(urlHelper.buildRequestParamsStr("/api/lessons/search", params)).catch(e => {
            console.log(e);
        });
        return {
            lessons: response.data.data.data,
            pagination: {
                currentPage: response.data.data.meta.pagination.current_page,
                pageCount: response.data.data.meta.pagination.total_pages
            }
        };
    },
    async upcomingNearbyLessons() {
        const response = await axios.get("/api/upcoming-lessons").catch(e => {
            console.log(e);
        });
        return response.data.data;
    },
    async preRecordedLessons(params) {
        const response = await axios
            .get(urlHelper.buildRequestParamsStr("/api/pre-r-lesson", params))
            .catch(e => {
                console.log(e);
            });
        return {
            lessons: response.data.data.data,
            pagination: {
                currentPage: response.data.data.meta.pagination.current_page,
                pageCount: response.data.data.meta.pagination.total_pages
            }
        };
    },
    async myLibraryLessons() {
        const response = await axios
            .get("/api/student/pre-r-lessons")
            .catch(e => {
                console.log(e);
            });
        return response.data.data.data;
    },
    async myLibraryLessonById(lessonId) {
        const response = await axios
            .get("/api/student/pre-r-lessons/" + lessonId)
            .catch(e => {
                console.log(e);
            });
        return response.data.data.data;
    },
    async myShopLessons(params) {
        const response = await axios
            .get(urlHelper.buildRequestParamsStr("/api/instructor/pre-r-lesson", params))
            .catch(e => {
                console.log(e);
            });
        return {
            lessons: response.data.data.data,
            pagination: {
                currentPage: response.data.data.meta.pagination.current_page,
                pageCount: response.data.data.meta.pagination.total_pages
            }
        };
    },
    async myShopLessonById(lessonId) {
        const response = await axios
            .get("/api/instructor/pre-r-lesson/" + lessonId)
            .catch(e => {
                console.log(e);
            });
        return response.data.data.data;
    },
    async myShopGenres() {
        const response = await axios
            .get("/api/instructor/pre-r-lesson/genres")
            .catch(e => {
                console.log(e);
            });
        return response.data.data;
    },
    async instructorLessonsById(instructorId) {
        const response = await axios
            .get("/api/pre-r-lesson/instructor/" + instructorId)
            .catch(e => {
                console.log(e);
            });
        return response.data.data.data;
    }
};

export default lessonService;
