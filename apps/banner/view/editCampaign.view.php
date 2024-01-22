<div class="container-fluid py-4" id="app">
    <editcampaign-viewer
        ref="campaign"
        :viewcountries="viewcountries"
        @toggleviewcountries="toggleViewCountries"></editcampaign-viewer>
    <country-viewer
        ref="country"
        :viewcountries="viewcountries"
        @addcountry="addCountry"
        @deletecountry="deleteCountry"
        ></country-viewer>
</div>