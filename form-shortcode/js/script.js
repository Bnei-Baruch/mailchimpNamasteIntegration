(function($) {
  var templates = {};
  var currentPath = location.origin
          + '/wp-content/plugins/mailchimp-bp-integrator/includes';

  $(document).ready(function() {
    $("#registrationForm").on('click', '.submit', submitRegister);
    $("#loginForm").on('click', '.submit', submitLogin);
    $("#registerUsersFromExel").on('click', registerUsersFromExel);

    // load js template and open dialog
    _loadTemplates();
  });

  function _loadTemplates() {
    if (location.pathname === "/") {
      $.get(currentPath + '/updateProfileTpl.html').done(function(data) {
        templates.updateProfile = Handlebars.compile(data);
        getUpdateProfile();
      });
    }
    /*
     * if (document.referrer.indexOf('login') > 0 ||
     * document.referrer.indexOf('registration') > 0) { } templates.thankYou =
     * Handlebars.compile('<div id="thankYou"><p>{{content}}</p></div>');
     */
  }

  function getThankYou(data) {
    var renderedHTML = $(templates.thankYou(data.message)).hide();
    $('#thankYou').remove();
    $('body').append(renderedHTML);

    var dialog = renderedHTML.dialog({
      autoOpen: true,
      modal: true,
      resizable: false,
      draggable: false,
      closeText: "hide",
      width: 600,
      title: data.message.title,
      buttons: [{
        text: data.buttons.toHome.text,
        click: function(e) {
          window.location.replace(data.buttons.toHome.url);
        }
      }]
    });
  }

  function getUpdateProfile() {
    var data = {
      action: 'getUpdateProfileRregistrationFormShortcode'
    };
    sendRequest(data, getUpdateProfileCallback, $('body'));
  }

  function getUpdateProfileCallback(data) {
    var renderedHTML = $(templates.updateProfile(data)).hide();
    $('#updateProfileForm').remove();
    $('body').append(renderedHTML);

    var dialog = renderedHTML.dialog({
      autoOpen: true,
      modal: true,
      resizable: false,
      draggable: false,
      closeText: "hide",
      title: data.translate.title,
      buttons: [{
        text: data.translate.save,
        click: function(e) {
          $(this).dialog("destroy");
          setUpdateProfile();
        }
      }, {
        text: data.translate.cancel,
        click: function() {
          $(this).dialog("destroy");
        }
      }]
    });
  }

  function setUpdateProfile() {
    $container = $('#updateProfileForm');
    var data = {
      action: 'setUpdateProfileRregistrationFormShortcode',
      userData: $container.find('form').serialize()
    };
    sendRequest(data, function(data) {

    }, $container);
  }

  function registerUsersFromExel(e) {
    e.preventDefault();

    $container = $("#registrationForm");
    var data = {
      action: 'fromExelRregistrationFormShortcode',
    };

    function callback(data) {
      if (data && data.result) {

      }
    }
    sendRequest(data, callback, $container);

  }
  /*
   * Reset password customisation $("#rememberPass").on('click', rememberPass);
   * 
   * function rememberPass(e){ e.preventDefault(); $container = $("#loginForm");
   * var data = { action : 'rememberRregistrationFormShortcode', userData :
   * $container.serialize() }; function callback(data) { if (data &&
   * data.result) $domEl.find(".errorMsg").html('Please check you mail').show(); }
   * sendRequest(data, callback, $container); }
   */

  function submitRegister(event) {
    event.preventDefault();
    $container = $("#registrationForm");
    var data = {
      action: 'registerRregistrationFormShortcode',
      userData: $container.serialize()
    };

    function callback(data) {
      if (data && data.result) {
        var _url = window.location.origin + "/thank-you/";
        // getThankYou(data)
        // window.location.search = "successful=true";
        window.location.replace(_url);
      }
    }
    sendRequest(data, callback, $container);
  }

  function submitLogin(event) {
    event.preventDefault();
    $container = $("#loginForm");
    var data = {
      action: 'loginRregistrationFormShortcode',
      userData: $container.serialize()
    };

    function callback(data) {
      if (!data && !data.result) return;
      var redirectUrl = searchToObject().redirectUrl;
      redirectUrl = !redirectUrl ? window.location.origin : redirectUrl;
      window.location.replace(redirectUrl);
    }
    sendRequest(data, callback, $container);
  }

  function sendRequest(data, callback, $domEl) {
    $domEl.find(".errorMsg").hide();
    if ($domEl.validate) $domEl.validate();
    /*
     * var errors = $domEl.validate(); if(!errors.valid()) return;
     */
    $domEl.find(".preloader").show();
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: data,
      dataType: 'json'
    }).done(function(data) {
      $domEl.find(".preloader").hide();
      if (!data.result) {
        $domEl.find(".errorMsg").html(data.error).show();
        return;
      }
      callback(data);
    })
            .fail(
                    function(jqXHR, textStatus) {
                      var error = 'Error:' + textStatus
                              + '. Please try more late.'
                      $domEl.find(".preloader").hide();
                      $domEl.find(".errorMsg")
                              .html(
                                      'Error:' + textStatus
                                              + '. Please try more late.')
                              .show();
                    });
  }

  function searchToObject() {
    var pairs = window.location.search.substring(1).split("&"), obj = {}, pair, i;

    for (i in pairs) {
      if (pairs[i] === "") continue;

      pair = pairs[i].split("=");
      obj[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
    }

    return obj;
  }
}(jQuery));