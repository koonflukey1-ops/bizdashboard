# Production API Migration

1. Replace localStorage calls with a typed HTTP repository implementing the same domain operations.
2. Move authentication, permission checks, validation, totals, and auditing to the server; keep client checks for UX only.
3. Store users, products, promotions, orders, order items, movements, idempotency requests, audits, integration settings, and jobs in PostgreSQL.
4. Confirm and cancel orders in database transactions with stock row locks and unique client request IDs.
5. Hash passwords with Argon2id, use secure sessions, encrypt integration secrets, and never return costs to Staff APIs.
6. Run LINE delivery and daily summaries in a durable worker with retry/dead-letter handling.
7. Add database migrations, encrypted backups, restore drills, monitoring, rate limiting, structured logs, and end-to-end tests.
