# Architecture

The app separates immutable domain types (`types.ts`), pure calculations and permission rules (`logic.ts`), browser persistence and audit helpers (`store.ts`), and React views (`App.tsx`). Pages never use hidden navigation as authorization: `Guard` and `allowed()` enforce admin routes centrally. Orders store immutable product and promotion snapshots. Stock movement records are append-only. The LINE queue is written after the order and never calls a network service.

GitHub Pages serves the Vite output. The deployment copies the entry page to `404.html` and the startup script restores deep SPA routes. All persisted values remain local to one browser.
