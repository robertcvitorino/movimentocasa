# Movimento Casa

Sistema de gestao para comunidade religiosa construido com Laravel 12, Filament 5, Livewire 4 e Tailwind CSS v4.

## Stack Tecnica

- **PHP:** 8.4+
- **Laravel:** 12.0
- **Filament:** 5.2 (paineis admin e membro)
- **Livewire:** 4.0 (componentes reativos)
- **Flux UI:** 2.9.0 (componentes de interface)
- **Tailwind CSS:** 4.0.7 (estilizacao)
- **Vite:** 7.0.4 (build frontend)
- **Pest:** 4.4 (testes)
- **Spatie Permission:** 6.24 (controle de acesso)
- **Fortify:** 1.30 (autenticacao)
- **DomPDF:** 3.1 (geracao de PDF)
- **Guava Calendar:** 3.1 (widget de calendario)

## Comandos

```bash
# Setup completo do projeto
composer setup

# Servidor de desenvolvimento (serve + queue + vite em paralelo)
composer dev

# Rodar testes (limpa config, verifica lint, executa testes)
composer test

# Lint com Pint
composer lint

# Verificar lint sem corrigir
composer lint:check

# Rodar apenas os testes do Pest
php artisan test

# Rodar testes com filtro
php artisan test --filter="NomeDoTeste"

# Gerar permissoes do Shield
php artisan shield:generate --all
```

## Arquitetura do Projeto

### Paineis Filament

- **Admin** (`/admin`) — painel administrativo com acesso por roles
- **Member** (`/member`) — painel do membro com resources de perfil, formacoes, certificados, eventos, avisos e contribuicoes

### Estrutura de Diretorios

```
app/
├── Actions/           # Logica de negocio isolada (padrao Action)
│   ├── Formation/     # CompleteFormationLesson, IssueCertificate, SubmitQuizAttempt, SyncFormationProgress
│   ├── Fortify/       # CreateNewUser, ResetUserPassword
│   └── Member/        # CreateMemberUser, SendMemberPasswordReset, SyncMemberUser
├── Enums/             # Enums PHP 8.1+ com labels em pt-BR, cores e icones
├── Filament/
│   ├── Admin/         # Resources do painel admin
│   └── Member/        # Resources do painel membro (Profile, Formation, Certificate, Event, Notice, Contribution)
├── Jobs/              # Jobs assincronos (SendEventCreatedNotificationsJob)
├── Models/            # Eloquent models com relationships, scopes e casts
├── Observers/         # Model observers (EventObserver)
├── Services/          # Servicos de dominio (EventNotification, EventRecurrence)
├── Livewire/          # Componentes Livewire (Settings: Profile, Password, TwoFactor, Appearance)
└── Providers/         # Service providers
```

### Modulos Principais

1. **Membros** — cadastro completo, status (Ativo/Inativo/Visitante/Pausado), titulos sacramentais
2. **Ministerios** — grupos organizacionais com coordenadores e membros com roles
3. **Formacoes** — cursos com aulas (video/texto), quizzes, certificados, progresso do membro
4. **Eventos** — calendario com recorrencia (diaria/semanal/mensal), audiencia por ministerio ou membro
5. **Tarefas** — atribuicao a membros ou ministerios com prioridade
6. **Avisos** — comunicados com likes e comentarios
7. **Financeiro** — contribuicoes, metas financeiras, configuracoes PIX
8. **Permissoes** — 5 roles: System Admin, General Coordinator, Ministry Coordinator, Financial Coordinator, Member

## Convencoes de Codigo

### Nomenclatura

- **Models:** Singular, PascalCase (`Member`, `Formation`, `FormationLesson`)
- **Actions:** `[Verbo][Entidade]Action` (`CompleteFormationLessonAction`, `IssueCertificateAction`)
- **Enums:** PascalCase com valores snake_case (`MemberStatus::Active`, `FormationProgressStatus::InProgress`)
- **Services:** `[Feature]Service` (`EventNotificationService`, `EventRecurrenceService`)
- **Observers:** `[Model]Observer` (`EventObserver`)
- **Jobs:** `[Acao]Job` (`SendEventCreatedNotificationsJob`)
- **Filament Resources:** `[Model]Resource` dentro de namespace por painel (`Admin/`, `Member/`)
- **Testes:** Pest syntax com `it()`, organizados em `Feature/` e `Unit/`

### Padroes Obrigatorios

- **Actions para logica de negocio** — nunca colocar logica complexa em controllers ou models
- **Enums para status** — sempre usar Enums PHP nativos com metodos `label()`, `color()`, `icon()`
- **DB::transaction()** — operacoes que alteram multiplas tabelas devem usar transacoes
- **Eager loading** — sempre carregar relationships necessarias para evitar N+1
- **Type hints** — todas as funcoes devem ter tipos de parametro e retorno declarados
- **Scopes** — queries reutilizaveis devem ser model scopes (`scopePublished`, `scopeVisibleToMember`)
- **CarbonImmutable** — o projeto usa `CarbonImmutable` como padrao para datas
- **Timezone** — `America/Sao_Paulo` configurado globalmente
- **Locale** — `pt_BR`, labels de Enums e interface em portugues brasileiro

### Padroes de Teste

- Framework: **Pest 4** com sintaxe `it()`
- Banco de testes: **SQLite em memoria**
- Usar factories para criar dados de teste
- Preferir assertions especificas (`assertSuccessful()`, `assertNotFound()`) ao inves de `assertStatus()`
- Testar Actions isoladamente
- Testar visibilidade e permissoes (scopes)

### Filament

- Resources separados por painel (`Admin/Resources/`, `Member/Resources/`)
- Tabelas e formularios em classes separadas quando complexos (`FormationTable::configure()`)
- Navigation com groups e icones Heroicons
- Widgets no dashboard (calendario, overview de jornada)
- Shield para gerenciar permissoes automaticamente

## Banco de Dados

- **Desenvolvimento:** SQLite
- **39 migrations** cobrindo todas as entidades
- Foreign keys com `cascadeOnDelete` para integridade referencial
- Pivot tables com atributos extras (role, status, timestamps)
- Indices em colunas frequentemente consultadas

## Configuracoes Importantes

- Filament Shield para permissoes: `config/filament-shield.php`
- Spatie Permission: `config/permission.php`
- Fortify (2FA, verificacao email): `config/fortify.php`
- Senha em producao: minimo 12 caracteres, mixed case, numeros, simbolos
- Comandos destrutivos bloqueados em producao
