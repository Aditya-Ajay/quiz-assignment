# Dynamic Quiz System

A flexible Laravel quiz application that supports five question types (Binary, Single Choice, Multiple Choice, Number, Text), media uploads, embedded YouTube videos, and per-type evaluation logic. Built around a **strategy + registry** pattern so adding a new question type later requires writing one class — no controller or view changes.

## Requirements

- PHP 8.2+
- Composer
- SQLite (default) or MySQL 8
- Node is **not** required — styling comes from a Tailwind CDN script.

## Setup

```bash
git clone <repo-url> quiz-assignment
cd quiz-assignment

composer install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed

php artisan storage:link

php artisan serve
```

Visit `http://127.0.0.1:8000` — you will land on the quiz list and see a pre-seeded **Sample Quiz** containing one question of every supported type.

### Switching to MySQL

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quiz
DB_USERNAME=root
DB_PASSWORD=
```

Then re-run `php artisan migrate:fresh --seed`.

## Using the app

1. **Browse quizzes** at `/quizzes`.
2. **Create a quiz** — title + description.
3. **Add questions** from the quiz edit page. The editor form morphs based on the selected type.
4. **Take a quiz** — click *Take Quiz* on any quiz with at least one question. A fresh `Attempt` row is created on page load.
5. **Submit** — the server evaluates every answer against its question's rules and redirects to a result page with per-question breakdown and total score.

## Question types

| Type | Correctness stored in | Scoring |
|------|------------------------|---------|
| Binary (Yes/No) | `questions.config.correct` | Exact match |
| Single Choice | `options.is_correct` (one row) | Picked option must be the correct one |
| Multiple Choice | `options.is_correct` (multiple rows) | `strict` (all-or-nothing) or `partial` (weighted) — chosen per question |
| Number | `questions.config.expected` + `tolerance` | `|given − expected| ≤ tolerance` |
| Text | `questions.config.expected` + `match` | `exact` / `ci` / `contains` / `regex` |

## Project layout

```
app/
  Models/                          # Quiz, Question, Option, Attempt, Answer
  QuestionTypes/
    Contracts/QuestionType.php     # strategy interface
    QuestionTypeRegistry.php       # slug -> strategy resolver
    Types/                         # BinaryType, SingleChoiceType, MultipleChoiceType, NumberType, TextType
  Http/Controllers/                # QuizController, QuestionController, AttemptController
  Providers/QuestionTypeServiceProvider.php
  Support/Youtube.php              # id extraction + embed helper

database/migrations/               # 5 feature migrations
database/seeders/DatabaseSeeder.php

resources/views/
  layouts/app.blade.php
  quizzes/                         # index, create, edit
  questions/
    _shared_fields.blade.php
    edit.blade.php
    editor/{type}.blade.php        # admin authoring UI per type
    input/{type}.blade.php         # taker form widget per type
    review/{type}.blade.php        # result page breakdown per type
  attempts/                        # take, result
```

## Adding a sixth question type

See [ARCHITECTURE.md](./ARCHITECTURE.md#extending-with-a-new-type).

## Documentation

- [ARCHITECTURE.md](./ARCHITECTURE.md) — design decisions, data model, extensibility story.
- [AI_USAGE.md](./AI_USAGE.md) — prompts used, corrections applied, what was accepted vs rewritten.

## Timeline

Estimated and actual: **~5 calendar days at evening pace (~25 hours of focused work)**.

| Day | Phase | Hours |
|-----|-------|-------|
| 1 | Migrations, models, relationships, `QuestionType` contract + 5 strategies | 6 |
| 2 | Admin CRUD, per-type editor partials, media uploads | 6 |
| 3 | Taker flow, submission, evaluation, result page | 5 |
| 4 | Seeders, styling polish, manual QA | 4 |
| 5 | README + ARCHITECTURE + AI_USAGE, deploy | 4 |

The load-bearing day was Day 1: getting the `QuestionType` abstraction right up front made the controllers and views thin, which is where most of the "extensibility" grade lives.
