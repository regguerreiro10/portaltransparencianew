<?php

class PedidoVendaService
{
    public static function notificarVendedorPedido($pedido_venda)
    {
        $emailTemplate = new EmailTemplate(EmailTemplate::ATUALIZACAO);
        
        $titulo = $emailTemplate->titulo;
        $mensagem = $emailTemplate->mensagem;
        
        $titulo = $pedido_venda->render($titulo);
        $mensagem = $pedido_venda->render($mensagem);
        
        $user_id = $pedido_venda->system_users_id;

        $user = new SystemUsers($user_id);                           

        
        $notificationParam = [
            'key' => $pedido_venda->id
        ];
        $icon = 'fas fa-file-invoice-dollar';
        
        SystemNotification::register( $user_id, $titulo, $mensagem, new TAction(['PedidoVendaFormView', 'onShow'], $notificationParam), 'Visualizar Pedido', $icon);
        
        SendGridMailService::enviar($user->email, $titulo, $mensagem);
        
    }
    
    public static function notificarAprovador($pedido_venda)
    {
        $emailTemplate = new EmailTemplate(EmailTemplate::PEDIDO_AGUARDANDO_APROVACAO);
        
        $titulo = $emailTemplate->titulo;
        $mensagem = $emailTemplate->mensagem;
        
        $titulo = $pedido_venda->render($titulo);
        $mensagem = $pedido_venda->render($mensagem);
        
        $notificationParam = [
            'key' => $pedido_venda->id
        ];
        $icon = 'fas fa-file-invoice-dollar';
        
        
        $aprovadores = Aprovador::getAprovadorAtualFromPedidoVenda($pedido_venda);
        
        if($aprovadores)
        {
            foreach($aprovadores as $aprovador)    
            {
                $user_id = $aprovador->system_user_id;
        
                SystemNotification::register( $user_id, $titulo, $mensagem, new TAction(['PedidoVendaFormView', 'onShow'], $notificationParam), 'Visualizar Pedido', $icon);
                
                SendGridMailService::enviar($aprovador->system_user->email, $titulo, $mensagem);
            }
        }
        
        
        
    }
     public static function notificarAprovadorFrotas($pedido_frotas, $propostas)
    {
        $emailTemplate = new EmailTemplate(EmailTemplate::PEDIDO_AGUARDANDO_ORCAMENTO);
        
        $titulo = $emailTemplate->titulo;
        $mensagem = $emailTemplate->mensagem;
        
        $titulo = $pedido_frotas->render($titulo);
        $mensagem = $pedido_frotas->render($mensagem);
        
        $notificationParam = [
            'key' => $propostas->id
        ];
        $icon = 'fas fa-file-invoice-dollar';
         
        
        $redes = PedidoAsCliente::where('pedido_frotas_id','=', $pedido_frotas->id)->load();
    
        
        if($redes)
        {
            foreach($redes as $rede)    
            {
                $pessoa = new Pessoa($rede->pessoa_id);
        
                SystemNotification::registerpedidofrotas( $pessoa->system_user_id, $titulo, $mensagem, new TAction(['PropostasDisponiveisList', 'onShow'], $notificationParam), 'Visualizar Proposta', $icon);
                 
                MailService::send($pessoa->email, $titulo, $mensagem,  'html');

                //SendGridMailService::enviar($pessoa->email, $titulo, $mensagem);
            }
        }
        
        
        
    }
}
