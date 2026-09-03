<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    private function header(): string
    {
        return '
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9; padding:30px 0;">
            <tr>
                <td align="center">
                    <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                        <tr>
                            <td style="background-color:#0057ff; padding:24px 40px; text-align:center;">
                                <h1 style="margin:0; color:#ffffff; font-family:Arial,Helvetica,sans-serif; font-size:22px; font-weight:700; letter-spacing:1px;">NUVEX GLOBAL</h1>
                                <p style="margin:4px 0 0; color:#a3c4ff; font-family:Arial,Helvetica,sans-serif; font-size:12px;">Plataforma de Gestão de Serviços</p>
                            </td>
                        </tr>';
    }

    private function footer(): string
    {
        return '
                        <tr>
                            <td style="background-color:#f8f9fb; padding:24px 40px; text-align:center; border-top:1px solid #e8ecf1;">
                                <p style="margin:0 0 8px; color:#6b7280; font-family:Arial,Helvetica,sans-serif; font-size:12px;">
                                    &copy; ' . date('Y') . ' NUVEX GLOBAL. Todos os direitos reservados.
                                </p>
                                <p style="margin:0 0 8px; color:#6b7280; font-family:Arial,Helvetica,sans-serif; font-size:11px;">
                                    Este é um e-mail automático. Por favor, não responda diretamente.
                                </p>
                                <p style="margin:0; color:#9ca3af; font-family:Arial,Helvetica,sans-serif; font-size:11px;">
                                    <a href="{{platform_url}}" style="color:#0057ff; text-decoration:none;">nuvex.ao</a>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>';
    }

    private function wrap(string $bodyHtml): string
    {
        return '
        <!DOCTYPE html>
        <html lang="pt">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>NUVEX GLOBAL</title>
        </head>
        <body style="margin:0; padding:0; background-color:#f4f6f9; font-family:Arial,Helvetica,sans-serif;">
            ' . $this->header() . $bodyHtml . $this->footer() . '
        </body>
        </html>';
    }

    private function bodyBlock(string $title, string $message, ?string $buttonText = null, ?string $buttonUrl = null, ?string $extraInfo = null): string
    {
        $html = '
                        <tr>
                            <td style="padding:32px 40px 16px;">
                                <h2 style="margin:0 0 12px; color:#1a1a2e; font-family:Arial,Helvetica,sans-serif; font-size:20px; font-weight:700;">' . $title . '</h2>
                                <p style="margin:0 0 16px; color:#4b5563; font-family:Arial,Helvetica,sans-serif; font-size:15px; line-height:1.6;">' . $message . '</p>';

        if ($buttonText && $buttonUrl) {
            $html .= '
                                <table cellpadding="0" cellspacing="0" style="margin:20px 0;">
                                    <tr>
                                        <td style="background-color:#0057ff; border-radius:6px;">
                                            <a href="' . $buttonUrl . '" style="display:inline-block; padding:12px 32px; color:#ffffff; font-family:Arial,Helvetica,sans-serif; font-size:14px; font-weight:600; text-decoration:none;">' . $buttonText . '</a>
                                        </td>
                                    </tr>
                                </table>';
        }

        if ($extraInfo) {
            $html .= '
                                <div style="background-color:#f0f4ff; border-left:4px solid #0057ff; padding:16px 20px; margin:20px 0; border-radius:0 6px 6px 0;">
                                    <p style="margin:0; color:#374151; font-family:Arial,Helvetica,sans-serif; font-size:14px; line-height:1.5;">' . $extraInfo . '</p>
                                </div>';
        }

        $html .= '
                            </td>
                        </tr>';

        return $html;
    }

    public function run(): void
    {
        $templates = [
            [
                'slug' => 'account.created',
                'name' => 'Conta Criada',
                'subject' => 'Bem-vindo à NUVEX GLOBAL - Sua Conta foi Criada',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Bem-vindo(a), {{user_name}}!',
                    'A sua conta na <strong>NUVEX GLOBAL</strong> foi criada com sucesso. Agora pode aceder à plataforma e gerir todos os seus serviços de internet de forma simples e segura.',
                    'Aceder à Plataforma',
                    '{{platform_url}}/login',
                    '<strong>Dados da sua conta:</strong><br>E-mail: {{user_email}}<br><br>Recomendamos que altere a sua senha no primeiro acesso por questões de segurança.'
                )),
                'body_text' => 'Bem-vindo(a), {{user_name}}! A sua conta na NUVEX GLOBAL foi criada com sucesso. Aceda a {{platform_url}}/login para começar.',
                'variables' => ['user_name', 'user_email', 'platform_url'],
            ],
            [
                'slug' => 'order.received',
                'name' => 'Pedido Recebido',
                'subject' => 'Pedido #{{order_number}} Recebido - NUVEX GLOBAL',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Pedido Recebido',
                    'Olá <strong>{{user_name}}</strong>, recebemos o seu pedido <strong>#{{order_number}}</strong> com sucesso. Seguem os detalhes:',
                    'Ver Pedido',
                    '{{platform_url}}/orders/{{order_id}}',
                    '<strong>Resumo do Pedido:</strong><br>Número: #{{order_number}}<br>Serviço: {{service_name}}<br>Valor: {{order_total}} Kz<br>Data: {{order_date}}<br><br>Prossiga com o pagamento para ativação do serviço.'
                )),
                'body_text' => 'Olá {{user_name}}, recebemos o seu pedido #{{order_number}}. Valor: {{order_total}} Kz. Acesse {{platform_url}}/orders/{{order_id}} para mais detalhes.',
                'variables' => ['user_name', 'order_number', 'order_id', 'service_name', 'order_total', 'order_date', 'platform_url'],
            ],
            [
                'slug' => 'order.paid',
                'name' => 'Pagamento Confirmado',
                'subject' => 'Pagamento Confirmado - Pedido #{{order_number}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Pagamento Confirmado!',
                    'O pagamento do pedido <strong>#{{order_number}}</strong> foi confirmado com sucesso. O seu serviço está sendo processado e será ativado em breve.',
                    'Ver Detalhes',
                    '{{platform_url}}/orders/{{order_id}}',
                    '<strong>Detalhes do Pagamento:</strong><br>Pedido: #{{order_number}}<br>Valor Pago: {{order_total}} Kz<br>Método: {{payment_method}}<br>Data: {{payment_date}}'
                )),
                'body_text' => 'O pagamento do pedido #{{order_number}} foi confirmado. Valor: {{order_total}} Kz. O seu serviço será ativado em breve.',
                'variables' => ['user_name', 'order_number', 'order_id', 'order_total', 'payment_method', 'payment_date', 'platform_url'],
            ],
            [
                'slug' => 'order.pending',
                'name' => 'Pagamento Pendente',
                'subject' => 'Pagamento Pendente - Pedido #{{order_number}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Pagamento Pendente',
                    'O pedido <strong>#{{order_number}}</strong> foi registado mas ainda aguarda confirmação do pagamento. Utilize a referência indicada para efetuar o pagamento.',
                    'Efetuar Pagamento',
                    '{{platform_url}}/orders/{{order_id}}',
                    '<strong>Instruções de Pagamento:</strong><br>Pedido: #{{order_number}}<br>Valor: {{order_total}} Kz<br>Referência: {{payment_reference}}<br>Método: {{payment_method}}<br><br>O pagamento deve ser efetuado no prazo de 48 horas. Após este prazo, o pedido será automaticamente cancelado.'
                )),
                'body_text' => 'O pedido #{{order_number}} aguarda pagamento. Valor: {{order_total}} Kz. Referência: {{payment_reference}}. Efetue o pagamento no prazo de 48 horas.',
                'variables' => ['user_name', 'order_number', 'order_id', 'order_total', 'payment_reference', 'payment_method', 'platform_url'],
            ],
            [
                'slug' => 'order.failed',
                'name' => 'Pagamento Falhado',
                'subject' => 'Pagamento Não Confirmado - Pedido #{{order_number}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Pagamento Não Confirmado',
                    'Não foi possível confirmar o pagamento do pedido <strong>#{{order_number}}</strong>. Verifique os dados utilizados para o pagamento ou tente novamente.',
                    'Tentar Novamente',
                    '{{platform_url}}/orders/{{order_id}}',
                    '<strong>Detalhes:</strong><br>Pedido: #{{order_number}}<br>Valor: {{order_total}} Kz<br><br>Se já efetuou o pagamento, entre em contato com o nosso suporte para verificação.'
                )),
                'body_text' => 'Pagamento do pedido #{{order_number}} não confirmado. Verifique os dados ou tente novamente. Se já pagou, contacte o suporte.',
                'variables' => ['user_name', 'order_number', 'order_id', 'order_total', 'platform_url'],
            ],
            [
                'slug' => 'order.processing',
                'name' => 'Pedido em Processamento',
                'subject' => 'Pedido #{{order_number}} em Processamento',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Pedido em Processamento',
                    'O pagamento do pedido <strong>#{{order_number}}</strong> foi confirmado e o seu serviço está em processamento. A ativação ocorrerá em breve.',
                    'Acompanhar Pedido',
                    '{{platform_url}}/orders/{{order_id}}',
                    '<strong>Estado do Pedido:</strong><br>Pedido: #{{order_number}}<br>Serviço: {{service_name}}<br>Estado: Em Processamento<br><br>Agradecemos a sua paciência. Receberá uma notificação assim que o serviço for ativado.'
                )),
                'body_text' => 'O pedido #{{order_number}} está em processamento. O serviço será ativado em breve. Acompanhe em {{platform_url}}/orders/{{order_id}}.',
                'variables' => ['user_name', 'order_number', 'order_id', 'service_name', 'platform_url'],
            ],
            [
                'slug' => 'order.completed',
                'name' => 'Pedido Concluído',
                'subject' => 'Pedido #{{order_number}} Concluído com Sucesso',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Pedido Concluído!',
                    'O seu pedido <strong>#{{order_number}}</strong> foi processado com sucesso. O serviço <strong>{{service_name}}</strong> já está ativo e pronto a utilizar.',
                    'Aceder ao Serviço',
                    '{{platform_url}}/services/{{service_id}}',
                    '<strong>Resumo:</strong><br>Pedido: #{{order_number}}<br>Serviço: {{service_name}}<br>Estado: Concluído<br><br>Pode aceder a todos os detalhes e credenciais do serviço na plataforma.'
                )),
                'body_text' => 'O pedido #{{order_number}} foi concluído. O serviço {{service_name}} já está ativo. Acesse {{platform_url}}/services/{{service_id}} para mais detalhes.',
                'variables' => ['user_name', 'order_number', 'order_id', 'service_name', 'service_id', 'platform_url'],
            ],
            [
                'slug' => 'order.cancelled',
                'name' => 'Pedido Cancelado',
                'subject' => 'Pedido #{{order_number}} Cancelado',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Pedido Cancelado',
                    'O pedido <strong>#{{order_number}}</strong> foi cancelado. Se utiliza um serviço pago, o mesmo foi desativado.',
                    'Criar Novo Pedido',
                    '{{platform_url}}/plans',
                    '<strong>Detalhes:</strong><br>Pedido: #{{order_number}}<br>Valor: {{order_total}} Kz<br><br>Se tem dúvidas sobre o cancelamento, entre em contato com o nosso suporte.'
                )),
                'body_text' => 'O pedido #{{order_number}} foi cancelado. Se tiver dúvidas, contacte o suporte em {{platform_url}}.',
                'variables' => ['user_name', 'order_number', 'order_id', 'order_total', 'platform_url'],
            ],
            [
                'slug' => 'service.activated',
                'name' => 'Serviço Ativado',
                'subject' => 'Serviço Ativado - {{service_name}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Serviço Ativado!',
                    'O seu serviço <strong>{{service_name}}</strong> foi ativado com sucesso. Pode agora utilizar todos os recursos incluídos no seu plano.',
                    'Ver Credenciais',
                    '{{platform_url}}/services/{{service_id}}',
                    '<strong>Detalhes do Serviço:</strong><br>Serviço: {{service_name}}<br>Plano: {{plan_name}}<br>Validade: {{expiry_date}}<br><br>As credenciais de acesso estão disponíveis na área de cliente.'
                )),
                'body_text' => 'O serviço {{service_name}} foi ativado com sucesso. Plano: {{plan_name}}. Validade: {{expiry_date}}. Acesse as credenciais em {{platform_url}}/services/{{service_id}}.',
                'variables' => ['user_name', 'service_name', 'service_id', 'plan_name', 'expiry_date', 'platform_url'],
            ],
            [
                'slug' => 'service.suspended',
                'name' => 'Serviço Suspenso',
                'subject' => 'Serviço Suspenso - {{service_name}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Serviço Suspenso',
                    'O seu serviço <strong>{{service_name}}</strong> foi suspenso. Para reativá-lo, regularize a situação do pagamento.',
                    'Regularizar Pagamento',
                    '{{platform_url}}/billing',
                    '<strong>Detalhes:</strong><br>Serviço: {{service_name}}<br>Motivo: {{suspension_reason}}<br><br>O serviço permanecerá suspenso até que o pagamento seja confirmado. Após 30 dias sem regularização, o serviço será cancelado automaticamente.'
                )),
                'body_text' => 'O serviço {{service_name}} foi suspenso. Motivo: {{suspension_reason}}. Regularize o pagamento em {{platform_url}}/billing para reativar.',
                'variables' => ['user_name', 'service_name', 'service_id', 'suspension_reason', 'platform_url'],
            ],
            [
                'slug' => 'service.expired',
                'name' => 'Serviço Expirado',
                'subject' => 'Serviço Expirado - {{service_name}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Serviço Expirado',
                    'O seu serviço <strong>{{service_name}}</strong> expirou em <strong>{{expiry_date}}</strong>. Para manter o serviço ativo, renove o mais rápido possível.',
                    'Renovar Serviço',
                    '{{platform_url}}/services/{{service_id}}/renew',
                    '<strong>Detalhes:</strong><br>Serviço: {{service_name}}<br>Expirado em: {{expiry_date}}<br><br>Após a expiração, os dados serão mantidos por 30 dias. Após este período, serão eliminados permanentemente.'
                )),
                'body_text' => 'O serviço {{service_name}} expirou em {{expiry_date}}. Renove em {{platform_url}}/services/{{service_id}}/renew. Os dados serão mantidos por 30 dias.',
                'variables' => ['user_name', 'service_name', 'service_id', 'expiry_date', 'platform_url'],
            ],
            [
                'slug' => 'service.renewed',
                'name' => 'Serviço Renovado',
                'subject' => 'Serviço Renovado - {{service_name}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Serviço Renovado!',
                    'O seu serviço <strong>{{service_name}}</strong> foi renovado com sucesso. A nova validade é até <strong>{{new_expiry_date}}</strong>.',
                    'Ver Serviço',
                    '{{platform_url}}/services/{{service_id}}',
                    '<strong>Detalhes da Renovação:</strong><br>Serviço: {{service_name}}<br>Nova Validade: {{new_expiry_date}}<br>Valor: {{renewal_amount}} Kz<br><br>Obrigado por continuar connosco!'
                )),
                'body_text' => 'O serviço {{service_name}} foi renovado. Nova validade: {{new_expiry_date}}. Valor: {{renewal_amount}} Kz.',
                'variables' => ['user_name', 'service_name', 'service_id', 'new_expiry_date', 'renewal_amount', 'platform_url'],
            ],
            [
                'slug' => 'service.transferred',
                'name' => 'Serviço Transferido',
                'subject' => 'Serviço Transferido - {{service_name}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Serviço Transferido',
                    'O serviço <strong>{{service_name}}</strong> foi transferido para a conta de <strong>{{new_owner_name}}</strong> com sucesso.',
                    'Ver Detalhes',
                    '{{platform_url}}/services',
                    '<strong>Detalhes da Transferência:</strong><br>Serviço: {{service_name}}<br>Novo Titular: {{new_owner_name}}<br>Data: {{transfer_date}}<br><br>A partir de agora, o serviço será gerido pela nova conta.'
                )),
                'body_text' => 'O serviço {{service_name}} foi transferido para {{new_owner_name}}. Data: {{transfer_date}}.',
                'variables' => ['user_name', 'service_name', 'service_id', 'new_owner_name', 'transfer_date', 'platform_url'],
            ],
            [
                'slug' => 'domain.registered',
                'name' => 'Domínio Registado',
                'subject' => 'Domínio Registado - {{domain_name}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Domínio Registado!',
                    'O domínio <strong>{{domain_name}}</strong> foi registado com sucesso e está pronto a utilizar.',
                    'Gerir Domínio',
                    '{{platform_url}}/domains/{{domain_id}}',
                    '<strong>Detalhes do Domínio:</strong><br>Domínio: {{domain_name}}<br>Registado em: {{registration_date}}<br>Expira em: {{expiry_date}}<br>Registar: {{registrar}}<br><br>Configure os name servers ou os registos DNS na área de cliente.'
                )),
                'body_text' => 'O domínio {{domain_name}} foi registado com sucesso. Expira em {{expiry_date}}. Configure em {{platform_url}}/domains/{{domain_id}}.',
                'variables' => ['user_name', 'domain_name', 'domain_id', 'registration_date', 'expiry_date', 'registrar', 'platform_url'],
            ],
            [
                'slug' => 'domain.configured',
                'name' => 'Domínio Configurado',
                'subject' => 'Domínio Configurado - {{domain_name}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Domínio Configurado',
                    'A configuração DNS do domínio <strong>{{domain_name}}</strong> foi atualizada com sucesso. As alterações podem levar até 48 horas para propagar totalmente.',
                    'Ver Configuração',
                    '{{platform_url}}/domains/{{domain_id}}',
                    '<strong>Configuração Aplicada:</strong><br>Domínio: {{domain_name}}<br><br>Os registos DNS foram atualizados. A propagação completa pode demorar até 48 horas dependendo do seu ISP.'
                )),
                'body_text' => 'A configuração DNS do domínio {{domain_name}} foi atualizada. A propagação pode demorar até 48 horas.',
                'variables' => ['user_name', 'domain_name', 'domain_id', 'platform_url'],
            ],
            [
                'slug' => 'domain.ns_updated',
                'name' => 'Name Servers Atualizadas',
                'subject' => 'Name Servers Atualizadas - {{domain_name}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Name Servers Atualizadas',
                    'As name servers do domínio <strong>{{domain_name}}</strong> foram atualizadas com sucesso.',
                    'Ver Domínio',
                    '{{platform_url}}/domains/{{domain_id}}',
                    '<strong>Novas Name Servers:</strong><br>' . '{{ns_1}}<br>{{ns_2}}<br><br>A alteração de name servers pode levar até 48 horas para propagar. Durante este período, o domínio pode ficar temporariamente inacessível.'
                )),
                'body_text' => 'As name servers do domínio {{domain_name}} foram atualizadas: {{ns_1}}, {{ns_2}}. A propagação pode levar até 48 horas.',
                'variables' => ['user_name', 'domain_name', 'domain_id', 'ns_1', 'ns_2', 'platform_url'],
            ],
            [
                'slug' => 'domain.expiring',
                'name' => 'Domínio Próximo da Expiração',
                'subject' => 'Aviso: Domínio {{domain_name}} Expira em {{days_remaining}} Dias',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Aviso de Expiração do Domínio',
                    'O domínio <strong>{{domain_name}}</strong> irá expirar em <strong>{{expiry_date}}</strong> ({{days_remaining}} dias). Renove para evitar a perda do domínio.',
                    'Renovar Domínio',
                    '{{platform_url}}/domains/{{domain_id}}/renew',
                    '<strong>Detalhes:</strong><br>Domínio: {{domain_name}}<br>Expira em: {{expiry_date}}<br>Dias Restantes: {{days_remaining}}<br><br>Após a expiração, o domínio ficará disponível para registo por terceiros após o período de carência de 30 dias.'
                )),
                'body_text' => 'O domínio {{domain_name}} expira em {{expiry_date}} ({{days_remaining}} dias). Renove em {{platform_url}}/domains/{{domain_id}}/renew para evitar a perda.',
                'variables' => ['user_name', 'domain_name', 'domain_id', 'expiry_date', 'days_remaining', 'platform_url'],
            ],
            [
                'slug' => 'hosting.activated',
                'name' => 'Hospedagem Ativada',
                'subject' => 'Hospedagem Ativada - {{hosting_name}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Hospedagem Ativada!',
                    'O serviço de hospedagem <strong>{{hosting_name}}</strong> foi ativado com sucesso. Seguem as credenciais de acesso.',
                    'Aceder ao Painel',
                    '{{panel_url}}',
                    '<strong>Credenciais de Acesso:</strong><br>Servidor: {{server_name}}<br>IP: {{server_ip}}<br>Painel: {{panel_url}}<br>Utilizador: {{panel_username}}<br><br><strong>⚠️ Recomendação:</strong> Altere a senha do painel no primeiro acesso por questões de segurança.'
                )),
                'body_text' => 'A hospedagem {{hosting_name}} foi ativada. Servidor: {{server_name}}. IP: {{server_ip}}. Acesse o painel em {{panel_url}}.',
                'variables' => ['user_name', 'hosting_name', 'hosting_id', 'server_name', 'server_ip', 'panel_url', 'panel_username', 'platform_url'],
            ],
            [
                'slug' => 'hosting.credentials',
                'name' => 'Acesso Disponível',
                'subject' => 'Credenciais de Acesso - {{hosting_name}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Credenciais de Acesso Disponíveis',
                    'As credenciais de acesso do serviço de hospedagem <strong>{{hosting_name}}</strong> estão disponíveis.',
                    'Ver Credenciais',
                    '{{platform_url}}/services/{{hosting_id}}',
                    '<strong>Credenciais:</strong><br>Servidor: {{server_name}}<br>IP: {{server_ip}}<br>Painel: {{panel_url}}<br>Utilizador: {{panel_username}}<br>Senha: {{panel_password}}<br><br><strong>⚠️ IMPORTANTE:</strong> Guarde estas credenciais em local seguro. Recomendamos alterar a senha após o primeiro acesso.'
                )),
                'body_text' => 'Credenciais da hospedagem {{hosting_name}}: Servidor {{server_name}}, IP {{server_ip}}. Acesse {{panel_url}} com o utilizador {{panel_username}}.',
                'variables' => ['user_name', 'hosting_name', 'hosting_id', 'server_name', 'server_ip', 'panel_url', 'panel_username', 'panel_password', 'platform_url'],
            ],
            [
                'slug' => 'hosting.expiring',
                'name' => 'Hospedagem Próxima da Expiração',
                'subject' => 'Aviso: Hospedagem {{hosting_name}} Expira em {{days_remaining}} Dias',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Aviso de Expiração da Hospedagem',
                    'O serviço de hospedagem <strong>{{hosting_name}}</strong> irá expirar em <strong>{{expiry_date}}</strong> ({{days_remaining}} dias). Renove para evitar a suspensão.',
                    'Renovar Hospedagem',
                    '{{platform_url}}/services/{{hosting_id}}/renew',
                    '<strong>Detalhes:</strong><br>Serviço: {{hosting_name}}<br>Expira em: {{expiry_date}}<br>Dias Restantes: {{days_remaining}}<br><br>Após a expiração, o serviço será suspenso por 30 dias antes de ser eliminado. Todos os dados serão perdidos.'
                )),
                'body_text' => 'A hospedagem {{hosting_name}} expira em {{expiry_date}} ({{days_remaining}} dias). Renove para evitar a suspensão e perda de dados.',
                'variables' => ['user_name', 'hosting_name', 'hosting_id', 'expiry_date', 'days_remaining', 'platform_url'],
            ],
            [
                'slug' => 'email.activated',
                'name' => 'Serviço de Email Ativado',
                'subject' => 'Serviço de Email Ativado - {{email_service_name}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Serviço de Email Ativado!',
                    'O serviço de email <strong>{{email_service_name}}</strong> foi ativado com sucesso. Pode agora criar contas de email no domínio.',
                    'Criar Contas de Email',
                    '{{platform_url}}/emails/{{email_service_id}}',
                    '<strong>Detalhes do Serviço:</strong><br>Serviço: {{email_service_name}}<br>Plano: {{plan_name}}<br>Armazenamento: {{storage_limit}}<br>Contas: {{accounts_limit}}<br><br>Pode criar contas de email até ao limite do seu plano na área de cliente.'
                )),
                'body_text' => 'O serviço de email {{email_service_name}} foi ativado. Plano: {{plan_name}}. Armazenamento: {{storage_limit}}. Crie contas em {{platform_url}}/emails/{{email_service_id}}.',
                'variables' => ['user_name', 'email_service_name', 'email_service_id', 'plan_name', 'storage_limit', 'accounts_limit', 'platform_url'],
            ],
            [
                'slug' => 'email.account_created',
                'name' => 'Conta de Email Criada',
                'subject' => 'Conta de Email Criada - {{email_address}}',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Conta de Email Criada!',
                    'A conta de email <strong>{{email_address}}</strong> foi criada com sucesso. Seguem as credenciais de acesso.',
                    'Aceder ao Email',
                    '{{webmail_url}}',
                    '<strong>Credenciais de Acesso:</strong><br>Email: {{email_address}}<br>Senha: {{email_password}}<br>Webmail: {{webmail_url}}<br>Servidor IMAP: {{imap_server}}<br>Servidor SMTP: {{smtp_server}}<br><br><strong>⚠️ IMPORTANTE:</strong> Altere a senha após o primeiro acesso. Configure o email no seu dispositivo com os dados acima.'
                )),
                'body_text' => 'Conta de email {{email_address}} criada. Webmail: {{webmail_url}}. IMAP: {{imap_server}}. SMTP: {{smtp_server}}. Altere a senha no primeiro acesso.',
                'variables' => ['user_name', 'email_address', 'email_password', 'webmail_url', 'imap_server', 'smtp_server', 'platform_url'],
            ],
            [
                'slug' => 'ticket.created',
                'name' => 'Ticket Recebido',
                'subject' => 'Ticket #{{ticket_number}} Recebido - NUVEX GLOBAL',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Ticket Recebido',
                    'O seu ticket <strong>#{{ticket_number}}</strong> foi criado com sucesso. A nossa equipa de suporte analisará o seu pedido e responderá em breve.',
                    'Ver Ticket',
                    '{{platform_url}}/tickets/{{ticket_id}}',
                    '<strong>Detalhes do Ticket:</strong><br>Número: #{{ticket_number}}<br>Assunto: {{ticket_subject}}<br>Categoria: {{ticket_category}}<br>Prioridade: {{ticket_priority}}<br><br>Tempo estimado de resposta: até 24 horas. Pode adicionar mais informações respondendo ao ticket na plataforma.'
                )),
                'body_text' => 'O ticket #{{ticket_number}} foi criado. Assunto: {{ticket_subject}}. Responderemos em até 24 horas. Acompanhe em {{platform_url}}/tickets/{{ticket_id}}.',
                'variables' => ['user_name', 'ticket_number', 'ticket_id', 'ticket_subject', 'ticket_category', 'ticket_priority', 'platform_url'],
            ],
            [
                'slug' => 'ticket.replied',
                'name' => 'Ticket Respondido',
                'subject' => 'Resposta ao Ticket #{{ticket_number}} - NUVEX GLOBAL',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Ticket Respondido',
                    'O ticket <strong>#{{ticket_number}}</strong> recebeu uma nova resposta da nossa equipa de suporte.',
                    'Ver Resposta',
                    '{{platform_url}}/tickets/{{ticket_id}}',
                    '<strong>Detalhes:</strong><br>Ticket: #{{ticket_number}}<br>Assunto: {{ticket_subject}}<br>Respondido por: {{responder_name}}<br><br>Leia a resposta completa e, se necessário, responda diretamente no ticket.'
                )),
                'body_text' => 'O ticket #{{ticket_number}} foi respondido por {{responder_name}}. Leia a resposta em {{platform_url}}/tickets/{{ticket_id}}.',
                'variables' => ['user_name', 'ticket_number', 'ticket_id', 'ticket_subject', 'responder_name', 'platform_url'],
            ],
            [
                'slug' => 'ticket.closed',
                'name' => 'Ticket Concluído',
                'subject' => 'Ticket #{{ticket_number}} Concluído - NUVEX GLOBAL',
                'body_html' => $this->wrap($this->bodyBlock(
                    'Ticket Concluído',
                    'O ticket <strong>#{{ticket_number}}</strong> foi marcado como concluído. Se ainda precisar de assistência, pode reabrir o ticket ou criar um novo.',
                    'Criar Novo Ticket',
                    '{{platform_url}}/tickets/create',
                    '<strong>Resumo:</strong><br>Ticket: #{{ticket_number}}<br>Assunto: {{ticket_subject}}<br>Estado: Concluído<br><br>Agradecemos o contacto. Avalie a qualidade do atendimento na plataforma.'
                )),
                'body_text' => 'O ticket #{{ticket_number}} foi concluído. Se precisar de mais ajuda, crie um novo ticket em {{platform_url}}/tickets/create.',
                'variables' => ['user_name', 'ticket_number', 'ticket_id', 'ticket_subject', 'platform_url'],
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                [
                    'name' => $template['name'],
                    'subject' => $template['subject'],
                    'body_html' => $template['body_html'],
                    'body_text' => $template['body_text'],
                    'variables' => $template['variables'],
                ]
            );
        }
    }
}
