$(document).ready(function(){
    console.log("start-suiv-api");
    
    $("body").append(`
        <style>
            .loading{ display: none; width: 100%; height: 100%; position: fixed; top: 0; z-index: 9999; background: rgba(0, 0, 0, 0.20);}
            .loading .engloba-loading{width: 100%;height: 100%;position: fixed;top: 0;z-index: 9999;display: flex;align-items: center;justify-content: center;}
            .loading .engloba-loading .square-center{ width: 100px; height: 100px; background: #0000006e; display: flex; align-items: center; justify-content: center; border-radius: 5px; flex-flow: wrap;}
            .loading .engloba-loading .square-center .lds-roller { display: inline-block; position: relative; width: 64px; height: 64px; }
            .loading .engloba-loading .square-center .lds-roller div { animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite; transform-origin: 32px 32px;}
            .loading .engloba-loading .square-center .lds-roller div:after { content: " "; display: block; position: absolute; width: 6px; height: 6px; border-radius: 50%; background: #fff; margin: -3px 0 0 -3px;}
            .loading .engloba-loading .square-center .lds-roller div:nth-child(1) { animation-delay: -0.036s; }
            .loading .engloba-loading .square-center .lds-roller div:nth-child(1):after { top: 50px; left: 50px;}
            .loading .engloba-loading .square-center .lds-roller div:nth-child(2) { animation-delay: -0.072s;}
            .loading .engloba-loading .square-center .lds-roller div:nth-child(2):after { top: 54px; left: 45px; }
            .loading .engloba-loading .square-center .lds-roller div:nth-child(3) { animation-delay: -0.108s; }
            .loading .engloba-loading .square-center .lds-roller div:nth-child(3):after { top: 57px; left: 39px;}
            .loading .engloba-loading .square-center .lds-roller div:nth-child(4) { animation-delay: -0.144s; }
            .loading .engloba-loading .square-center .lds-roller div:nth-child(4):after { top: 58px; left: 32px;}
            .loading .engloba-loading .square-center .lds-roller div:nth-child(5) { animation-delay: -0.18s;}
            .loading .engloba-loading .square-center .lds-roller div:nth-child(5):after { top: 57px; left: 25px; }
            .loading .engloba-loading .square-center .lds-roller div:nth-child(6) { animation-delay: -0.216s; }
            .loading .engloba-loading .square-center .lds-roller div:nth-child(6):after { top: 54px; left: 19px;}
            .loading .engloba-loading .square-center .lds-roller div:nth-child(7) { animation-delay: -0.252s;}
            .loading .engloba-loading .square-center .lds-roller div:nth-child(7):after { top: 50px; left: 14px;}
            .loading .engloba-loading .square-center .lds-roller div:nth-child(8) { animation-delay: -0.288s;}
            .loading .engloba-loading .square-center .lds-roller div:nth-child(8):after { top: 45px; left: 10px;}
            .loading .engloba-loading .square-center p{ float: left; width: 100%; text-align: center; color: #fff; margin: 0; font-size: 9px;}
            @keyframes lds-roller {
            	0% {transform: rotate(0deg);}
            	100% { transform: rotate(360deg);} 
            }
        </style> 
        <div class="loading">
            <div class="engloba-loading">
                <div class="square-center">
                    <div class="lds-roller">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <p class="loading-carregando">Carregando... 0/100</p>
                </div>
            </div>
        </div>
    `);
    
    
    let placa_veiculo = $(".placa-veiculo").attr("placa");
    placa_veiculo = placa_veiculo.split(" ").join("");
    placa_veiculo = placa_veiculo.split("-").join("");
    let suiv_url = $("body").attr("suiv-url");
    let suiv_authorization = $("body").attr("suiv-authorization");
    
    let pecas_nome = [];
    let pecas_id = [];
    
    verifica_produto_suiv();
    async function verifica_produto_suiv() {
        let produto_suiv = $(".produto-suiv").length;
        if(`${produto_suiv}` > "0"){
            $(".produto-suiv").each(function(){
                console.log($(this).attr('rel'));
                pecas_nome.push($(this).attr('rel'));
                pecas_id.push($(this).attr('id'));
            });
            await consultar_placa(placa_veiculo);
        }else{
            setTimeout(verifica_produto_suiv, 1000); 
        }
    }
    
    let array_pecas = [];
    let gerar_pecas_return = [];
    let gerar_nicknames_return = [];
    let gerar_VehicleToken_return = [];
    let consultar_placa_return = [];
    let retorno_funcao = [];
    let contador = 0;
    
    console.log(placa_veiculo);
    console.log(suiv_url);
    console.log(suiv_authorization);

    $(".loading").show();

    async function moedaBR(valor){
        valor = valor.toString().replace(/\D/g,"");
        valor = valor.toString().replace(/(\d)(\d{8})$/,"$1.$2");
        valor = valor.toString().replace(/(\d)(\d{5})$/,"$1.$2");
        valor = valor.toString().replace(/(\d)(\d{2})$/,"$1,$2");
        return valor;
    }  

    async function consultar_placa(placa){
        await $.ajax({
            url: `${suiv_url}/VehicleInfo/byplate?plate=${placa}`, type: 'GET', dataType: 'json', headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
            success: function (data) {
                console.log(data);
                let suiv = data.suivDataCollection[0];
                
                if(`${suiv}` != "undefined"){
                    let yearFab = data.yearFab;
                    gerar_VehicleToken(suiv.versionId, yearFab); 
                }else{
                    console.log("suivDataCollection vazio");
                    $(".loading").hide(); 
                }
            },   
            error: function (data) {
                $(".loading").hide(); 
                console.log("erro na busca da api");
                //console.log(data);
            } 
        });
    }
    
    async function gerar_VehicleToken(versionId, yearFab){
        await $.ajax({
            url: `${suiv_url}/VehicleToken?versionId=${versionId}&year=${yearFab}`, type: 'GET', dataType: 'json', headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
            success: function (data) {
                //console.log(data);
                console.log(`TOKEN: ${data.token}`);
                gerar_Sets(data.token);
            },  
            error: function (data) {
                $(".loading").hide(); 
                console.log("erro na busca da api");
                //console.log(data);
            } 
        });
    } 
    
    async function gerar_Sets(token){
        let result = [];
        let result_quantidade = [];
        await $.ajax({
            url: `${suiv_url}/Sets?vehicleToken=${token}`, type: 'GET', dataType: 'json', headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
            success: function (data) {
                result_quantidade = data.length;
                result = data;
            },   
            error: function (data) {
                $(".loading").hide(); 
                console.log("erro na busca da api");
                //console.log(data);
            } 
        });
        
        console.log(result_quantidade);
        let a = 0;
        for(let i of result){
            let gerar_nicknames_res = await gerar_Nicknames(token, i.id, i.description);
            $(".loading-carregando").html(`Carregando... ${a}/100`);
            if(gerar_nicknames_res['quantidade'] == result_quantidade){
                let arrays = gerar_nicknames_res['arrays'];
                arrays = arrays.filter(function(este, i) { return arrays.indexOf(este) === i; });
                console.log(arrays);
                
                let array_remove_repetidos_nome = [];
                let array_remove_repetidos_preco = [];
                
                for(let x of arrays){
                    if(array_remove_repetidos_nome.indexOf(x.description) !== -1){
                        
                    }else{
                        array_remove_repetidos_nome.push(x.description);
                        array_remove_repetidos_preco.push(x.price);
                    }
                }

                let a = 0;
                for(let pecas of pecas_nome){
                    let b = 0;
                    for(let pecasAPI of array_remove_repetidos_nome){
                        if(`${pecas}` == `${pecasAPI}`){
                            $(`.produto-suiv-${pecas_id[a]}`).html(` | valor da peça genuína: R$ ${array_remove_repetidos_preco[b].toLocaleString('pt-br', {minimumFractionDigits: 2}) }`);
                        }
                        b++;
                    } 
                    a++;
                }
                $(".loading").hide();
            }else{
                console.log("carregando...");
            }
            
            a++;
        }
    }
    
    async function gerar_Nicknames(token, setId, nickname){
        let result = [];
        let array_gerar_Nicknames = [];
        await $.ajax({
            url: `${suiv_url}/Nicknames?vehicleToken=${token}&setId=${setId}`, type: 'GET', dataType: 'json', headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
            success: function (data) {
                result = data;
                gerar_nicknames_return.push(data);
            },  
            error: function (data) {
                $(".loading").hide(); 
                console.log("erro na busca da api");
                //console.log(data);
            } 
        });
        for(let i of result){
            array_gerar_Nicknames['nickname'] = i.description;
            array_gerar_Nicknames['quantidade'] = gerar_nicknames_return.length;
            array_gerar_Nicknames['arrays'] = await gerar_pecas(token, i.id, i.description);
        } 
        return array_gerar_Nicknames;
    }  
    
    async function gerar_pecas(token, id_peca, nome_peca){
        let array_pecas_selecionadas = [];
        await $.ajax({
            url: `${suiv_url}/Parts?vehicleToken=${token}&nicknameId=${id_peca}`, type: 'GET', dataType: 'json', headers: {"Authorization": `${suiv_authorization}`, "Content-Type": "application/json"},
            success: function (data) {
                if(`${data}` != ""){
                    for(let i of data){
                        if(pecas_nome.indexOf(i.description) !== -1){
                            array_pecas_selecionadas['description'] = i.description;
                            if(`${i.price}` != "undefined"){
                               array_pecas_selecionadas['price'] = i.price; 
                            }
                        	array_pecas.push(array_pecas_selecionadas);
                        }
                    }
                }
            },  
            error: function (data) {
                $(".loading").hide(); 
                console.log("erro na busca da api");
                //console.log(data);
            }  
        });
        return array_pecas;
    } 

});