# The Message Crafter - Quick Reference Guide

## File Locations

### Domain Layer
- **Membership Context:** `/domain/Membership/`
  - Entities: `Entities/User.php`
  - Repositories: `Repositories/IUserRepository.php`
  - Events: `Events/UserCreated.php`

- **Messaging Context:** `/domain/Messaging/`
  - Entities: `Entities/Contact.php`, `Conversation.php`, `Message.php`, `MessageButton.php`
  - Repositories: `Repositories/I*.php`
  - Events: `Events/ConversationStarted.php`, `MessageReceived.php`, `ConversationFinished.php`
  - Value Objects: `ValueObjects/MessageBody.php`, `ValueObjects/Body/*.php`
  - Enums: `Enums/*.php` (ConversationStatus, MessageStatus, ReplyAction, etc.)
  - Exceptions: `Exceptions/ConversationAlreadyStartedException.php`

- **Shared Context:** `/domain/Shared/`
  - Events: `Events/DomainEvent.php`, `HasDomainEvents.php`, `IDomainEventBus.php`
  - Value Objects: `ValueObjects/ValueObject.php`
  - Enums: `Enums/Course.php`
  - Attributes: `Attributes/SkipPersistence.php`

### Application Layer
- **Actions:** `/app/Actions/`
  - Membership: `Membership/UserApiLogin.php`, `UserApiLogout.php`
  - Messaging: `Messaging/StartConversation.php`, `HandleMessageReceived.php`, etc.
  - Strategies: `Messaging/Strategies/IMessageFlow.php`, `StartCourseStrategy.php`, etc.
  - Pipes: `Messaging/Pipes/ConversationPipe.php`, `AskCourse.php`, etc.

- **DTOs:** `/app/DTOs/`
  - `MessageFlowInputDTO.php`, `UserApiLoginInputDTO.php`, etc.

- **Presenters:** `/app/Presenters/`
  - `AuthenticationPresenter.php`

- **Event Listeners:** `/app/Listeners/`
  - `ConversationStartedListener.php`, `MessageReceivedListener.php`, etc.

### Infrastructure Layer
- **Repositories (Eloquent):** `/app/Repositories/Eloquent/`
  - `BaseRepository.php`, `UserRepository.php`, `ContactRepository.php`, etc.

- **Services:** `/app/Services/`
  - Messenger: `Messenger/Contracts/IMessenger.php`, `Whatsapp/Messenger.php`, `Email/SendGrid.php`
  - Templates: `app/Support/Whatsapp/Templates/`
  - WhatsApp Builders: `app/Support/Whatsapp/Builders/`

- **Models (Eloquent):** `/app/Models/`
  - `User.php`, `Contact.php`, `Conversation.php`, `Message.php`, `MessageButton.php`

### Presentation Layer
- **Controllers:** `/app/Http/Controllers/`
  - Auth: `Auth/AuthenticationAPIController.php`
  - Messaging: `Contact/StartConversationController.php`, `ProcessCallbackController.php`
  - Webhooks: `Meta/WebhookController.php`

- **Requests:** `/app/Http/Requests/`
  - `Auth/UserApiLoginRequest.php`
  - `StartConversationRequest.php`

### Configuration
- **Config Files:** `/config/`
  - `whatsapp.php` - WhatsApp API settings
  - `app.php` - General app config
  - `services.php` - External service configs

### Database
- **Migrations:** `/database/migrations/`
  - `2025_05_06_000001_create_contacts_table.php`
  - `2025_05_07_000001_create_conversations_table.php`
  - `2025_05_08_000002_create_messages_table.php`
  - `2025_05_08_000003_create_message_buttons_table.php`

---

## Key Classes to Understand

### Aggregates (Business Logic Centers)
1. **Contact** (`Domain\Messaging\Entities\Contact`)
   - `startConversation()` - Initiates a conversation with a contact
   - `messageReceived()` - Records that a message was received
   - Records domain events

2. **Conversation** (`Domain\Messaging\Entities\Conversation`)
   - `finish()` - Ends an active conversation
   - `startStrategy()` - Sets the conversation strategy
   - `advanceToStep()` - Advances to next step in pipeline
   - Records domain events

### Use Cases (Application Services)
1. **StartConversation** - Initiates messaging with a contact
2. **HandleMessageReceived** - Processes incoming messages and routes to strategies
3. **HandleConversationFinished** - Sends closing message when conversation ends

### Strategies (Message Flow Patterns)
1. **StartCourseStrategy** - Ask course, ask email, save email flow
2. **DiveDeeperStrategy** - Deep dive into course content
3. **HelpOrSupportStrategy** - Support request handling

### Event Flow
```
Domain Event (e.g., ConversationStarted)
  ↓
LaravelDomainEventBus.publish()
  ↓
Maps to Laravel Event (ConversationStartedEvent)
  ↓
Event::dispatch()
  ↓
Listener (ConversationStartedListener)
  ↓
Action (SendStartMessage)
```

---

## Important Patterns

### Factory Pattern
All domain entities use static `create()` methods:
```php
$contact = Contact::create(name, email, phone, verified);
$conversation = Conversation::create(contactId, status, startedAt);
```

### Repository Pattern
All data access through repository interfaces:
```php
$user = $userRepository->findById(1);
$contact = $contactRepository->create(['name' => 'John']);
```

### Strategy Pattern
Message handling uses runtime strategy selection:
```php
$strategy = $strategyResolver->resolve($replyAction);
$strategy->handle($messageFlowData);
```

### Pipeline Pattern
Multiple processing steps in sequence:
```php
$pipeline->process($input, $conversation, [
    AskCourse::class,
    AskEmail::class,
    SaveEmail::class
]);
```

---

## Database Relationships

```
User (1) ─────────→ (N) Contact
Contact (1) ───────→ (N) Conversation
Conversation (1) ──→ (N) Message
Message (N) ───────→ (N) MessageButton
```

**Key Constraints:**
- Cascade delete on Contact → Conversation → Message
- User → Contact: SET NULL on delete
- Message → MessageButton: Cascade delete

---

## Event Flow Examples

### Conversation Started Flow
1. Contact requests to start conversation via StartConversationController
2. StartConversation action calls Contact.startConversation()
3. Contact records ConversationStarted event
4. DomainEventBus publishes event
5. ConversationStartedListener triggered
6. SendStartMessage action executes
7. Initial greeting message sent via WhatsApp

### Message Received Flow
1. WhatsApp webhook hits WebhookController.handle()
2. ProcessMessageCallback action creates MessageReceived event
3. MessageReceivedListener triggered
4. HandleMessageReceived action:
   - Marks message as FINISHED
   - Loads conversation
   - Resolves strategy from reply action
   - Executes strategy via FlowPipeline
5. Pipeline steps execute in order (e.g., AskCourse → AskEmail → SaveEmail)

---

## Configuration Requirements

### WhatsApp Integration
```php
// config/whatsapp.php
[
    'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
    'base_url' => env('WHATSAPP_BASE_URL'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
]
```

### Database
```php
// config/database.php
'default' => env('DB_CONNECTION', 'mysql'),
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST'),
    'port' => env('DB_PORT'),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
]
```

---

## Common Tasks

### Adding a New Use Case
1. Create action class in `app/Actions/{Context}/`
2. Inject dependencies via constructor
3. Implement `handle()` method
4. Emit domain events if needed
5. Register in service provider if using DI

### Adding a New Repository Method
1. Add method to interface in `domain/{Context}/Repositories/I*.php`
2. Implement in `app/Repositories/Eloquent/*.php`
3. Use in actions/services via dependency injection

### Adding a New Strategy
1. Create class in `app/Actions/Messaging/Strategies/`
2. Implement `IMessageFlow` interface
3. Add to `FlowStrategyResolver.resolve()` match statement
4. Define pipeline steps in `handle()` method

### Adding a New Event
1. Create readonly class in `domain/{Context}/Events/`
2. Extend `DomainEvent`
3. Add properties with constructor
4. Create corresponding Laravel event wrapper
5. Add listener mapping in AppServiceProvider

---

## Testing Hints

### Test Locations
- `/tests/Feature/` - Integration tests
- `/tests/Unit/` - Unit tests

### Key Test Classes
- `TestCase.php` - Base test class
- Test database is separate from main DB

---

## Areas to Extend

1. **UserApiLogout** - Currently empty, needs token revocation
2. **SendGrid Integration** - Email messenger is placeholder
3. **Additional Strategies** - More conversation flows
4. **CQRS Pattern** - Separate read/write models
5. **Event Sourcing** - Full event log for conversations
6. **Soft Deletes** - Track deleted entities
7. **Audit Logging** - Track all entity changes

---

## Important Notes

- Domain events are the primary communication mechanism between contexts
- The repository pattern abstracts Eloquent from business logic
- Facades provide convenient access (Repository, Messenger, DomainEventBus)
- All DTOs are readonly for immutability
- The SkipPersistence attribute marks non-persistent properties
- Conversation step tracking prevents duplicate message sends
- Strategy class and current_step track conversation progress

---

## Useful Commands

```bash
# Run migrations
php artisan migrate

# Create a new migration
php artisan make:migration create_table_name

# Run tests
php artisan test

# Run specific test
php artisan test tests/Feature/Auth/AuthenticationTest.php

# Clear cache
php artisan cache:clear

# Show routes
php artisan route:list
```

---
