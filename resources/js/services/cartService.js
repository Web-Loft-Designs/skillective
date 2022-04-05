import axios from "axios";

const cartService = {
    async fetchCartItems() {
        const response = await axios.get("/api/cart").catch(e => {
            console.log(e);
        });
        return response.data.data.data;
    },
    async addItemToCartAtStart(data) {
        const response = await axios
            .post("/api/cart", {
                lesson_id: data.lessonId,
                description: data.specialRequest,
                isPreRecorded: data.isPreRecorded
            })
            .catch(({ response }) => {
                return { message: response.data.message, isError: true };
            });
        return response;
    },
    async removeItemFromCart(id) {
        await axios.delete("/api/cart/" + id).catch(e => {
            console.log(e);
        });
    },
    async checkCartDotNeeded() {
        const needed = await axios.get("/api/cart/has-items").catch(e => {
            console.log(e);
        });
        return needed;
    },
    async fetchCartTotal() {
        const response = await axios.get("/api/cart/total").catch(e => {
            console.log(e);
        });
        return response.data.data;
    }
};

export default cartService;
