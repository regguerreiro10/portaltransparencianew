
let suiv_url = $("body").attr("suiv_url");
let suiv_authorization = $("body").attr("suiv_authorization");
let token_veiculo = $("body").attr("token_veiculo");

console.log(suiv_url);
console.log(suiv_authorization);
console.log(token_veiculo);

gerar_Sets(token_veiculo);

async function gerar_Sets(token){
    await $.ajax({
        url: `${suiv_url}/Sets?vehicleToken=${token}`, type: 'GET', dataType: 'json', headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
        success: function (data) {
            $("#suiv-grupo-de-pecas").html(`<option value="" hidden>Selecione...</option>`);
            for(let i of data){
                $("#suiv-grupo-de-pecas").append(`<option value="${i.id}" hidden>${i.description}</option>`);
            } 
        },   
        error: function (data) {
            console.log(data);
        } 
    });
}

$("#suiv-grupo-de-pecas").change(function() {
    var setId = $(this).val();
    gerar_Nicknames(token_veiculo, setId);
    $(".suiv-nome-da-peca").val("");
    $(".suiv-preco-da-peca").val("");
});

async function gerar_Nicknames(token, setId){
    await $.ajax({
        url: `${suiv_url}/Nicknames?vehicleToken=${token}&setId=${setId}`, type: 'GET', dataType: 'json', headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
        success: function (data) {
            $("#suiv-pecas").html(`<option value="" hidden>Selecione...</option>`);
            for(let i of data){
                $("#suiv-pecas").append(`<option value="${i.id}" hidden>${i.description}</option>`);
            } 
        },  
        error: function (data) {
            console.log(data);
        } 
    });
}

$("#suiv-pecas").change(function() {
    var nicknameId = $(this).val();
    var nicknameName = $("#suiv-pecas option:selected").text();
    $(".suiv-nome-da-peca").val(nicknameName);
    $(".suiv-preco-da-peca").val("");
    price(token_veiculo, nicknameId);
});

async function price(token, nicknameId){
    await $.ajax({
        url: `${suiv_url}/Parts?vehicleToken=${token}&nicknameId=${nicknameId}`, type: 'GET', dataType: 'json', headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
        success: function (data) {
            $(".suiv-preco-da-peca").val(data[0].price);
        },  
        error: function (data) {
            console.log(data);
        } 
    });
}
