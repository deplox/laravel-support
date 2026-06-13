# Graph Report - .  (2026-06-12)

## Corpus Check
- Corpus is ~20,524 words - fits in a single context window. You may not need a graph.

## Summary
- 350 nodes · 568 edges · 27 communities
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 29 edges (avg confidence: 0.81)
- Token cost: 7,500 input · 1,200 output

## Community Hubs (Navigation)
- [[_COMMUNITY_Package Configuration|Package Configuration]]
- [[_COMMUNITY_Eloquent Validation Rules|Eloquent Validation Rules]]
- [[_COMMUNITY_Query Scoping & Relationships|Query Scoping & Relationships]]
- [[_COMMUNITY_Artisan Route Commands|Artisan Route Commands]]
- [[_COMMUNITY_Password Token Storage|Password Token Storage]]
- [[_COMMUNITY_Password Reset Broker|Password Reset Broker]]
- [[_COMMUNITY_Password Reset Contracts|Password Reset Contracts]]
- [[_COMMUNITY_Relationship Eager Loading|Relationship Eager Loading]]
- [[_COMMUNITY_STI Hierarchy Traits|STI Hierarchy Traits]]
- [[_COMMUNITY_Model Expiration Dates|Model Expiration Dates]]
- [[_COMMUNITY_In-Memory SQLite Models|In-Memory SQLite Models]]
- [[_COMMUNITY_Validation & Dispatch Traits|Validation & Dispatch Traits]]
- [[_COMMUNITY_Model Parent Polymorphism|Model Parent Polymorphism]]
- [[_COMMUNITY_Action Object Tests|Action Object Tests]]
- [[_COMMUNITY_Sorting & Service Registration|Sorting & Service Registration]]
- [[_COMMUNITY_Documentation & Rationale|Documentation & Rationale]]
- [[_COMMUNITY_Animal Hierarchy Fixtures|Animal Hierarchy Fixtures]]
- [[_COMMUNITY_Validation Test Fixtures|Validation Test Fixtures]]
- [[_COMMUNITY_Dispatcher Test Fixtures|Dispatcher Test Fixtures]]
- [[_COMMUNITY_Action Trait Core|Action Trait Core]]
- [[_COMMUNITY_Test Infrastructure & CI|Test Infrastructure & CI]]

## God Nodes (most connected - your core abstractions)
1. `RouteShowCommand` - 22 edges
2. `DatabaseTokenRepository` - 17 edges
3. `UniqueEloquent` - 11 edges
4. `PasswordBroker` - 10 edges
5. `PasswordBrokerManager` - 10 edges
6. `ExistsEloquent` - 9 edges
7. `expires()` - 8 edges
8. `Post` - 8 edges
9. `Collection` - 7 edges
10. `makeValidator()` - 7 edges

## Surprising Connections (you probably didn't know these)
- `CI Pipeline (tests.yml)` --references--> `TestCase`  [INFERRED]
  .github/workflows/tests.yml → tests/TestCase.php
- `PHP 8.4 Trait Property Conflict Avoidance` --rationale_for--> `Sluggable Configuration Pattern`  [INFERRED]
  README.md → docs/sluggable.md
- `DatabaseTokenRepository` --implements--> `TokenRepositoryInterface`  [EXTRACTED]
  src/Auth/Passwords/DatabaseTokenRepository.php → src/Auth/Passwords/PasswordBroker.php
- `PasswordBroker` --implements--> `PasswordBrokerContract`  [EXTRACTED]
  src/Auth/Passwords/PasswordBroker.php → src/Auth/Passwords/PasswordBrokerManager.php
- `Pest Bootstrap Configuration` --references--> `TestCase`  [EXTRACTED]
  tests/Pest.php → tests/TestCase.php

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Single Table Inheritance Trait Pair (HasChildren + HasParent)** — concerns_haschildren, concerns_hasparent, concept_single_table_inheritance [EXTRACTED 0.95]
- **Custom Password Reset Subsystem (Provider + Manager + Broker + TokenRepo)** — passwords_passwordresetserviceprovider, passwords_passwordbrokermanager, passwords_passwordbroker, passwords_databasetokenrepository [EXTRACTED 1.00]
- **HTTP Query Parameter to Eloquent Scope Pattern** — concerns_hassearch, concerns_canincluderelationships, concept_eloquent_model_concerns [INFERRED 0.85]
- **Eloquent Model-backed Validation Rules** — rules_existseloquent, rules_uniqueeloquent, concept_eloquent_model_validation_rules [INFERRED 0.90]
- **Query Parameter Scope Pattern (Sort, Search, Include)** — concerns_hassorting, concept_query_param_scope, feature_hassortingtest [INFERRED 0.85]
- **InMemory Deferred Migration Boot Pattern** — concerns_inmemory, concept_deferred_migration, feature_hassearchtest [INFERRED 0.75]
- **Polymorphic Animal Hierarchy (Animal, Dog, Cat with HasChildren/HasParent)** — fixtures_animal, fixtures_dog, fixtures_cat [EXTRACTED 1.00]
- **Author-Post Bidirectional Relationship Fixtures** — fixtures_author, fixtures_post, feature_uniqueeloquenttest [EXTRACTED 1.00]
- **Custom Validation Rules Test Suite (StrongPassword, UniqueEloquent, ValidUlid)** — feature_strongpasswordruletest, feature_uniqueeloquenttest, feature_validulidruletest [INFERRED 0.95]
- **Laravel 13 Boot Safety Coordination** — readme_laravel13_boot_safety_rationale, readme_sti_pattern, readme_inmemory_pattern [EXTRACTED 0.95]
- **Action Class Building Blocks** — readme_action_pattern, tests_testcase_testcase, tests_pest_pest_bootstrap [INFERRED 0.75]
- **CI Quality Pipeline** — workflows_tests_ci_pipeline, tests_testcase_testcase, tests_pest_pest_bootstrap [EXTRACTED 0.95]

## Communities (27 total, 0 thin omitted)

### Community 0 - "Package Configuration"
Cohesion: 0.06
Nodes (34): pestphp/pest-plugin, authors, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+26 more)

### Community 1 - "Eloquent Validation Rules"
Cohesion: 0.10
Nodes (12): Eloquent-backed Validation Rule Pattern, Password, ExistsEloquent, StrongPassword, UniqueEloquent, ValidUlid, Closure, self (+4 more)

### Community 2 - "Query Scoping & Relationships"
Cohesion: 0.13
Nodes (18): BelongsTo, CanIncludeRelationships, SearchablePost, SortablePost, Author, Country, CustomSluggable, Post (+10 more)

### Community 3 - "Artisan Route Commands"
Cohesion: 0.18
Nodes (4): Collection, Command, RouteShowCommand, Route

### Community 4 - "Password Token Storage"
Cohesion: 0.19
Nodes (5): ConnectionInterface, HasherContract, DatabaseTokenRepository, Builder, CanResetPasswordContract

### Community 5 - "Password Reset Broker"
Cohesion: 0.16
Nodes (8): BasePasswordResetServiceProvider, composer.json (deplox/laravel-support), Custom Password Reset Subsystem, PasswordBroker, PasswordResetServiceProvider, CanResetPasswordContract, Closure, TokenRepositoryInterface

### Community 6 - "Password Reset Contracts"
Cohesion: 0.15
Nodes (8): Authenticatable, CanResetPassword, CanResetPasswordContract, DatabaseTokenRepository, ResettableUser, PasswordBrokerFactoryContract, PasswordBrokerManager, PasswordBrokerContract

### Community 7 - "Relationship Eager Loading"
Cohesion: 0.18
Nodes (14): Eloquent Model Concerns (Mixin Traits), loadIncluded(), parseIncluded(), withIncluded(), getSearchable(), whereSearch(), bootHasSlugs(), getSluggable() (+6 more)

### Community 8 - "STI Hierarchy Traits"
Cohesion: 0.21
Nodes (13): Single Table Inheritance Pattern, belongsTo(), belongsToMany(), classFromAlias(), classToAlias(), getChildModel(), getChildTypes(), getClassNameForRelationships() (+5 more)

### Community 9 - "Model Expiration Dates"
Cohesion: 0.29
Nodes (15): Attribute, CarbonImmutable, addDays(), addHours(), addMinutes(), addMonths(), addWeeks(), expired() (+7 more)

### Community 10 - "In-Memory SQLite Models"
Cohesion: 0.21
Nodes (10): Deferred Migration Pattern, createTable(), createTableSafely(), createTableWithNoData(), getRows(), getSchema(), migrate(), resolveConnection() (+2 more)

### Community 11 - "Validation & Dispatch Traits"
Cohesion: 0.21
Nodes (12): dispatch(), getDispatcher(), attributes(), getValidationFactory(), makeValidator(), messages(), rules(), validate() (+4 more)

### Community 12 - "Model Parent Polymorphism"
Cohesion: 0.33
Nodes (9): bootHasParent(), getClassNameForRelationships(), getClassNameForSerialization(), getForeignKey(), getMorphClass(), getParentClass(), getTable(), joiningTable() (+1 more)

### Community 13 - "Action Object Tests"
Cohesion: 0.33
Nodes (4): Actionable, DoubleAction, EchoAction, GreetAction

### Community 14 - "Sorting & Service Registration"
Cohesion: 0.28
Nodes (5): Query Parameter Scope Pattern, withSorting(), ServiceProvider, Builder, SupportServiceProvider

### Community 15 - "Documentation & Rationale"
Cohesion: 0.36
Nodes (9): Sluggable Configuration Pattern, laravel-support Package Overview, Action Class Pattern, Allowlist-Not-Blocklist Security Pattern, InMemory SQLite Model Pattern, Laravel 13 Boot Safety Pattern, Final Readonly Password Broker Design, PHP 8.4 Trait Property Conflict Avoidance (+1 more)

### Community 16 - "Animal Hierarchy Fixtures"
Cohesion: 0.39
Nodes (5): Animal, Cat, Dog, HasChildren, HasParent

### Community 17 - "Validation Test Fixtures"
Cohesion: 0.31
Nodes (3): OrderValidator, SimpleFormRequest, HasValidation

### Community 18 - "Dispatcher Test Fixtures"
Cohesion: 0.36
Nodes (4): EventEmitter, EventEmitter, HasDispatcher, DispatcherContract

### Community 19 - "Action Trait Core"
Cohesion: 0.43
Nodes (6): Action Pattern (Invokable Service Objects), execute(), __invoke(), make(), run(), static

### Community 20 - "Test Infrastructure & CI"
Cohesion: 0.40
Nodes (4): BaseTestCase, Pest Bootstrap Configuration, TestCase, CI Pipeline (tests.yml)

## Knowledge Gaps
- **35 isolated node(s):** `$schema`, `name`, `type`, `description`, `license` (+30 more)
  These have ≤1 connection - possible missing edges or undocumented components.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Eloquent Model Concerns (Mixin Traits)` connect `Relationship Eager Loading` to `STI Hierarchy Traits`, `Model Expiration Dates`?**
  _High betweenness centrality (0.096) - this node is a cross-community bridge._
- **Why does `ResettableUser` connect `Password Reset Contracts` to `Query Scoping & Relationships`?**
  _High betweenness centrality (0.063) - this node is a cross-community bridge._
- **Why does `DatabaseTokenRepository` connect `Password Token Storage` to `Password Reset Broker`?**
  _High betweenness centrality (0.051) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _40 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Package Configuration` be split into smaller, more focused modules?**
  _Cohesion score 0.05714285714285714 - nodes in this community are weakly interconnected._
- **Should `Eloquent Validation Rules` be split into smaller, more focused modules?**
  _Cohesion score 0.09982174688057041 - nodes in this community are weakly interconnected._
- **Should `Query Scoping & Relationships` be split into smaller, more focused modules?**
  _Cohesion score 0.1268939393939394 - nodes in this community are weakly interconnected._