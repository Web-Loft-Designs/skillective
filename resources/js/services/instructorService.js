import axios from "axios";

const instructorService = {
    async autocompleteInstructors(str) {
        const response = await axios
            .get("/api/search/instructors?instructor=" + str)
            .catch(e => {
                console.log(e);
            });
        return response ? response.data.data : false;
    },
    async featuredInstructors() {
        const response = await axios
            .get("/api/featured-instructors")
            .catch(e => {
                console.log(e);
            });
        return response ? response.data.data.data : false;
    },
    async addLesson(fields, documents) {
        const response = await axios
            .post("/api/instructor/pre-r-lesson", {
                ...fields,
                documents,
            })
            .catch(e => {
                console.log(e);
                return e.response.data;
            });
        return response;
    },
    async createDiscount(fields) {
        const response = await axios
            .post("/api/instructor/discount", {
                ...fields
            })
            .catch(e => {
                console.log(e);
                return e.response.data;
            });
        return response;
    },
    async updateDiscount(fields, id) {
        const response = await axios
            .put(`/api/instructor/discount/${id}`, {
                ...fields
            })
            .catch(e => {
                console.log(e);
                return e.response.data;
            });
        return response;
    },
    async deleteDiscount(id) {
        const response = await axios
            .delete(`/api/instructor/discount/${id}`)
            .catch(e => {
                console.log(e);
                return e.response.data;
            });
        return response;
    },
    async createPromo(fields, notifyClients) {
        const response = await axios
            .post("/api/instructor/promo", {
                ...fields,
                notifyClients,
            })
            .catch(e => {
                console.log(e);
                return e.response.data;
            });
        return response;
    },
    async updatePromo(fields, id, notifyClients) {
        const response = await axios
            .put(`/api/instructor/promo/${id}`, {
                ...fields,
                notifyClients
            })
            .catch(e => {
                console.log(e);
                return e.response.data;
            });
        return response;
    },
    async deletePromo(id) {
        const response = await axios
            .delete(`/api/instructor/promo/${id}`)
            .catch(e => {
                console.log(e);
                return e.response.data;
            });
        return response;
    },
    async editLesson(id, fields, documents) {
        const response = await axios
            .put("/api/instructor/pre-r-lesson/" + id, {
                ...fields,
                documents,
            })
            .catch(e => {
                console.log(e);
                return e.response.data;
            });
        return response;
    },
    async deleteLesson(id) {
        const response = await axios
            .delete("/api/instructor/pre-r-lesson/" + id)
            .catch(e => {
                console.log(e);
                return e.response.data;
            });
        return response;
    },
    async uploadVideo(file, progressChanged) {
        const formData = new FormData();
        formData.append("uploaded_video", file);

        const response = await axios
            .post("/api/instructor/uploud-video", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
                onUploadProgress: (progressEvent) => {
                    progressChanged(progressEvent.loaded * 100 / progressEvent.total);
                },
            })
            .catch(e => {
                console.log(e);
            });
        return response.data.data;
    },
};

export default instructorService;
