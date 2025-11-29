# 🔒 Documentação de Segurança - Toggles de Controle do Site

## Visão Geral
Este documento descreve as medidas de segurança implementadas nos toggles de controle do site (uploads públicos e modo de manutenção).

## 🛡️ Camadas de Segurança Implementadas

### 1. **Proteção CSRF (Cross-Site Request Forgery)**
- ✅ Token CSRF obrigatório em todas as requisições POST
- ✅ Validação automática pelo middleware `VerifyCsrfToken` do Laravel
- ✅ Token gerado por sessão do usuário autenticado
- ✅ Verificação no frontend antes de enviar requisição

```php
// Backend valida automaticamente
Route::post('/dashboard/settings/toggle', ...)
    ->middleware('web'); // Inclui CSRF protection

// Frontend inclui token
headers: {
    'X-CSRF-TOKEN': '{{ csrf_token() }}'
}
```

### 2. **Prevenção de SQL Injection**
- ✅ Uso de Eloquent ORM (prepared statements automáticos)
- ✅ Validação de formato de chave (apenas alfanuméricos e underscore)
- ✅ Sanitização de entrada com regex: `/^[a-zA-Z0-9_]+$/`
- ✅ Type casting rigoroso de valores

```php
// Sanitização de chave
$sanitizedKey = preg_replace('/[^a-zA-Z0-9_]/', '', $key);

// Eloquent previne SQL injection automaticamente
self::updateOrCreate(['key' => $key], [...]);
```

### 3. **Proteção XSS (Cross-Site Scripting)**
- ✅ Sanitização de HTML em logs: `htmlspecialchars()`
- ✅ Escape de mensagens no frontend antes de exibir
- ✅ Blade templates escapam variáveis automaticamente: `{{ $var }}`
- ✅ Validação de tipo de entrada (boolean, string, integer, json)

```php
// Backend sanitiza descrições de log
'description' => htmlspecialchars(
    "Changed {$key} to ...",
    ENT_QUOTES,
    'UTF-8'
),

// Frontend escapa mensagens
const message = String(data.message)
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
```

### 4. **Autorização e Autenticação**
- ✅ Verificação obrigatória de autenticação: `auth()->check()`
- ✅ Verificação de permissão admin: `auth()->user()->is_admin`
- ✅ Abort 403 para usuários não autorizados
- ✅ Middleware `auth` e `admin` nas rotas

```php
// Verificação rigorosa de autorização
if (!auth()->check() || !auth()->user()->is_admin) {
    abort(403, 'Acesso negado...');
}
```

### 5. **Rate Limiting (Limitação de Taxa)**
- ✅ Limite de 10 requisições por minuto por usuário
- ✅ Proteção contra ataques de força bruta
- ✅ Proteção contra spam de toggles
- ✅ Middleware `throttle:10,1` aplicado

```php
// Route rate limiting
Route::post('/dashboard/settings/toggle', ...)
    ->middleware('throttle:10,1'); // 10 requests per minute
```

### 6. **Validação de Input**
- ✅ Validação Laravel com regras estritas
- ✅ Whitelist de configurações permitidas
- ✅ Validação de tipo de dados
- ✅ Validação duplicada (backend + frontend)

```php
// Validação rigorosa
$validated = $request->validate([
    'key' => ['required', 'string', 'in:public_uploads_enabled,maintenance_mode'],
    'value' => ['required', 'boolean'],
]);

// Whitelist adicional (defense in depth)
$allowedSettings = ['public_uploads_enabled', 'maintenance_mode'];
if (!in_array($validated['key'], $allowedSettings, true)) {
    // Reject
}
```

### 7. **Type Safety (Segurança de Tipos)**
- ✅ Type casting explícito de valores
- ✅ Validação de tipos permitidos
- ✅ Conversão segura de boolean/integer/json
- ✅ Strict comparison (`===`) em validações

```php
// Type casting rigoroso
$settingKey = (string) $validated['key'];
$settingValue = (bool) $validated['value'];

// Tipos permitidos
$allowedTypes = ['string', 'boolean', 'integer', 'json'];
if (!in_array($type, $allowedTypes, true)) {
    throw new \InvalidArgumentException('Invalid type');
}
```

### 8. **Cache Poisoning Prevention**
- ✅ Sanitização de chaves de cache
- ✅ Invalidação de cache após mudanças
- ✅ TTL (Time To Live) definido: 5-60 minutos
- ✅ Cache separado por configuração

```php
// Previne cache poisoning
$sanitizedKey = preg_replace('/[^a-zA-Z0-9_]/', '', $key);
Cache::remember("setting_{$sanitizedKey}", 300, ...);

// Limpa cache após mudança
Cache::forget("setting_{$key}");
Cache::forget('site_settings');
```

### 9. **Logging Seguro**
- ✅ Log de todas as mudanças de configuração
- ✅ Registro de IP e User Agent
- ✅ User ID do responsável pela mudança
- ✅ Sanitização de dados logados
- ✅ Erros logados sem expor informações sensíveis

```php
// Log completo e seguro
ActivityLog::create([
    'user_id' => auth()->id(),
    'type' => 'site_setting_changed',
    'description' => htmlspecialchars(...),
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
]);
```

### 10. **Error Handling Seguro**
- ✅ Try-catch para capturar exceções
- ✅ Mensagens de erro genéricas ao usuário
- ✅ Detalhes técnicos apenas no log
- ✅ HTTP status codes apropriados (400, 403, 500)

```php
try {
    // Operação
} catch (\Exception $e) {
    // Log detalhado (privado)
    \Log::error('Failed to toggle', [...]);
    
    // Mensagem genérica (público)
    return response()->json([
        'message' => 'Erro ao atualizar configuração.'
    ], 500);
}
```

### 11. **Frontend Security**
- ✅ Prevenção de double-click/rapid requests
- ✅ Validação de entrada antes de enviar
- ✅ Credentials: 'same-origin' (não envia para outros domínios)
- ✅ Verificação de existência do CSRF token
- ✅ Desabilita botão durante requisição

```javascript
// Previne requisições rápidas
if (toggleInProgress) {
    console.warn('Please wait...');
    return;
}

// Validação client-side
const allowedKeys = ['public_uploads_enabled', 'maintenance_mode'];
if (!allowedKeys.includes(key)) {
    alert('Configuração inválida');
    return;
}
```

### 12. **Middleware de Manutenção Seguro**
- ✅ Bypass apenas para admins autenticados
- ✅ Cache de status (5 minutos) para performance
- ✅ Whitelist de rotas permitidas durante manutenção
- ✅ Verificação de rota atual

```php
// Whitelist de rotas
$allowedRoutes = ['maintenance', 'logout'];
if (in_array($currentRoute, $allowedRoutes, true)) {
    return $next($request);
}
```

## 📋 Checklist de Segurança

- [x] **CSRF Protection** - Token em todas as requisições POST
- [x] **SQL Injection** - Eloquent ORM + sanitização
- [x] **XSS Protection** - htmlspecialchars + escape frontend
- [x] **Authorization** - Verificação admin obrigatória
- [x] **Rate Limiting** - 10 req/min por usuário
- [x] **Input Validation** - Validação rigorosa backend + frontend
- [x] **Type Safety** - Type casting e validação de tipos
- [x] **Cache Security** - Sanitização de chaves + TTL
- [x] **Secure Logging** - Logs completos e sanitizados
- [x] **Error Handling** - Mensagens genéricas ao público
- [x] **Frontend Validation** - Double-click prevention
- [x] **Same-Origin Policy** - Credentials same-origin

## 🚨 Possíveis Ataques Mitigados

| Ataque | Mitigação |
|--------|-----------|
| **CSRF** | Token CSRF obrigatório |
| **SQL Injection** | Eloquent ORM + sanitização de chaves |
| **XSS** | htmlspecialchars + escape frontend |
| **Brute Force** | Rate limiting (10/min) |
| **Authorization Bypass** | Verificação admin rigorosa |
| **Cache Poisoning** | Sanitização de chaves de cache |
| **Type Confusion** | Type casting explícito |
| **Double Submit** | Lock frontend durante request |
| **Error Disclosure** | Mensagens genéricas + log privado |

## 🔧 Uso Correto

### Toggling Uploads Públicos
```php
POST /dashboard/settings/toggle
Headers: X-CSRF-TOKEN, Content-Type: application/json
Body: {
    "key": "public_uploads_enabled",
    "value": true/false
}
```

### Toggling Modo Manutenção
```php
POST /dashboard/settings/toggle
Headers: X-CSRF-TOKEN, Content-Type: application/json
Body: {
    "key": "maintenance_mode",
    "value": true/false
}
```

## ⚠️ Avisos de Segurança

1. **Apenas Admins** - Apenas usuários com `is_admin = true` podem usar toggles
2. **CSRF Token** - Sessão deve estar ativa com token CSRF válido
3. **Rate Limit** - Máximo 10 mudanças por minuto
4. **Logs** - Todas as mudanças são registradas com IP e User Agent
5. **Cache** - Status é cacheado (5 min), mudanças levam até 5 min para propagar

## 🔍 Auditoria e Monitoramento

Todas as mudanças são registradas na tabela `activity_logs`:
```sql
SELECT * FROM activity_logs 
WHERE type = 'site_setting_changed' 
ORDER BY created_at DESC;
```

## 📚 Referências

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security Best Practices](https://laravel.com/docs/11.x/security)
- [CSRF Protection](https://laravel.com/docs/11.x/csrf)
- [Rate Limiting](https://laravel.com/docs/11.x/routing#rate-limiting)
