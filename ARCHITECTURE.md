# Architecture

## Guiding principle

The assignment explicitly penalises "hardcoded or non-extensible logic." Every design decision below was made in service of a single rule:

> **Adding a new question type must not require editing any controller, model, migration, or existing view.**

If that rule holds, the scoring logic, admin UI, taker UI, and result UI all extend cleanly. The rest of this document explains how that rule is enforced.

---

## Data model

Five tables. Two JSON columns do the heavy lifting.

```
quizzes(id, title, description)
  └── questions(id, quiz_id, type, body_html, image_path, video_url, marks, config JSON, position)
        └── options(id, question_id, label, image_path, is_correct, position)     # choice-based types only
  └── attempts(id, quiz_id, started_at, submitted_at, score, max_score)
        └── answers(id, attempt_id, question_id, payload JSON, marks_awarded)
```

### Why `questions.config` is JSON

Each type stores different correctness rules:

| Type | `config` shape |
|------|----------------|
| Binary | `{"correct": "yes"\|"no"}` |
| Single Choice | `{}` (correctness lives in `options.is_correct`) |
| Multiple Choice | `{"scoring": "strict"\|"partial"}` |
| Number | `{"expected": 120, "tolerance": 0}` |
| Text | `{"expected": "Eloquent", "match": "exact"\|"ci"\|"contains"\|"regex"}` |

The alternative — a separate column per rule (`binary_correct`, `number_expected`, `number_tolerance`, `text_expected`, `text_match`, …) — turns every new type into a schema migration, breaks Laravel's automatic fillable mass-assignment, and leaves most rows with mostly-NULL columns. A single JSON column keeps the schema stable and lets each strategy class own the shape of its own config.

### Why `answers.payload` is JSON

The same reasoning in reverse. A user's answer is:

| Type | payload shape |
|------|---------------|
| Binary | `{"value": "yes"}` |
| Single Choice | `{"option_id": 42}` |
| Multiple Choice | `{"option_ids": [42, 43, 45]}` |
| Number | `{"value": 120}` |
| Text | `{"value": "eloquent"}` |

Without JSON, you either need five `answer_*` columns (most NULL per row) or five separate answer tables (foreign-key complexity explodes). One JSON column + a strategy that knows its shape is strictly simpler.

### Additional schema decisions

- **`attempts.max_score`** is captured at submission time, not read from the quiz on result display. Without this, if an admin later tweaks a question's marks, old results would silently misreport.
- **`(attempt_id, question_id)` unique index** on `answers` prevents duplicate answers from a replayed form.
- **`cascadeOnDelete`** everywhere — deleting a quiz cleans up questions, options, attempts, and answers atomically.
- **`position` columns** on questions and options support ordered display and future drag-reorder without schema changes.

---

## The QuestionType strategy

One interface, one registry, one class per type.

### Contract

`app/QuestionTypes/Contracts/QuestionType.php`

```php
interface QuestionType
{
    public function slug(): string;                                          // unique key (e.g. 'number')
    public function label(): string;                                         // human label for the type dropdown

    public function buildConfig(array $input): array;                        // form data -> questions.config
    public function persistOptions(Question $question, array $input): void;  // manage options rows (or no-op)
    public function normalizePayload(mixed $raw): array;                     // raw form input -> answers.payload
    public function evaluate(Question $question, array $payload): float;     // marks awarded, 0..question->marks

    public function editorView(): string;                                    // Blade partial for admin form
    public function inputView(): string;                                     // Blade partial for taker form
    public function reviewView(): string;                                    // Blade partial for result page
}
```

Five concrete classes under `app/QuestionTypes/Types/` implement this:

- `BinaryType`
- `SingleChoiceType`
- `MultipleChoiceType`
- `NumberType`
- `TextType`

### Registry

`app/QuestionTypes/QuestionTypeRegistry.php` is a thin map `slug -> strategy`. Controllers and views resolve it from the container and ask `$registry->for($question->type)` — they never branch on the type string themselves.

### Service provider

`app/Providers/QuestionTypeServiceProvider.php` binds the registry as a singleton and registers all five types on boot. This is the single wiring point; adding a type means adding **one line** here.

---

## Request lifecycle

### Creating a question

```
POST /quizzes/{quiz}/questions
  │
  QuestionController@store
  │
  ├── validateBase(request)            # common fields: body_html, marks, image, video_url, type
  ├── $type = registry->for(type)
  ├── quiz->questions()->create([
  │     ...base,
  │     config => $type->buildConfig(request->all())   # strategy shapes the JSON
  │   ])
  ├── $type->persistOptions($question, request->all())  # strategy saves options (or no-op)
  └── persistOptionImages($question, request)           # shared handler for option file uploads
```

Zero type branching in the controller. `buildConfig` and `persistOptions` are the two extension points that absorb all type-specific save logic.

### Taking a quiz

`GET /quizzes/{quiz}/take` creates the `Attempt` on page load (so timing and resume work cleanly later) and renders `attempts/take.blade.php`. The view loops over questions and `@include($type->inputView(), ...)` — never `@if ($q->type === 'binary')`.

### Submitting a quiz

```
POST /attempts/{attempt}/submit
  │
  AttemptController@submit
  │
  for each question in attempt->quiz->questions:
    ├── $type = registry->for($question->type)
    ├── $payload = $type->normalizePayload(request input for this question)
    ├── $marks   = $type->evaluate($question, $payload)
    └── attempt->answers()->create(...)
  │
  update attempt.submitted_at, .score, .max_score
```

Same pattern on the result page: `@include($type->reviewView(), ...)`.

---

## Scoring rules

| Type | Rule |
|------|------|
| Binary | Full marks if payload value matches `config.correct`, else 0. |
| Single Choice | Full marks if picked option has `is_correct = 1`, else 0. |
| Multiple Choice — strict | Full marks only if picked set **equals** correct set. |
| Multiple Choice — partial | `max(0, right_picked − wrong_picked) / total_correct × marks`, rounded to 2 decimals. |
| Number | Full marks if `abs(value − expected) ≤ tolerance`, else 0. |
| Text | Full marks if the chosen match mode (`exact`, `ci`, `contains`, `regex`) returns true. |

The admin picks strict vs partial per multiple-choice question; this is stored in that question's `config`.

---

## Extending with a new type

Say you want to add **Matching** questions (drag-match two columns). The steps:

1. **Write a class** at `app/QuestionTypes/Types/MatchingType.php` implementing the `QuestionType` interface. Decide what goes in its `config` (e.g. the pair list) and what shape its `payload` takes (e.g. `{"pairs": [[leftId, rightId], ...]}`).
2. **Register it** in `QuestionTypeServiceProvider::register()` with one line:
   ```php
   ->register(new MatchingType)
   ```
3. **Add three Blade partials** under the paths returned by the class's `editorView()`, `inputView()`, and `reviewView()`.

That is the whole change. No migration, no controller edit, no view edit outside the three new partials. The type dropdown in the admin UI picks up the new option automatically because it's generated from `registry->options()`.

---

## What was deliberately **not** built

Constraining scope is itself a design decision.

- **No auth** — the assignment says auth is not required, and every extra layer is surface area that can fail in the reviewer's browser.
- **No WYSIWYG editor** — the spec allows rich text *or* HTML. A `<textarea>` with `{!! !!}` rendering meets the spec with zero dependencies. Swapping in TinyMCE/Quill later is a one-file change in `_shared_fields.blade.php`.
- **No automated tests** — the spec does not require them. Correctness is verified by the seeded Sample Quiz plus a documented manual QA pass. The strategy classes are pure enough that adding unit tests later is straightforward if desired.
- **No timer, no pagination, no review-before-submit screen** — not in the spec.

---

## Trade-offs and known limitations

- **Orphan attempts.** Hitting *Take Quiz* and then closing the tab leaves an attempt row with `submitted_at = NULL`. Intentional: it enables resume and timing. A background cleanup job for attempts older than N hours would suffice in production.
- **Option images are keyed by position.** The editor uses array indices (`option_images[0]`, `option_images[1]`, …) so reordering during an edit can associate the wrong file to the wrong option. Acceptable for this scope; in production I would switch to stable per-row IDs.
- **Regex text matching is unsandboxed.** Admins supplying malicious patterns could trigger catastrophic backtracking. Acceptable for an unauthenticated admin UI used by trusted users; for production I would either drop regex mode or enforce `preg_match` with a timeout extension.
- **`payload` JSON is not validated at the DB layer.** The strategy's `normalizePayload()` is the type-level guard. The strategy is always the first thing to touch raw input, so schema-level validation was judged unnecessary.
