(($) => {
    
    function infoPopup(message, html = null) {
        return Swal.fire({
            title: message,
            html,
            icon: 'info',
            didOpen: () => {
                Swal.hideLoading();
            }
        });
    }
    
    function errorPopup(message, html = null) {
        return Swal.fire({
            title: message,
            html,
            icon: 'error',
            didOpen: () => {
                Swal.hideLoading();
            }
        });
    }
    
    function successPopup(message, html = null) {
        return Swal.fire({
            title: message,
            html,
            icon: 'success',
            didOpen: () => {
                Swal.hideLoading();
            }
        });
    }
    
    function waitingPopup(title, html = null) {
        Swal.fire({
            title,
            html,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
    
    $(document).on('click', '.copy-token-address', function() {
        let text = $(this).attr("data-address");
		let aux = document.createElement("input");
		aux.setAttribute("value", text);
		document.body.appendChild(aux);
		aux.select();
		document.execCommand("copy");
		document.body.removeChild(aux);
        successPopup(Tokenico.lang.successCopy);
    });

    $(document).on('click', '.load-more-button', function() {

        let btn = $(this);

        let page = parseInt(btn.attr('data-page'));
        let filter = {
            status: $("#status").val(),
            network: $("#network").val()
        };

        let maxPage = parseInt(btn.attr('data-max-page'));
        let text = btn.attr('data-text');
        let text2 = btn.html();

        page++;
        btn.attr('data-page', page);
        
        $.ajax({
            method: 'GET',
            url: Tokenico.apiUrl + '/get-presales',
            data: {
                page,
                filter
            },
            beforeSend() {
                $(".filter-disable-el").show();
                btn.attr("disabled", "disabled");
                btn.html(text);
            },
            success(response) {
                if (response.success) {
                    $(".presales-content").append(response.data);
                } else {
                    btn.hide();
                }
            },
            error() {
                alert("Error");
            },
            complete() {
                $(".filter-disable-el").hide();
                btn.removeAttr("disabled");
                btn.html(text2);
                if (page == maxPage) {
                    btn.hide();
                }
            }
        });
    });

    $(document).on('click', '.filter-button', function(e) {
        e.preventDefault();

        let lmb = $('.load-more-button');
        let btn = $(this);

        let filter = {
            status: $("#status").val(),
            network: $("#network").val()
        };

        let text = btn.attr('data-text');
        let text2 = btn.html();
        
        $.ajax({
            method: 'GET',
            url: Tokenico.apiUrl + '/filter-presales',
            data: {
                filter
            },
            beforeSend() {
                $(".filter-disable-el").show();
                btn.attr("disabled", "disabled");
                btn.html(text);
            },
            success(response) {
                if (response.success) {
                    $(".presales-content").html(response.data.content);
                    if (response.data.maxPage > 1) {
                        lmb.show();
                        lmb.attr('data-page', 1);
                        lmb.attr('data-max-page', response.data.maxPage);
                    } else {
                        lmb.hide();
                    }
                } else {
                    btn.remove();
                }
            },
            error() {
                alert("Error");
            },
            complete() {
                $(".filter-disable-el").hide();
                btn.removeAttr("disabled");
                btn.html(text2);
            }
        });
    });

    if (Tokenico.presale) {

        Tokenico.acceptedChains = {};
        Tokenico.acceptedChains[Tokenico.presale.network.hexId] = Tokenico.presale.network;
        Tokenico.acceptedChains[Tokenico.presale.network.hexId].currencies = {};
        Tokenico.acceptedChains[Tokenico.presale.network.hexId].currencies[Tokenico.presale.network.nativeCurrency.symbol] = Tokenico.presale.network.nativeCurrency;

        initCryptoPay("tokenico-presale", Tokenico);
    }
})(jQuery);