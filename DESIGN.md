# SIAKAD Design Direction

## Product character

SIAKAD supports day-to-day school administration and teaching. The interface should feel dependable, calm, and easy to scan during routine work. Navigation is dense because it reflects real school workflows; hierarchy comes from grouping and clear active states, not decorative cards.

## Design read

- ENERGY 2: restrained surfaces, with a clear forest-green action hierarchy.
- RHYTHM 2: vary spacing and composition to match each task, while keeping data-heavy pages predictable.
- MOTION 1: use brief state transitions only; no decorative loops.

The shared visual motif is the forest-green navigation and selection state. It ties school workflows together without competing with page data.

## Color

Use the existing light and dark theme utilities, with a forest-green identity drawn from the school and book context.

| Token | Value | Use |
| --- | --- | --- |
| Brand 900 | `#173C32` | Sidebar and highest-emphasis brand surface |
| Brand 800 | `#1D4B3E` | Strong brand actions and hover states |
| Brand 700 | `#255B4B` | Primary actions and selected navigation |
| Brand 600 | `#2F725D` | Interactive accents |
| Brand 100 | `#DCEBE3` | Quiet brand-tinted surfaces |
| Brand 50 | `#F1F7F3` | Subtle brand background |
| Accent | `#8A5E13` | Keyboard focus and occasional emphasis |
| Page neutral | `#F5F6F2` | Main light-theme canvas |
| Surface | `#FFFFFF` | Content surfaces and menus |
| Text | Slate 900 | Main text |

Use red, amber, and blue only for real destructive, warning, or informational states. Keep most content surfaces neutral. Do not use gradients, glow, or decorative status dots.

## Typography

Keep Figtree, already loaded by the application. Its open forms remain readable in compact navigation, forms, and data-heavy pages. Use regular body text, medium-weight labels, and semibold page titles. Avoid all-caps labels as a default.

## Spacing and shape

Use Tailwind's existing spacing scale. The shell uses a 16-unit top bar, 12-unit navigation insets, and compact 2-to-6-unit control spacing. Page-specific screens retain their existing content spacing.

Use `rounded-md` for controls and `rounded-lg` for grouped surfaces. Keep surfaces flat; reserve a restrained shadow for overlays such as dropdowns and dialogs.

## Shared components

- The sidebar is the persistent navigation surface. Role directives and route destinations remain unchanged.
- Brand-green selection marks the current route; muted text separates inactive links from group headings.
- The top bar holds the mobile navigation control and the existing profile menu.
- Page headers keep their existing Blade-provided title and gain a small route-derived breadcrumb.
- Primary, secondary, and destructive buttons remain distinct by action. Inputs keep visible labels, validation messages, and focus states.
- Interactive elements retain their existing routes, forms, Alpine state, and event behavior.

## Responsive rules

- Below the desktop navigation breakpoint, the sidebar becomes a fixed, off-canvas panel with a dimmed backdrop. The top-bar control opens it; the panel control, backdrop, or Escape closes it.
- At desktop widths, the sidebar keeps its existing expanded and icon-only states.
- The content column may shrink and scroll vertically without forcing the shell wider than the viewport.
- Keep navigation and controls reachable by keyboard, provide visible focus, and keep touch controls comfortably sized.
- Do not impose a new width or grid on page-specific content; each existing screen remains responsible for its own responsive layout.

## Scope

This direction applies across public, authenticated, profile, and module views, including print-oriented screens where practical. The shared shell and reusable Blade controls establish the common foundation; each page keeps its own information hierarchy and responsive needs.

The redesign is presentation-only. Preserve existing routes, controllers, models, schemas, middleware, authentication, authorization, role-based navigation, form contracts, data, and JavaScript interactions. Do not invent dashboard metrics or records. Keep page-level access rules in the existing route/controller layer.
