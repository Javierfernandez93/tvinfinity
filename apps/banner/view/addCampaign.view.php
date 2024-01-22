<div class="container-fluid py-4" id="app">
    <addcampaign-viewer
        ref="campaign"
        :viewcountries="viewcountries"
        @toggleviewcountries="toggleViewCountries"></addcampaign-viewer>
    <country-viewer
        ref="country"
        :viewcountries="viewcountries"
        @addcountry="addCountry"
        @deletecountry="deleteCountry"
        ></country-viewer>
</div>