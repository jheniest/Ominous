# Deploy no Hostinger - Passo a Passo

## 📋 Preparação Local

### 1. Otimizar o Projeto
```bash
# No terminal do projeto:
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Criar arquivo .zip do projeto
Compacte TODA a pasta do projeto (incluindo arquivos ocultos como .htaccess)

---

## 🌐 Upload via Hostinger

### Método 1: File Manager (Mais Fácil)

1. **Acesse o painel Hostinger:**
   - Login: https://hpanel.hostinger.com
   - Vá em "Websites" > Seu site > "File Manager"

2. **Navegue até public_html:**
   - Delete todos os arquivos padrão (index.html, etc)
   - Limpe a pasta completamente

3. **Upload do projeto:**
   - Clique em "Upload Files"
   - Arraste o arquivo .zip do projeto
   - Aguarde o upload completar
   - Clique com botão direito no .zip > "Extract"
   - Delete o .zip após extrair

4. **Ajustar estrutura:**
   - Mova TODOS os arquivos da pasta "Ominous" para a raiz de public_html
   - A estrutura deve ficar:
     ```
     public_html/
     ├── app/
     ├── bootstrap/
     ├── config/
     ├── database/
     ├── public/
     ├── resources/
     ├── routes/
     ├── storage/
     ├── vendor/
     ├── .env
     ├── artisan
     ├── composer.json
     └── ...
     ```

5. **Configurar Document Root:**
   - Volte ao hPanel > Websites > Seu site
   - Vá em "Advanced" > "Document Root"
   - Altere de `/public_html` para `/public_html/public`
   - Salve

---

### Método 2: FTP (FileZilla)

1. **Obter credenciais FTP:**
   - hPanel > Websites > Seu site > "FTP Accounts"
   - Use as credenciais fornecidas

2. **Conectar com FileZilla:**
   - Host: ftp.seudominio.com (ou IP fornecido)
   - Username: usuário FTP
   - Password: senha FTP
   - Port: 21

3. **Upload:**
   - Lado esquerdo: pasta local do projeto
   - Lado direito: /public_html/
   - Arraste TODOS os arquivos e pastas
   - Aguarde transferência (pode demorar 10-30 min)

---

## ⚙️ Configuração do .env

### Via File Manager:
1. Localize o arquivo `.env` em public_html/
2. Clique direito > Edit
3. Configure:

```env
APP_NAME=Atrocidades
APP_ENV=production
APP_KEY=base64:SUA_CHAVE_AQUI
APP_DEBUG=false
APP_URL=https://seudominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario_banco
DB_PASSWORD=senha_banco

SESSION_DRIVER=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

### Obter dados do banco:
- hPanel > Websites > Databases > MySQL Databases
- Anote: nome do banco, usuário, senha
- Host geralmente é: localhost

---

## 🗄️ Configurar Banco de Dados

1. **Criar banco MySQL:**
   - hPanel > Databases > MySQL Databases
   - Clique em "Create New Database"
   - Nome: atrocidades_db
   - Criar usuário ou usar existente
   - Anotar credenciais

2. **Executar migrations via SSH ou File Manager:**

### Via SSH (se disponível):
```bash
ssh usuario@seudominio.com
cd public_html
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan db:seed --class=DatabaseSeeder
```

### Via Terminal Web do Hostinger:
- hPanel > Advanced > Terminal
- Execute os comandos acima

---

## 🔧 Ajustes de Permissões

### Via File Manager:
1. Clique direito em `storage/` > Permissions
2. Defina como: 775 (ou 755)
3. Marcar "Apply to all files inside"

4. Repetir para `bootstrap/cache/`:
   - Permissions: 775

---

## 🔐 Criar .htaccess na raiz (se não existir)

Crie arquivo `.htaccess` em `/public_html/`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## ✅ Checklist Final

- [ ] Todos os arquivos enviados
- [ ] Document Root apontando para /public_html/public
- [ ] .env configurado com dados corretos
- [ ] Banco de dados criado e configurado
- [ ] APP_KEY gerado (php artisan key:generate)
- [ ] Migrations executadas
- [ ] storage/ e bootstrap/cache/ com permissões corretas
- [ ] storage:link executado
- [ ] Teste: acesse seudominio.com

---

## 🚨 Problemas Comuns

### Erro 500:
- Verifique permissões de storage/ e bootstrap/cache/
- Ative APP_DEBUG=true temporariamente
- Cheque logs em storage/logs/

### Página em branco:
- Document Root incorreto
- Verifique se aponta para /public_html/public

### Erro de conexão com banco:
- Verifique credenciais no .env
- Confirme que o banco foi criado
- Host geralmente é "localhost"

### CSS/JS não carregam:
- Execute: php artisan storage:link
- Verifique APP_URL no .env
- Rode: npm run build (se usar Vite)

---

## 📞 Suporte Hostinger

Se precisar de ajuda:
- Chat ao vivo: disponível 24/7 no hPanel
- Base de conhecimento: https://support.hostinger.com
