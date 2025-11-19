# Atrocidades - Guia de Deploy

## 🚀 Deploy Rápido no Railway

### Passo 1: Preparar o Projeto
```bash
git add .
git commit -m "Preparar para deploy"
git push origin master
```

### Passo 2: Configurar Railway
1. Acesse: https://railway.app
2. Clique em "Start a New Project"
3. Escolha "Deploy from GitHub repo"
4. Selecione o repositório "Ominous"
5. Railway detectará Laravel automaticamente

### Passo 3: Configurar Variáveis de Ambiente
No painel do Railway, vá em "Variables" e adicione:
```
APP_NAME=Atrocidades
APP_ENV=production
APP_DEBUG=false
APP_KEY=[será gerado automaticamente]
APP_URL=[será fornecido pelo Railway]
DB_CONNECTION=sqlite
```

### Passo 4: Deploy
- Railway fará o deploy automaticamente
- Aguarde 2-5 minutos
- Acesse a URL fornecida

---

## 🌐 Outras Opções de Hospedagem

### Render.com (Grátis)
1. Conta: https://render.com
2. New > Web Service
3. Conecte GitHub
4. Build Command: `composer install --no-dev`
5. Start Command: `php artisan serve --host=0.0.0.0 --port=$PORT`

### Vercel (Grátis)
```bash
npm i -g vercel
vercel --prod
```

### Heroku (Pago)
```bash
heroku create atrocidades-app
git push heroku master
heroku run php artisan migrate
```

---

## 📋 Checklist Pré-Deploy

- [x] Arquivos de configuração criados (railway.json, Procfile, nixpacks.toml)
- [x] .env.production configurado
- [ ] FFmpeg instalado no servidor (para thumbnails)
- [ ] Storage linkado (`php artisan storage:link`)
- [ ] Migrations executadas (`php artisan migrate --force`)
- [ ] Cache otimizado (config, route, view)

---

## 🔧 Comandos Úteis Pós-Deploy

```bash
# Gerar chave da aplicação
php artisan key:generate

# Rodar migrations
php artisan migrate --force

# Criar link do storage
php artisan storage:link

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Criar admin (caso necessário)
php artisan db:seed --class=DatabaseSeeder
```

---

## 📊 Monitoramento

Após deploy, verifique:
- [ ] Homepage carregando
- [ ] Login funcionando
- [ ] Upload de vídeos operacional
- [ ] Banco de dados persistente
- [ ] Arquivos de mídia sendo salvos

---

## ⚠️ Troubleshooting

### Erro 500
- Verifique APP_DEBUG=true temporariamente
- Cheque logs: `tail -f storage/logs/laravel.log`

### Storage não funciona
- Execute: `php artisan storage:link`
- Verifique permissões da pasta storage/

### Banco de dados vazio
- Execute: `php artisan migrate --force`
- Opcionalmente: `php artisan db:seed`

---

## 🎯 URLs Importantes

- **Railway Dashboard**: https://railway.app/dashboard
- **Render Dashboard**: https://dashboard.render.com
- **Documentação Laravel Deploy**: https://laravel.com/docs/deployment
