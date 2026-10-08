<!-- airc v0.4.1 - managed file, do not edit -->

# Accessibility

Accessibility is a default, not an afterthought. Pairs with `js.md` / `react.md`.

## Structure

- Use `<main>`, `<nav>`, `<header>`, `<footer>` for their semantic role instead of a generic `<div>`.
- Don't skip heading levels (`h1` -> `h2` -> `h3`) - they form the page's outline.

## Keyboard

- Every interactive element must be reachable and operable via keyboard, not just the mouse.
- Prefer a real `<button>` over a `div`/`span` with a click handler for icon-only controls (e.g. a password show/hide toggle, a clear-input button), and add an accessible name (`aria-label`).

## Forms

- Give every input an associated `<label>` - a placeholder alone isn't one.

## Images

- Give meaningful images a descriptive `alt`; mark purely decorative ones with `alt=""`.
