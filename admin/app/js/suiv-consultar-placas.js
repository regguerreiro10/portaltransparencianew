$(document).ready(function(){
    console.log("start-suiv-api");
    
    let suiv_url = $("body").attr("SUIV_URL");
    let suiv_authorization = $("body").attr("SUIV_AUTHORIZATION");
    
    function removerEspacos(texto) {
        return texto.replace(/[^\w]/gi, '');
    }

    $(document).on("keyup", ".pagina-consultar-placas-formulario input", function(){
        let valor = $(this).val();
        $(this).val(removerEspacos(valor).toUpperCase());
    });

    $(document).on("click", ".pagina-consultar-placas-formulario a", function(){
        $(".loading").show();
        let placa = $(".pagina-consultar-placas-formulario input").val();
        if(`${placa}` == ""){
            alert("Digite a placa desejada para consultar sua lista de peças e preços");
            $(".loading").hide();
        }else{
            $(".pagina-consultar-placas-container-titulo").html("");
            $(".pagina-consultar-placas-container-div").html("");
            $(".loading-carregando").html(`Conectando com o SUIV`);
            consultar_placa(placa);
        }
    });

    let array_pecas = [];
    let gerar_pecas_return = [];
    let gerar_nicknames_return = [];
    let gerar_VehicleToken_return = [];
    let consultar_placa_return = [];
    let retorno_funcao = [];
    let contador = 0;
    let msgError = "Não foi possível localizar peças nessa categoria!";
    
    async function moedaBR(valor){
        valor = valor.toString().replace(/\D/g,"");
        valor = valor.toString().replace(/(\d)(\d{8})$/,"$1.$2");
        valor = valor.toString().replace(/(\d)(\d{5})$/,"$1.$2");
        valor = valor.toString().replace(/(\d)(\d{2})$/,"$1,$2");
        return valor;
    }  
    
    async function status_api() {
        $(".pagina-consultar-placas-container-titulo").html("Tempo limite de conexão com a API do SUIV excedido, tente novamente ou aguarde alguns minutos antes de realizar uma nova consulta");
        $(".loading").hide(); 
    }
   let dataTemp = []
    async function consultar_placa(placa){
    
        const verifica_conexao = setTimeout(status_api, 20000);
        await $.ajax({
            url: `${suiv_url}/VehicleInfo/byplate?plate=${placa}`, 
            type: 'GET', dataType: 'json', 
            headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
            success: function (data) {
                let suiv = data.suivDataCollection[0];
     
                if(`${suiv}` != "undefined"){
                    dataTemp = data
                    consultar_marca()
                    $(".pagina-consultar-placas-container-titulo").html(`${suiv.makerDescription} | ${suiv.modelDescription} | ${suiv.versionDescription} | ${data.yearModel}`);
                   
                }else{
                    $(".pagina-consultar-placas-container-titulo").html(`Não foi possível localizar a placa ${placa}, favor tente novamente...`);
                 
                }
                $(".loading").hide(); 
                clearTimeout(verifica_conexao);  
            },   
            error: function (data) {
                $(".pagina-consultar-placas-container-titulo").html(`Não foi possível localizar a placa ${placa}, favor tente novamente...`);
                $(".loading").hide(); 
                //console.log(data);
                clearTimeout(verifica_conexao);
                $(".loading").hide(); 
            } 
        });
    }
    
    function clearModel() {
        $("#modeloSelect").empty();
        $("#modeloSelect").append($("<option>").attr("value", "").text("Selecione..."));
        clearHTML();
    }
    
    function clearVersion() {
        $("#versaoSelect").empty();
        $("#versaoSelect").append($("<option>").attr("value", "").text("Selecione..."));
        $("#anoSelect").empty();
        $("#anoSelect").append($("<option>").attr("value", "").text("Selecione..."));
        versoesArray = [];
        clearHTML();
    }
    
    function clearPartsSelect() {
        $("#partsSelect").empty();
        $("#partsSelect").append($("<option>").attr("value", "").text("Selecione..."));
        $("#partsSelect-subgrupo").html(`<option value="" hidden>Selecione...</option>`);
        clearHTML();
    }
    
    function clearHTML() {
        $(".pagina-consultar-placas-container-titulo").html("");
        $(".pagina-consultar-placas-container-div").html("");
    }
    
    consultar_marca()
    async function consultar_marca() {
        $(".loading").show();
        const verifica_conexao = setTimeout(status_api, 20000);
        await $.ajax({
            url: `${suiv_url}/Makers`,
            type: 'GET',
            dataType: 'json',
            headers: { "Authorization": `${suiv_authorization}`, "Content-Type": "application/json" },
            success: function (data) {
                const selectElement = $("#marcaSelect");
                selectElement.empty(); 
                selectElement.append($("<option>").attr("value", "").text("Selecione..."));
                
                data.forEach(function(marca) {
                    selectElement.append($("<option>").attr('value', marca.id).text(marca.description));
                });
                
                if(dataTemp.length !== 0) {
                    const temp = data.find(marca => marca.id === dataTemp.suivDataCollection[0].makerId);
                    const tempId = temp.id
                    if (temp) {
                        selectElement.val(tempId);
                        consultar_modelo(tempId);
                    }
                }
                
                clearTimeout(verifica_conexao);
                $(".loading").hide(); 
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText); // Exibe a resposta de erro
                console.log(status); // Exibe o status da requisição
                console.log(error); // Exibe a mensagem de erro
                clearTimeout(verifica_conexao);
                $(".loading").hide(); 
            }
        });
    }

    $('#marcaSelect').on('change', function() {
        clearModel();
        clearVersion();
        clearPartsSelect();
        var marcaId = $(this).val();
        
        consultar_modelo(marcaId);
    });
    async function consultar_modelo(marcaId) {
        $(".loading").show(); 
        const verifica_conexao = setTimeout(status_api, 20000);
        await $.ajax({
            url: `${suiv_url}/Models?makerId=${marcaId}`,
            type: 'GET',
            dataType: 'json',
            headers: { "Authorization": `${suiv_authorization}`, "Content-Type": "application/json" },
            success: function (data) {
                const selectElement = $("#modeloSelect");
                selectElement.empty(); 
                
                selectElement.append($("<option>").attr("value", "").text("Selecione..."));
                data.forEach(function(modelo) {
                    selectElement.append($("<option>").attr('value', modelo.id).text(modelo.description));
                });
                
                if(dataTemp.length !== 0) {
                    const temp = data.find(el => el.id === dataTemp.suivDataCollection[0].modelId);
                    const tempId = temp.id
                    if (temp) {
                        selectElement.val(tempId);
                        consultar_versao(tempId);
                    }
                }
                
                clearTimeout(verifica_conexao);
                $(".loading").hide(); 
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText); // Exibe a resposta de erro
                console.log(status); // Exibe o status da requisição
                console.log(error); // Exibe a mensagem de erro
                clearTimeout(verifica_conexao);
                $(".loading").hide(); 
            }
        });
    }

     $('#modeloSelect').on('change', function() {
        clearVersion();
        clearPartsSelect();
        var modelId = $(this).val();
        
        consultar_versao(modelId);
    });
    let versoesArray = [];
    async function consultar_versao(versaoId) {
        $(".loading").show(); 
        const verifica_conexao = setTimeout(status_api, 20000);
        await $.ajax({
            url: `${suiv_url}/Versions?modelId=${versaoId}`,
            type: 'GET',
            dataType: 'json',
            headers: { "Authorization": `${suiv_authorization}`, "Content-Type": "application/json" },
            success: function (data) {
                const selectElement = $("#versaoSelect");
                selectElement.empty(); 

                selectElement.append($("<option>").attr("value", "").text("Selecione..."));
                versoesArray = data;
                data.forEach(function(versao) {
                    selectElement.append($("<option>").attr('value', versao.id).text(versao.description));
                });
                
                if(dataTemp.length !== 0) {
                    console.log('dataTemp',dataTemp)
                    const temp = data.find(el => el.id === dataTemp.suivDataCollection[0].versionId);
                    const tempId = temp.id
                    if (temp) {
                        selectElement.val(tempId);
                    }
                }
                clearTimeout(verifica_conexao);
                $(".loading").hide(); 
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText); // Exibe a resposta de erro
                console.log(status); // Exibe o status da requisição
                console.log(error); // Exibe a mensagem de erro
                clearTimeout(verifica_conexao);
                $(".loading").hide(); 
            }
        });
    }
    
     $('#versaoSelect').on('change', function() {
        var modelId = $(this).val();
        
        const selectElement = $("#anoSelect");
        selectElement.empty(); 

        const versoesFiltradas = versoesArray.filter((el) => {
            return String(el.id) === modelId;
        });        
        
        if (versoesFiltradas) {
            const startingYear = versoesFiltradas[0].startingYear;
            const endingYear = versoesFiltradas[0].endingYear;
            
            const yearFab = [];
            for (let year = startingYear; year <= endingYear; year++) {
                yearFab.push({ value: year, label: year });
            }
            
            selectElement.append($("<option>").attr("value", "").text("Selecione..."));
            yearFab.forEach(function(year) {
                selectElement.append($("<option>").attr('value', year.value).text(year.label));
            });
        }
    });
    
    $('#anoSelect').on('change', function() {
        clearPartsSelect();
        $(".loading").show();
        
        var versionId = $("#versaoSelect").val();
        var yearFab = $("#anoSelect").val();
  
        gerar_VehicleToken(versionId, yearFab);
    });    
    

    let tempToken = '';
    async function gerar_VehicleToken(versionId, yearFab){
        await $.ajax({
            url: `${suiv_url}/VehicleToken?versionId=${versionId}&year=${yearFab}`, 
            type: 'GET', 
            dataType: 'json', 
            headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
            success: function (data) {
                //console.log(`TOKEN: ${data.token}`);
                tempToken = data.token;
                gerar_Sets(data.token);
                $(".loading").hide(); 
            },  
            error: function (data) {
                //console.log(data);
                alert("Tempo limite de conexão com a API do SUIV excedido, tente novamente ou aguarde alguns minutos antes de realizar uma nova consulta");
                $(".loading").hide(); 
            } 
        });
    } 
    
    async function gerar_Sets(token){
        let result = [];
        let result_quantidade = [];
        $(".loading").show();
        await $.ajax({
            url: `${suiv_url}/Sets?vehicleToken=${token}`, 
            type: 'GET', 
            dataType: 'json', 
            headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
            success: function (data) {
                //console.log(data);
                result_quantidade = data.length;
                result = data;
                
                const selectElement = $("#partsSelect");
                selectElement.empty(); 
                
                selectElement.append($("<option>").attr("value", "").text("Selecione..."));
                data.forEach(function(parts) {
                    selectElement.append($("<option>").attr('value', parts.id).text(parts.description));
                });
                
                clearTimeout(verifica_conexao);
                $(".loading").hide(); 
            },   
            error: function (data) {
                alert("Tempo limite de conexão com a API do SUIV excedido, tente novamente ou aguarde alguns minutos antes de realizar uma nova consulta");
                $(".loading").hide(); 
            } 
        });
    }
    
    $('#partsSelect').on("change", async function() {
        $(".loading").show();
        $("#partsSelect-subgrupo").html(`<option value="" hidden>Selecione...</option>`);
        
        let partsSelect = $(this).val();
        await $.ajax({
            url: `${suiv_url}/Nicknames?vehicleToken=${tempToken}&setId=${partsSelect}`, type: 'GET', dataType: 'json', headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
            success: function (data) {
                for(let i of data){
                    $("#partsSelect-subgrupo").append(`<option value="${i.id}">${i.description}</option>`);
                }
                $(".loading").hide(); 
            },  
            error: function (data) {
                alert("Tempo limite de conexão com a API do SUIV excedido, tente novamente ou aguarde alguns minutos antes de realizar uma nova consulta");
                $(".loading").hide(); 
            } 
        });
    }); 
    
    $('#btnConsultar2').on('click', async function() {
        $(".loading").show();
        let marcaId = $("#marcaSelect").val();
        let modelId = $("#modeloSelect").val();
        let versionId = $("#versaoSelect").val();
        let yearFab = $("#anoSelect").val();
        let partsSelect = $("#partsSelect").val();
        let subgrupo = $("#partsSelect-subgrupo").val();
      
        if(`${marcaId}` == "") {
            alert("Favor selecionar uma Marca");
            $(".loading").hide();
        } else if(`${modelId}` == "") {
            alert("Favor selecionar uma Modelo");
            $(".loading").hide();
        } else if(`${versionId}` == "") {
            alert("Favor selecionar uma Versão");
            $(".loading").hide();
        } else if (`${yearFab}` == "") {
            alert("Favor selecionar Ano do veiculo");
            $(".loading").hide();
        } else if (`${partsSelect}` == "") {
            alert("Favor selecionar um grupo de peças");
            $(".loading").hide();
        } else if (`${subgrupo}` == "") {
            alert("Favor selecionar um subgrupo de peças");
            $(".loading").hide();
        } else{
            clearHTML();
            await $.ajax({
                url: `${suiv_url}/Parts?vehicleToken=${tempToken}&nicknameId=${subgrupo}`, type: 'GET', dataType: 'json', headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
                success: function (data) {
            		for(let i of data){ 
            		    let moeda = (i.price).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}); 
                        $(".pagina-consultar-placas-container-div").append(`${i.description} - <b>${moeda}</b><br><hr>`);
            		}
                    $(".loading").hide(); 
                },
                error: function (data) {
                    alert("Tempo limite de conexão com a API do SUIV excedido, tente novamente ou aguarde alguns minutos antes de realizar uma nova consulta");
                    $(".loading").hide(); 
                }  
            });
        }
    });
});