📌 Product Requirements Document (PRD) – Bryan Barangan’s Friends (PHP + SQLite)

1. Purpose

Bryan Barangan’s Friends is a secure PHP + SQLite web application to manage a list of friends.
It provides CRUD functionality, enforces strict data validation & security, and features a modern UI with modals and toast notifications.

⸻

2. Database Design
	•	Database file: barangan_friends.db
	•	Table: barangan_friends

Schema

CREATE TABLE IF NOT EXISTS barangan_friends (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    phone TEXT UNIQUE,
    url TEXT
);

Field Rules
	•	id → Primary key, hidden in UI.
	•	name → Required text.
	•	email → Unique, lowercase, trimmed, valid email format.
	•	phone → Unique, digits only (no symbols/spaces).
	•	url → Lowercase, trimmed, must start with https://, auto-add if missing, reject http://.

⸻

3. Features

Backend Features
	•	CRUD operations (createFriend, readFriends, updateFriend, deleteFriend).
	•	Validation & Normalization:
	•	Email → must be unique, lowercase, valid format.
	•	Phone → must be unique, digits only.
	•	URL → must be normalized and start with https://.
	•	Security:
	•	PDO prepared statements for all queries (SQL injection protection).
	•	Escape all output (htmlspecialchars) for XSS protection.
	•	Suppress database errors from frontend.

Frontend Features
	•	Page Title & Header: "Bryan Barangan’s Friends" → "Friends List".
	•	Friends Table (ID hidden):
	•	Columns: Name, Email, Phone, URL (clickable), Actions.
	•	Actions:
	•	Add Friend Button → Opens modal for adding.
	•	Edit Button → Opens modal with pre-filled form.
	•	Delete Button → Shows toast confirmation.
	•	Modal Forms:
	•	Add Friend Modal → Blank form.
	•	Edit Friend Modal → Pre-filled form.
	•	Inline validation for invalid input.
	•	Toast Notifications:
	•	Success Toasts → Friend added/updated/deleted.
	•	Error Toasts → Invalid email, invalid phone, duplicate email/phone, invalid URL.
	•	Delete Confirmation Toast → “Are you sure you want to delete [Friend Name]?” with confirm/cancel buttons.

⸻

4. User Flow
	1.	User loads index.php → sees list of friends (no IDs shown).
	2.	User clicks Add Friend → modal opens → submits → validated → success/error toast.
	3.	User clicks Edit → modal opens → updates → validated → success/error toast.
	4.	User clicks Delete → toast confirmation → confirm/cancel → success toast if deleted.

⸻

5. Deliverables
	•	barangan_friends.db (schema with unique phone + unique email).
	•	db.php (secure PDO connection).
	•	CRUD functions in PHP with validation.
	•	index.php with:
	•	Friends table (without ID).
	•	Add Friend button.
	•	Add/Edit modals.
	•	Toast notifications for success, errors, and delete confirmation.

⸻

6. Security
	•	SQL Injection → Prevented via prepared statements.
	•	XSS → Prevented with htmlspecialchars.
	•	Validation → Required for all inputs.
	•	DB Constraints → Enforce uniqueness of email and phone at schema level.