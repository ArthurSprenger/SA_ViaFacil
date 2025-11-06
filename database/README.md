# 📊 Database - SA ViaFácil

## 📁 Estrutura de Arquivos

### ✅ **ARQUIVO PRINCIPAL (USE ESTE)**
- **`sa_viafacil_completo.sql`** - Script consolidado com todas as tabelas e dados necessários

### 📦 Arquivos Antigos (Mantidos para Referência)
- `sa_viafacil_db.sql` - Script original com tabelas básicas
- `notificacoes.sql` - Tabelas de avisos e notificações
- `login_db.sql` - Banco antigo de login (descontinuado)

---

## 🚀 Como Instalar o Banco de Dados

### Opção 1: Via phpMyAdmin
1. Acesse `http://localhost/phpmyadmin`
2. Clique em **"Importar"**
3. Selecione o arquivo `sa_viafacil_completo.sql`
4. Clique em **"Executar"**

### Opção 2: Via Terminal MySQL
```bash
mysql -u root -p < database/sa_viafacil_completo.sql
```

### Opção 3: Via PowerShell (XAMPP)
```powershell
cd C:\xampp\mysql\bin
.\mysql.exe -u root -p sa_viafacil_db < ..\..\htdocs\2025_atividades_FelipeC\SA_ViaFacil\SA_ViaFacil\database\sa_viafacil_completo.sql
```

---

## 📋 Estrutura do Banco de Dados

### 🗂️ Tabelas Principais

| Tabela | Descrição |
|--------|-----------|
| **usuarios** | Gerenciamento de usuários (admin/normal) |
| **solicitacoes** | Solicitações relacionadas a estações |
| **sensor** | Catálogo de sensores IoT |
| **sensor_data** | Dados coletados pelos sensores |
| **avisos** | Avisos publicados por administradores |
| **notificacoes** | Sistema de notificações em tempo real |

---

## 👥 Usuários Padrão

| Email | Senha | Tipo | Status |
|-------|-------|------|--------|
| admin@exemplo.com | admin123 | admin | aprovado |
| felipe@viafacil.com | felipe123 | admin | aprovado |
| usuario@exemplo.com | senha123 | normal | aprovado |
| operador@exemplo.com | operador123 | normal | aprovado |

⚠️ **IMPORTANTE**: Altere as senhas após o primeiro acesso!

---

## 🔧 Recursos do Sistema

### 🌐 Sistema MQTT
- **Sensores IoT**: Monitoramento em tempo real
- **Notificações**: Pub/Sub via broker MQTT
- **Tópicos**:
  - `viafacil/sensores/#` - Dados dos sensores
  - `viafacil/notificacoes/#` - Avisos e alertas

### 📊 Tipos de Sensores
- `temperatura_freio` - Temperatura dos freios (°C)
- `vibracao_motor` - Vibração do motor (mm/s)
- `pressao_ar` - Pressão do ar (bar)
- `temperatura_motor` - Temperatura do motor (°C)

---

## 🔐 Segurança

- **Senhas**: Sistema usa `password_hash()` com bcrypt
- **Migração Automática**: Senhas MD5 antigas são convertidas no primeiro login
- **Prepared Statements**: Proteção contra SQL Injection
- **Foreign Keys**: Integridade referencial garantida

---

## 📝 Notas de Versão

### Versão Consolidada (06/11/2025)
- ✅ Unificação de todos os scripts SQL
- ✅ Adição de índices para performance
- ✅ Foreign Keys configuradas
- ✅ Charset UTF-8 (utf8mb4)
- ✅ Dados de exemplo incluídos
- ✅ Prevenção de duplicatas (INSERT com NOT EXISTS)

---

## 🛠️ Manutenção

### Backup Diário
```bash
mysqldump -u root -p sa_viafacil_db > backup_$(date +%Y%m%d).sql
```

### Limpar Dados Antigos de Sensores (> 30 dias)
```sql
DELETE FROM sensor_data WHERE data_hora < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

### Verificar Integridade
```sql
CHECK TABLE usuarios, solicitacoes, sensor, sensor_data, avisos, notificacoes;
```

---

## 📞 Suporte

Para problemas de instalação ou dúvidas:
- 📧 Email: felipe@viafacil.com
- 📚 Documentação: Ver `README.md` principal do projeto
