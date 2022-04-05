const geoService = {
    async getLocation() {
        let result = null;

        if (navigator.geolocation) {
            let latlng = (latlng = { lat: parseFloat(0), lng: parseFloat(0) });

            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject);
            });

            latlng = {
                lat: parseFloat(position.coords.latitude),
                lng: parseFloat(position.coords.longitude)
            };

            let geocoder = new google.maps.Geocoder();

            await geocoder.geocode(
                { location: latlng },
                (results, status) => {
                    if (status == "OK") {
                        let complited_address = "";
                        results[0].address_components.map(item => {
                            if (item.types[0] == "locality") {
                                complited_address = item.short_name;
                            }

                            if (
                                item.types[0] ==
                                "administrative_area_level_1"
                            ) {
                                complited_address =
                                    complited_address +
                                    ", " +
                                    item.short_name;
                            }

                            if (item.types[0] == "country") {
                                complited_address =
                                    complited_address +
                                    ", " +
                                    item.short_name;
                            }
                        });
                        result = complited_address;
                    } else {
                        alert(
                            "Geocode was not successful for the following reason: " +
                                status
                        );
                    }
                }
            );
        } else {
            alert("Geolocation not enabled");
        }

        return result;
    }
};

export default geoService;
