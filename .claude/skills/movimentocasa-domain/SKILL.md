---
name: movimentocasa-domain
description: "Domain knowledge for the Movimento Casa church management system. Activates when creating new features, models, actions, or services; working with members, ministries, formations, events, tasks, notices, or contributions; or when context about the business domain is needed."
license: MIT
metadata:
  author: movimentocasa
---

# Movimento Casa - Domain Knowledge

## When to Apply

Activate this skill when:

- Creating new features or modules
- Working with domain entities (members, ministries, formations, events)
- Designing database schema or migrations
- Implementing business logic in Actions
- Understanding relationships between entities
- Localizing content for pt-BR

## Domain Overview

Movimento Casa is a church community management system. All UI text, labels, and enum values must be in **Portuguese (Brazil)**.

## Core Entities and Relationships

### Member (Membro)

Central entity representing a community member.

- **Status flow:** Visitor -> Active -> Paused/Inactive
- **Relationships:** belongs to User (1:1), has many Ministries (M:M with pivot), has many Titles, has Formations progress, Contributions, Certificates
- **Key fields:** full_name, birth_date, email, phone, is_whatsapp, instagram, address fields, status, joined_at

### Ministry (Ministerio)

Organizational groups within the community.

- Members belong to ministries with roles and status (active/inactive/on_leave)
- Each ministry has coordinators (via `ministry_coordinators` pivot)
- Events and Tasks can be targeted at specific ministries

### Formation (Formacao)

Educational/training programs.

- **Flow:** Draft -> Published -> Archived
- Has ordered Lessons (FormationLesson) with video/text content
- Optional Quiz with multiple choice questions
- Certificates issued on completion (when enabled)
- Progress tracked per member (MemberFormationProgress, MemberLessonProgress)
- **Business rules:**
  - Progress synced via `SyncFormationProgressAction`
  - Certificate issued automatically when all required lessons completed AND certificate_enabled
  - Quiz attempts scored against minimum_score

### Event (Evento)

Calendar events with audience targeting.

- **Recurrence:** none, daily, weekly, monthly
- **Audience types:** all_members, specific_ministries, specific_members
- Resolved via `EventAudienceResolver` (deduplicates users)
- Notifications sent via `SendEventCreatedNotificationsJob`
- Observed by `EventObserver`

### Task (Tarefa)

To-do items assigned to members or ministries.

- **Priority:** Uses `TaskPriority` enum
- **Responsible:** Polymorphic (member or ministry via `TaskResponsibleType`)
- Visible in calendar widgets

### Notice (Aviso)

Community announcements.

- Has likes (`NoticeLike`) and comments (`NoticeComment`)
- Auto-sets `published_at` on creation (via model `booted()`)
- Can have expiry dates

### Contribution (Contribuicao)

Financial donations.

- **Types:** via `ContributionType` enum
- **Payment methods:** via `PaymentMethod` enum (includes PIX)
- **Status:** via `ContributionStatus` enum
- PIX settings managed globally (`PixSetting`)
- Financial goals tracked via `FinancialGoal`

## Architecture Patterns

### Action Pattern

All complex business logic MUST go in Action classes:

```
app/Actions/
├── Formation/
│   ├── CompleteFormationLessonAction.php   # Marks lesson complete, syncs progress, issues certificate
│   ├── EnsureFormationProgressAction.php   # Creates progress record if not exists
│   ├── IssueCertificateAction.php          # Creates certificate for completed formation
│   ├── SubmitQuizAttemptAction.php          # Processes quiz answers and scores
│   └── SyncFormationProgressAction.php      # Recalculates progress percentage and status
├── Fortify/
│   ├── CreateNewUser.php                    # User registration with role assignment
│   └── ResetUserPassword.php                # Password reset logic
└── Member/
    ├── CreateMemberUserAction.php           # Creates User + assigns role + temp password
    ├── SendMemberPasswordResetAction.php    # Sends password reset email
    └── SyncMemberUserAction.php             # Syncs Member data with User
```

**Creating a new Action:**

```php
namespace App\Actions\Feature;

class DoSomethingAction
{
    public function __construct(
        private readonly OtherAction $otherAction,
    ) {}

    public function execute(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data) {
            // Business logic here
            return $model->fresh(['relationships']);
        });
    }
}
```

### Enum Pattern

All status fields use PHP Enums with pt-BR labels:

```php
namespace App\Enums;

enum ExampleStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Ativo',
            self::Inactive => 'Inativo',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Active => 'heroicon-o-check-circle',
            self::Inactive => 'heroicon-o-x-circle',
        };
    }
}
```

### Service Pattern

Services handle cross-cutting concerns:

- `EventNotificationService` — sends event notifications
- `EventRecurrenceService` — handles recurring event logic

### Observer Pattern

Model observers for side effects:

- `EventObserver` — registered in `AppServiceProvider::boot()`

## Existing Enums Reference

| Enum | Values |
|------|--------|
| MemberStatus | Active, Inactive, Visitor, Paused |
| MemberMinistryStatus | Active, Inactive, OnLeave |
| MinistryStatus | Active, Inactive |
| FormationStatus | Draft, Published, Archived |
| FormationProgressStatus | NotStarted, InProgress, Completed |
| LessonProgressStatus | NotStarted, InProgress, Completed |
| LessonSourceType | Video, Text/Document |
| ContributionStatus | Pending, Confirmed, Cancelled |
| ContributionType | Monthly, Annual, Special |
| PaymentMethod | PIX, Cash, Transfer, Other |
| FinancialGoalStatus | Active, Completed, Cancelled |
| EventAudienceType | AllMembers, SpecificMinistries, SpecificMembers |
| EventRecurrenceType | None, Daily, Weekly, Monthly |
| TaskPriority | Low, Medium, High, Urgent |
| TaskResponsibleType | Member, Ministry |
| QuestionType | MultipleChoice |
| QuizAttemptStatus | InProgress, Completed |
| RoleName | SystemAdmin, GeneralCoordinator, MinistryCoordinator, FinancialCoordinator, Member |

## Permissions (Roles)

| Role | Access |
|------|--------|
| System Admin | Full access to admin and member panels |
| General Coordinator | Manage all members, ministries, events |
| Ministry Coordinator | Manage their specific ministry |
| Financial Coordinator | Manage contributions and financial goals |
| Member | View own profile, formations, events, notices |

## Common Pitfalls

- Forgetting to wrap multi-table operations in `DB::transaction()`
- Not using Actions for business logic (putting it in controllers/models)
- Using mutable Carbon instead of CarbonImmutable
- Forgetting pt-BR labels on new Enums
- Not deduplicating audience when resolving event members
- Missing eager loading on Filament resource queries
- Not syncing formation progress after lesson completion
