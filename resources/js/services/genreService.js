import axios from "axios";

const genreService = {
    async autocompleteGenres(str) {
        const response = await axios
            .get("/api/search/genre?genre=" + str)
            .catch(e => {
                console.log(e);
            });
        return response ? response.data.data : false;
    }
};

export default genreService;
