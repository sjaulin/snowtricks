document.addEventListener("DOMContentLoaded", function () {

    jQuery("form .btn-primary").click(function(event) {
        var ajaxErrors = document.getElementsByClassName('ajax-errors');
        while(ajaxErrors[0]) {
            ajaxErrors[0].parentNode.removeChild(ajaxErrors[0]);
        }​

        event.preventDefault();

        var url = window.location.href;

        var formElement = document.getElementById("trick-form");
        var request = new XMLHttpRequest();
        request.open("POST", url);
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");// Tells server that this call is made for ajax purposes.
        request.send(new FormData(formElement));

        request.onload = function () {
            if (request.readyState === request.DONE && request.status === 200 && request.response !== undefined) {
                    
                    const response = JSON.parse(request.response);
                    
                    if (response.code !== undefined) {
                        if (response.code == 'ok') {
                            formElement.submit();
                        }
                        else {
                            if (response.code !== undefined && response.code == 'error') {
                                for (const [key, value] of Object.entries(response.errors)) {
                                
                                const div = document.createElement('div');

                                var errorHtml = `<div class="ajax-errors mb-2"><span class="invalid-feedback d-block"><span class="d-block">
                                <span class="form-error-icon badge badge-danger text-uppercase">Erreur</span> <span class="form-error-message">Le fichier est trop volumineux. Sa taille ne doit pas dépasser 2097152 bytes.</span>
                                </span></span></div>
                                `;

                                var inputError = document.getElementById(key);
                                var parentGroup = inputError.closest(".form-group");
                                parentGroup.insertAdjacentHTML('afterbegin', errorHtml);
                                
                                }
                            }
                        }
                    }
            }
        };

        /*
        $.ajax({
            type: "POST",
            url: url,
            data: formSerialize,
            success: function (result) {
                console.log(result);
                if (result.code === 200) {
                    // refresh current url to see student
                } else {

                }
            }
        });
        */
        
    })

});


