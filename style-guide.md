---
name: wordpress-plugin-style-guide
description: Use this skill whenever designing or building UI for a WordPress plugin admin panel, control panel, or visual builder tool, so it matches the established ArcheType Builder aesthetic. Trigger on requests to build plugin settings screens, admin dashboards, visual/drag-and-drop builders, or any wp-admin-embedded tool that should feel consistent with existing plugins. Covers color tokens, typography, spacing, component patterns, and layout structure.
---

# WordPress Plugin Style Guide

A dense, structural, developer-facing design language for plugin UI that lives inside `wp-admin`. Built on one functional accent color, light neutral surfaces, and precise multi-pane workspaces. No gradients, no drop shadows on ordinary elements, no decorative color.

## Scope: what to design vs. what WordPress gives you for free

The dark admin menu rail and admin bar are **WordPress core chrome** (default `#1d2327`), not part of this system. Do not recreate, restyle, or theme them unless explicitly asked to override the admin color scheme. Design only the surface to the right of/below that chrome: your plugin's own header, toolbar, and workspace.

If a tool is ever built **outside** wp-admin (standalone app), do not invent a new dark rail to replace WordPress's. Default to light chrome consistent with the rest of this system instead.

## Color tokens

```
--canvas:          #F5F6F8   workspace/background behind panels
--panel:            #FFFFFF   cards, blocks, side panels
--header-bar:       #4457E8   plugin's own title bar — the ONE strong-color surface
--accent:           #3B5FE0   primary buttons, active tabs, links, toggle-on, selected states
--accent-hover:     #2F4EC7
--border:           #E5E7EB   default dividers, card/input borders
--border-strong:    #D8DBE0   input borders, more prominent dividers
--text:             #1F2328   headings, body copy
--text-secondary:   #6B7280   labels, helper text
--text-muted:       #9CA3AF   icons, placeholders, breadcrumbs
```

Rules:
- The accent color (`#3B5FE0`) is used **functionally only** — primary actions, active/selected state, links, focus rings, toggle-on. Never decoratively (no accent backgrounds on static content, no accent text for emphasis alone).
- Everything you design is light. Do not add new dark panels/surfaces of your own — the header bar is the one exception, and it stays a simple bar (title + primary actions), never bleeding into nav or content areas.
- Depth comes from a 1px border and a subtle background-shade step (`--panel` vs `--canvas`), not shadows. Reserve shadow/elevation for modals only.

## Typography

System UI sans-serif stack only. No display face. Hierarchy comes from weight and size, not typeface variety.

| Role | Size | Weight | Notes |
|---|---|---|---|
| Panel/page title | 18–20px | 700 | e.g. "Control Panel", "Data-Table Settings" |
| Block/section title | 14px | 600 | Component names inside the tree |
| Body | 13px | 400 | Descriptions, helper copy |
| Eyebrow / micro-label | 11px | 700 | UPPERCASE, letter-spacing ~0.06em, `--text-secondary`. Used only for section headers and table column headers — never body copy |
| Code / paths | 12.5px | 400 | Monospace (`SFMono-Regular, Consolas, Menlo, monospace`), `--text-secondary` |

## Spacing & radius

- Base unit: 4px. Padding steps: 4 / 8 / 12 / 16 / 24px.
- Row rhythm inside panels/lists: 8–12px vertical. Section-to-section gaps: 24px+.
- Row height in dense lists/tables: ~36–44px.
- Radius: 4–6px on controls (buttons, inputs, list rows). 8px on containers (cards, blocks, modals). Nothing pill-shaped except toggles and segmented controls.
- Borders: 1px solid `--border` (or `--border-strong` on inputs) everywhere structure needs to read.

## Layout structure

```
[ WP admin chrome — not yours ] [ Header bar (#4457E8, title + actions) ]
                                 [ Toolbar with tabs (light, tab row) ]
                                 [ Workspace: 2–3 panes ]
                                   - Left: tree/list panel (~20–25% width)
                                   - Center: canvas/preview (flexible)
                                   - Right: contextual properties panel (~24–28% width),
                                     itself tabbed (e.g. Config / Conditions / Events / Style)
```

- Selecting an item in the tree swaps the right panel's contents — don't inline-edit complex config directly in the canvas.
- Use a breadcrumb (`Root / Stack / Stack / Data-Table`, `--text-muted` with the current segment in `--accent`) to show position in a nested structure.

## Component patterns

**Buttons**
- Primary: solid `--accent` fill, white text, 6px radius, 600 weight, ~8px/14px padding.
- Secondary: white fill, `--border-strong` border, `--text` label.
- Danger: white fill, red text/border (`#DC2626` / `#F3D2D2`), reserved for destructive actions.
- Icon-only: transparent fill, `--border` outline, `--text-secondary` glyph, square ~30px.

**Form fields**
- Label (12px, 600, `--text-secondary`) above input.
- Input: white fill, `--border-strong` 1px border, 6px radius, 13px text.
- Focus: 2px light-indigo outline ring + `--accent` border.
- Optional helper/hint text below in `--text-muted`, 11px.

**Toggle switch**: pill, `--accent` when on / `#D1D5DB` when off, white knob.

**Segmented control**: pill group on a `#F0F1F4` track, active segment gets `--accent` fill + white text.

**Tabs**: flat row, bottom-border indicator only. Active tab = `--accent` text + `--accent` 2px underline. Inactive = `--text-secondary`, no underline.

**List / tree row** (the core repeating unit of the whole builder): always the same control cluster, in this order, left to right:
```
[drag handle ⋮⋮] [visibility toggle ◎] [type/label identity] ... [spacer] ... [expand ▾] [duplicate ⧉] [delete ×]
```
Consistency of this cluster across every row is what makes deeply nested trees scannable — never omit or reorder it.

**Schema/column builder table**: header row with uppercase 10.5px column labels on `#FAFAFB`; each data row is a drag handle + inline-editable inputs + a sort/delete icon; 1px row dividers, no zebra striping.

**Data table preview / loading state**: skeleton bars (`#E9EAED`, 4px radius, ~80% row width) in place of unloaded cell content — never a spinner for row-level loading.

## Non-negotiables

1. One accent color, used only for function (action/selection/link), never decoration.
2. No new dark surfaces — the header bar is the sole exception and stays simple.
3. Every list/tree row uses the same left-to-right control cluster.
4. Structure via borders + background-shade steps, not shadows.
5. Uppercase micro-labels (11px/700/letter-spaced) only for section and column headers, never body text.
6. Complex configuration always goes in the contextual right-hand panel, never inline in the canvas.
