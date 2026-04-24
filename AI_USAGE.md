# AI Usage

This project was built with Claude (Anthropic) as a pairing assistant. Below is an honest log of how AI was used, where it helped, and where I had to correct or override it.

## How I worked

I did **not** one-shot the project with a single "build me a quiz app" prompt. Instead I used AI as a thinking partner in phases, at roughly the level of a senior engineer reviewing my approach before I wrote code. My rule of thumb was: **if I couldn't defend a line in an interview, I rewrote it myself.**

Every phase followed the same loop:

1. Describe the goal and constraints in my own words.
2. Ask the AI for a short design recommendation, not code.
3. Push back on anything that felt over-engineered.
4. Ask for code only once the design was settled.
5. Review, edit, and replace anything that was generic or wrong.

## The prompts that shaped the project

### Phase 1 — Data model

> Given the assignment spec (5 question types, media, extensibility), propose a data model. Prefer stable schemas over type-specific columns. Explain the trade-offs of a JSON config column vs. a column per rule.

The AI suggested either (a) one JSON column per question, or (b) separate tables per type. I chose (a) because (b) fragments the query surface and complicates the "list questions of a quiz" path. I added `attempts.max_score` and the `(attempt_id, question_id)` unique index myself — the AI's first draft omitted them.

### Phase 2 — Extensibility pattern

> I want a single rule: adding a new question type must not require editing a controller, model, or existing view. Propose an interface that captures every type-specific concern.

The AI suggested a `QuestionType` interface with `evaluate` and `renderInput`. I extended it with `buildConfig`, `persistOptions`, `normalizePayload`, `editorView`, and `reviewView` after realising the admin-side authoring was the bigger extensibility risk — not just scoring.

**Where I corrected it:** the AI's first draft had `renderEditor()` returning a view directly (coupled to the Blade facade). I changed this to returning a view *name string* so the controller/view layer stays in charge of rendering context.

### Phase 3 — Admin UI morphing per type

> I have 5 type editor partials. What's the simplest way to let the admin switch types without a full page reload?

The AI suggested Alpine.js. I rejected this — Alpine is a dependency for what is 10 lines of vanilla JS. The final implementation is a plain `select` + `addEventListener` loop that toggles `display` on sibling panels. Zero dependencies, zero build step.

### Phase 4 — Multiple choice scoring

> Write the evaluate() for MultipleChoiceType that supports both strict ("all correct, none wrong") and partial ("net right over total correct") scoring.

The AI's first draft gave partial credit even when the user picked every option — which is obviously wrong (you'd score everyone 100% by selecting all). I rewrote it as `max(0, right − wrong) / total`, capped at `[0, marks]`. This punishes spray-and-pray picks without going negative.

### Throughout — code comments

> Do not add explanatory comments unless the code would surprise a reader. Never write comments that describe what the code does — identifiers already do that.

The AI's default is to sprinkle "// This method handles…" comments everywhere. I stripped those. The comments that remain in the codebase are either documentation strings on doc-oriented classes or nothing — the convention is **no comments**.

## What I accepted verbatim

- The Blade skeleton for `_shared_fields.blade.php` — generic form markup, no logic.
- Tailwind utility classes for layout and spacing.
- The `Youtube::idFromUrl` regexes — three well-known patterns, easier to read than to write.
- The migration `up()` method shells.

## What I rewrote or tightened

- The `QuestionType` interface (added 3 methods, changed return types).
- `MultipleChoiceType::evaluate()` partial-credit formula (noted above).
- `QuestionController::store` — the AI's first draft branched on `$type === 'binary'` for options handling. I moved that into `persistOptions()` on each strategy and deleted the controller branching.
- The admin type-picker JS (AI suggested Alpine; replaced with 10 lines of vanilla).
- `ARCHITECTURE.md` — the first AI draft read like marketing copy ("this elegant system…"). I rewrote it to lead with the guiding principle and state trade-offs honestly, including the known limitations section.

## What I asked for but did not use

- **Automated tests** — the AI offered a full test suite. I deliberately skipped automated testing because (a) the spec doesn't require it, (b) time was better spent on docs, which are 25% of the grade, and (c) the seeded sample quiz + a documented manual QA pass is sufficient proof of correctness for this scope.
- **Livewire / Inertia for the admin editor.** Overkill for five form partials.
- **A tagging / category system for questions.** Not in the spec.

## Honest assessment of AI's contribution

AI accelerated three things significantly: (1) scaffolding boilerplate — migrations, routes, validation rules; (2) surfacing design alternatives I could compare and choose from; (3) writing first drafts of repetitive Blade partials. It was weakest at: (1) calibrating scope (tended to over-engineer), (2) getting subtle scoring logic right the first time, and (3) writing doc prose that sounded human rather than generic.

The architectural decisions — the strategy pattern, the JSON columns, the rule that controllers never branch on type — are mine. The execution was a back-and-forth where AI drafted and I edited until I could defend every file.
