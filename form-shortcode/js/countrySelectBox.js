(function($) {
  var selectedCountry, countries, city, $countryEl, $cityEl, _prevCountrySearchText, _prevCitySearchText;
  $(document).ready(function() {
    getCountries();
    $countryEl = $("#country").autocomplete({
      select: selectCountry,
      minLength: 1,
      change: checkIsDisableCity
    });

    $cityEl = $("#registerUsersCityByText input").autocomplete({
      search: getCities,
      minLength: 1
    });
  });

  function getCountries() {
    var data = {
      lang: "ru",
      username: "kabacademy"
    };
    return jQuery.get("http://api.geonames.org/countryInfoJSON", data).done(
            function(r) {
              countries = r.geonames;

              var source = r.geonames.map(function(c) {
                return c.countryName;
              });
              $countryEl.autocomplete("option", "source", source);
            });
  }

  function selectCountry(event, ui) {
    selectedCountry = _findCountryByName(ui.item.value);
  }

  function getCities() {

    var text = $cityEl.val();
    if (!text || !!_prevCitySearchText
            && _prevCitySearchText.indexOf(text) !== -1) { return; }
    _prevCitySearchText = text;

    var data = {
      lang: "ru",
      username: "kabacademy",
      name_startsWith: text,
      cities: "cities1000",
      country: selectedCountry.countryCode,
      type: "json"
    };
    return jQuery.get("http://api.geonames.org/search", data).done(function(r) {
      var source = r.geonames.map(function(item) {
        return item.name;
      });
      $cityEl.autocomplete("option", "source", source);
    });
  }

  function _findCountryByName(name) {
    var _country;
    countries.some(function(c) {
      if (c.countryName === name) {
        _country = c;
        return true;
      }
    });
    return _country;
  }
  function checkIsDisableCity() {
    if (!selectedCountry || selectedCountry.countryName !== $countryEl.val()) {
      $cityEl.attr("disabled", true);
      $cityEl.val('');
      return;
    }
    $cityEl.attr("disabled", false);
  }

}(jQuery));