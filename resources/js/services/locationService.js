import axios from "axios";

const locationService = {
    async autocompleteLocation(str) {
        const response = await axios
            .get("/api/search/location?location=" + str)
            .catch(e => {
                console.log(e);
            });
        return response ? response.data.data : false;
    }
};

export default locationService;
