# Sistema de Dashboard Admin - Atrocidades

## Funcionalidades Implementadas

### 🎛️ Controles de Site

#### 1. Toggle de Uploads Públicos
- **Localização**: Admin Dashboard
- **Funcionalidade**: Controla se usuários não-admin podem fazer upload de vídeos
- **Estados**:
  - ✅ **Habilitado**: Todos os usuários podem fazer upload
  - ❌ **Desabilitado**: Apenas admins podem fazer upload
- **Backend**: Rotas de upload verificam a permissão via `SiteSetting::get('public_uploads_enabled')`

#### 2. Modo de Manutenção
- **Localização**: Admin Dashboard
- **Funcionalidade**: Coloca o site em modo de manutenção
- **Estados**:
  - ✅ **Ativado**: Site exibe página de manutenção para usuários comuns
  - ❌ **Desativado**: Site operando normalmente
- **Comportamento**:
  - Admins sempre podem acessar (bypass automático)
  - Usuários comuns são redirecionados para `/maintenance`
  - Página de manutenção estilizada com informações claras

### 📊 Analytics e Estatísticas

#### Métricas de Usuários
- Total de usuários cadastrados
- Usuários online (últimos 5 minutos)
- Usuários banidos/suspensos
- Total de admins

#### Métricas de Vídeos
- Total de vídeos no sistema
- Vídeos pendentes de moderação
- Vídeos aprovados
- Vídeos rejeitados
- Uploads de hoje

#### Métricas de Denúncias
- Total de denúncias
- Denúncias pendentes
- Denúncias revisadas
- Denúncias descartadas
- Denúncias de hoje
- **Breakdown por tipo**: Gráfico mostrando quantidade por categoria

### 🚨 Monitoramento em Tempo Real

#### Denúncias Pendentes
- Lista das 10 denúncias mais recentes aguardando revisão
- Informações exibidas:
  - Tipo de denúncia (spam, conteúdo inapropriado, etc.)
  - Vídeo reportado (com link)
  - Descrição opcional
  - Usuário que reportou
  - Tempo decorrido
- Ação rápida: Botão "Revisar" para moderar

#### Vídeos Aguardando Aprovação
- Grid com os 10 vídeos mais recentes pendentes
- Preview visual (thumbnail)
- Informações do autor
- Botão direto para moderação

#### Log de Atividades
- 15 atividades mais recentes do sistema
- Timestamp e descrição da ação
- Rastreamento de mudanças críticas

### ⚡ Ações Rápidas
Links diretos para:
- Moderação de vídeos
- Gerenciamento de usuários
- Logs de atividade detalhados

## 🔧 Tecnologias Utilizadas

### Backend
- **SiteSetting Model**: Sistema de configurações chave-valor com cache
- **Middleware CheckMaintenanceMode**: Intercepta requisições durante manutenção
- **Middleware UpdateLastSeen**: Rastreia atividade de usuários
- **Cache**: Otimização de queries frequentes (online users, settings)

### Banco de Dados
Novas tabelas:
- `site_settings`: Armazena configurações do sistema
- Nova coluna em `users`: `last_seen_at` para rastreamento

### Frontend
- Toggle switches animados para controles
- Design responsivo e moderno
- Feedback visual em tempo real
- AJAX para atualização de settings sem reload

## 📝 Rotas Adicionadas

```php
// Página de manutenção
GET /maintenance

// Toggle de configurações (Admin only)
POST /admin/settings/toggle
```

## 🔒 Segurança

1. **Proteção de Rotas**: Apenas admins podem acessar o dashboard
2. **Middleware Cascade**: `auth` -> `admin` -> `check.suspended`
3. **Bypass Inteligente**: Admins nunca são bloqueados pelo modo manutenção
4. **Logging**: Todas as mudanças de configuração são registradas
5. **Validação**: Inputs validados antes de salvar no banco

## 🚀 Como Usar

### Ativar Modo Manutenção
1. Acesse: `/admin` (Admin Dashboard)
2. Localize "🛠️ Modo Manutenção"
3. Clique no toggle
4. Confirme a mensagem

### Desabilitar Uploads Públicos
1. Acesse: `/admin`
2. Localize "🔒 Controle de Uploads"
3. Clique no toggle
4. Apenas admins poderão fazer upload

## 🔄 Atualizações Automáticas

- **Cache de 5min** para contadores de usuários online
- **Cache de 1h** para site settings
- **Atualização last_seen** a cada 5 minutos (otimizado)

## 📦 Migrations Necessárias

Execute para ativar as funcionalidades:

```bash
php artisan migrate
```

Migrations criadas:
- `2025_11_20_000000_create_site_settings_table.php`
- `2025_11_20_000001_add_last_seen_at_to_users_table.php`

## 🎯 Próximas Melhorias Sugeridas

- [ ] Dashboard de analytics com gráficos interativos
- [ ] Notificações push para denúncias críticas
- [ ] Sistema de backup automático
- [ ] Rate limiting configurável
- [ ] Blacklist de IPs
- [ ] Whitelist de domínios de email
- [ ] Agendamento de manutenção
