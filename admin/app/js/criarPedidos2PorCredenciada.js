
let suiv_url = $("body").attr("suiv_url");
let suiv_authorization = $("body").attr("suiv_authorization");
let tempToken = '';

async function gerar_Sets(token){
    tempToken =  token;
    $(".loading").show();
    await $.ajax({
        url: `${suiv_url}/Sets?vehicleToken=${token}`, 
        type: 'GET', 
        dataType: 'json', 
        headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
        success: function (data) {
            const selectElement = $("#partsSelect");
            selectElement.empty(); 
            selectElement.append($("<option>").attr("value", "").text("Selecione..."));
            
            data.forEach(function(parts) {
                selectElement.append($("<option>").attr('value', parts.id).text(parts.description));
            });
            
            $(".loading").hide(); 
            $("#partsSelect").parent().css("display", "block");
            $("#nickNamesSelect").parent().css("display", "block");
        },   
        error: function (data) {
            $(".loading").hide(); 
        } 
    });
}

$("#partsSelect").change(function() {
    var setId = $(this).val();

    gerar_Nicknames(tempToken, setId);
});

async function gerar_Nicknames(token, setId){
    await $.ajax({
        url: `${suiv_url}/Nicknames?vehicleToken=${token}&setId=${setId}`, 
        type: 'GET', 
        dataType: 'json', 
        headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
        success: function (data) {
            const selectElement = $("#nickNamesSelect");
            selectElement.empty(); 
            selectElement.append($("<option>").attr("value", "").text("Selecione..."));
            
            data.forEach(function(parts) {
                selectElement.append($("<option>").attr('value', parts.id).text(parts.description));
            });
            
            $(".loading").hide(); 
        },  
        error: function (data) {
            $(".loading").hide(); 
        } 
    });
}

$("#nickNamesSelect").change(function() {
    var nicknameId = $(this).val();
    var nicknameName = $("#nickNamesSelect option:selected").text();
    $('.nomeDaPeca').val(nicknameName);

    price(tempToken, nicknameId);
});

async function price(token, nicknameId){
    await $.ajax({
        url: `${suiv_url}/Parts?vehicleToken=${token}&nicknameId=${nicknameId}`, 
        type: 'GET', 
        dataType: 'json', 
        headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
        success: function (response) {
            if (response) {
                $('.priceDaPeca').val(response[0].price);
            }
        },  
        error: function (data) {
             
        } 
    });
}
