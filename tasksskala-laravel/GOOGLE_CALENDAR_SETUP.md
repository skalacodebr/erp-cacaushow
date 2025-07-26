# Configuração da Integração com Google Calendar

## Pré-requisitos

1. Uma conta Google
2. Acesso ao Google Cloud Console

## Passos para Configuração

### 1. Criar um Projeto no Google Cloud Console

1. Acesse [Google Cloud Console](https://console.cloud.google.com/)
2. Clique em "Criar Projeto"
3. Dê um nome ao projeto (ex: "TasksSkala Calendar Integration")
4. Clique em "Criar"

### 2. Habilitar a API do Google Calendar

1. No menu lateral, vá para "APIs e Serviços" > "Biblioteca"
2. Procure por "Google Calendar API"
3. Clique na API e depois em "Ativar"

### 3. Criar Credenciais OAuth 2.0

1. Vá para "APIs e Serviços" > "Credenciais"
2. Clique em "Criar Credenciais" > "ID do cliente OAuth"
3. Se solicitado, configure a tela de consentimento OAuth:
   - Escolha "Externo" se for para uso geral
   - Preencha as informações obrigatórias
   - Adicione os escopos: `calendar.readonly` e `calendar.events.readonly`
4. Volte para criar as credenciais:
   - Tipo de aplicativo: "Aplicativo da Web"
   - Nome: "TasksSkala Web Client"
   - URIs de redirecionamento autorizados: 
     - `http://localhost:8000/auth/google/callback` (desenvolvimento)
     - `https://seudominio.com/auth/google/callback` (produção)
5. Clique em "Criar"
6. Copie o Client ID e Client Secret

### 4. Configurar o Arquivo .env

Adicione as seguintes variáveis ao seu arquivo `.env`:

```env
GOOGLE_CLIENT_ID=seu_client_id_aqui
GOOGLE_CLIENT_SECRET=seu_client_secret_aqui
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
GOOGLE_CALENDAR_ID=primary
```

### 5. Executar as Migrations

Se ainda não executou:

```bash
php artisan migrate
```

## Como Usar

### Para o Colaborador

1. Acesse o Dashboard
2. Na seção "Agendamentos do Google Calendar", clique em "Conectar Google Calendar"
3. Você será redirecionado para o Google para autorizar o acesso
4. Após autorizar, você voltará ao dashboard e verá seus próximos agendamentos

### Funcionalidades

- **Visualização de Eventos**: Mostra os próximos 10 eventos dos próximos 30 dias
- **Detalhes do Evento**: Exibe título, local, data e horário
- **Link Direto**: Cada evento tem um link para visualizar no Google Calendar
- **Desconectar**: Opção para remover a integração a qualquer momento

## Solução de Problemas

### Erro "Invalid redirect URI"

Verifique se a URI configurada no Google Cloud Console corresponde exatamente à configurada no `.env`.

### Erro "Access blocked"

Certifique-se de que o app está em produção no Google Cloud Console ou adicione usuários de teste se estiver em desenvolvimento.

### Tokens Expirados

O sistema automaticamente renova tokens expirados usando o refresh token. Se houver problemas, o usuário pode desconectar e reconectar.

## Segurança

- Os tokens de acesso são armazenados criptografados no banco de dados
- Cada colaborador tem seus próprios tokens
- Os tokens podem ser revogados a qualquer momento
- A aplicação solicita apenas permissões de leitura do calendário