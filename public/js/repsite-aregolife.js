jQuery(function () {

    // Check for cookie
    var repsite = getCookie("repsite");

    // Get domain - username, ignore www
    var domain = window.location.hostname;
    domain = domain.replace('www.', "");

    var domainArr = domain.split('.');

    if (domainArr.length > 2) {
        var username = domainArr[0];
    }

    if (repsite) {
        repsite = JSON.parse(repsite);
        if (username && repsite.username != username) {
            getRepsiteHeader();
        } else {
            setRepsiteHeader(repsite);
        }
    } else {
        getRepsiteHeader();

    }


    function getRepsiteHeader() {
        if (username) {
            jQuery.post(repsite_ajax.ajax_url, {
                _ajax_nonce: repsite_ajax.nonce,
                action: "repsite_header",
                username: username,
            }, function (data) {
                response = JSON.parse(data);
                if (response.error) {
                    if (repsite) { //we already have a dist, use it instead
                        setRepsiteHeader(repsite);
                    }
                } else {
                    setCookie("repsite", data, 7, domain.replace(username, '')); //TODO: setting for cookie expiration
                    setRepsiteHeader(response);
                }
            });
        }
    }


    function setRepsiteHeader(repsite) {
        if (repsite.name) {
            jQuery(".repsite-guestOf").each(function () {
                jQuery(this).text(jQuery(this).data('text'));
            });
            jQuery(".repsite-name").text(repsite.name);
        }

        if (repsite.img) {
            jQuery(".repsite-photo").append('<img src="' + repsite.img + '">');
        }

        if (repsite.email) {
            jQuery(".repsite-email").each(function () {
                jQuery(this).prepend(jQuery(this).data('text'));
                jQuery(this).find("a").attr("href", "mailto: " + repsite.email).text(repsite.email);
            });
        }
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function setCookie(cname, cvalue, exdays, domain) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";domain=" + domain + ";path=/";
    }

});
