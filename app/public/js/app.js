//Facebook login

window.fbAsyncInit = function () {
    FB.init({
        appId: '2140704912894827',
        cookie: true,
        xfbml: true,
        version: 'v3.3'
    });

    FB.AppEvents.logPageView();

};

(function (d, s, id) {
    let js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// This is called with the results from from FB.getLoginStatus().
function statusChangeCallback(response) {
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected') {
        // Logged into your app and Facebook.
        registerSession();
    }
}

// This function is called when someone finishes with the Login
// Button.  See the onlogin handler attached to it in the sample
// code below.
function checkLoginState() {
    FB.getLoginStatus(function (response) {
        statusChangeCallback(response);
    });
}

// Here we run a very simple test of the Graph API after login is
// successful.  See statusChangeCallback() for when this call is made.
function registerSession() {
    FB.api('/me', {
        "fields": "id,name,email,first_name,last_name"
    }, function (response) {

        let name = response.name;
        let email = response.email;
        let id = response.id;

        $.ajax({
            type: 'POST',
            url: "{{url('login/faceLogin')}}",
            data: {
                name: name,
                email: email,
                id: id
            },
            success: function (result) {
                // redirect to country search page
                window.location.href = "{{url('country')}}";
            }
        });
    });
}

let table = $('#infoCountry').dataTable({
    "sPaginationType": "full_numbers",

    responsive: true,
    "language": {
        "lengthMenu": "Mostrar _MENU_ resultados por página",
        "zeroRecords": "Sem resultados",
        "info": "Página _PAGE_ de _PAGES_",
        "infoEmpty": "Sem registos",
        "infoFiltered": "(filtrado de um total de _MAX_ resultados)",
        "search": "Pesquisar",
        "paginate": {
            "previous": "<",
            "first": "<<",
            "last": ">>",
            "next": ">"
        }
    },
    "columnDefs": [{
        "defaultContent": " ",
        "targets": "_all"
    }]
});


function getInfoCountry(countryName) {
    table.fnClearTable();
    $.ajax({
        type: 'GET',
        url: "https://restcountries.eu/rest/v2/name/" + countryName,

        success: function (result) {
            if (result.length > 0) {
                for (index = 0; index < result.length; ++index) {

                    table.fnAddData([
                        "<img  class='flag' alt='' src=" + result[index]['flag'] + " >" + result[index]['name'],
                        result[index]['alpha2Code'],
                        result[index]['alpha3Code'],
                        result[index]['numericCode'],
                        result[index]['region'],
                        result[index]['population']
                    ]);
                }
            }
        },
        error: function(){
            alert("Pais não encontrado.");
        }
    });
}

function saveSearchInBD(countryName) {

    var searchSaveUrl = $(".url-info").data('urlsave');
    $.ajax({
        type: 'POST',
        url: searchSaveUrl,
        data : {countryName : countryName},
        success: function (result) {
        },
        error: function (){
            alert("Erro a inserir o log da pesquisa!!!");
        }
    });
}

function saveSearch(countryName) {
    let txt = "";
    let search = localStorage.getItem('search') ? JSON.parse(localStorage.getItem('search')) : [];
    if (search.length < 5) {
        search.push(countryName);
    } else if (search.length == 5) {
        search.shift();
        search.push(countryName);
    }
    localStorage.setItem('search', JSON.stringify(search));

    search.forEach(function (entry, index) {
        txt += entry;
        // If last element don't apply separator
        if (index !== (search.length - 1)) {
            txt += ', ';
        }
    });
    $(".lastSearch").html(txt);
}

$('.searchinput').keypress(function (e) {
    if (e.which == 13) {//Enter key pressed
        let country = $(".searchinput").val();
        search(country);
    }
});

$(".btn-search").on("click", function () {
    let country = $(".searchinput").val();
    search(country);
});

$(".last-search").on("click", function () {
    let lastSearchs = localStorage.getItem('search') ? JSON.parse(localStorage.getItem('search')) : [];
    if (lastSearchs.length > 0) {
        search(lastSearchs.slice(-1)[0]); // get last search
    }
});

function search(country) {

    if (country.trim().length == 0) {
        alert("Precisa de inserir um país");
        return false;
    }
    saveSearch(country);
    getInfoCountry(country);
    saveSearchInBD(country);
}