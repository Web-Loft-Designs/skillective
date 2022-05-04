import axios from "axios";
import guestCartHelper from "../helpers/guestCartHelper";

const cartService = {
    async fetchCartItems(guestMode) {
        const response = await axios
            .get(
                guestMode
                    ? `/api/cart?guest_cart=${guestCartHelper.getProductsString()}`
                    : "/api/cart"
            )
            .catch(e => {
                console.log(e);
            });
        return response.data.data.data;
    },
    async addItemToCartAtStart(data, guestMode) {
        if (guestMode) {
            return guestCartHelper.addProduct({
                lesson_id: data.lessonId,
                description: data.specialRequest,
                isPreRecorded: data.isPreRecorded
            });
        } else {
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
        }
    },
    async removeItemFromCart(id, guestMode) {
        if (guestMode) {
            guestCartHelper.removeProduct(id);
        } else {
            await axios.delete("/api/cart/" + id).catch(e => {
                console.log(e);
            });
        }
    },
    async checkCartDotNeeded(guestMode) {
        let needed = false;
        if (guestMode) {
            needed = !guestCartHelper.isEmpty();
        } else {
            needed = await axios
                .get(
                    `/api/cart/has-items?guest_cart=${guestCartHelper.getProductsString()}`
                )
                .catch(e => {
                    console.log(e);
                });
            needed = needed.data.data;

            guestCartHelper.clearProducts();
        }
        return needed;
    },
    async fetchCartTotal(guestMode) {
        if (guestMode) {
            const response = await axios
                .get(
                    `/api/cart/total?guest_cart=${guestCartHelper.getProductsString()}&promo_codes=${guestCartHelper.getPromosString()}`
                )
                .catch(e => {
                    console.log(e);
                });
            return response.data.data;
        } else {
            const response = await axios.get(`/api/cart/total?guest_cart=${guestCartHelper.getProductsString()}&promo_codes=${guestCartHelper.getPromosString()}`).catch(e => {
                console.log(e);
            });
            return response.data.data;
        }
    }
};

export default cartService;
