Final PRD: Bryan Barangan’s Friends

1. Overview

A simple CRUD web application for managing a personal list of friends.
Users can add, search, edit, and delete friends using a minimal, modern, responsive interface with modals for forms and toasts for feedback.
The project is pure PHP + SQLite, no APIs are used.

⸻

2. Objectives
	•	Lightweight CRUD application.
	•	Modern, minimal design (clean typography, soft colors, flat/rounded buttons).
	•	Modals for Add/Edit forms.
	•	Toasts for all feedback (success, error, and interactive delete confirmation).
	•	Per-record Edit/Delete actions in the table.
	•	Responsive design for desktop & mobile.
	•	Organized project inside its own folder, separate from unrelated files/folders (e.g., .gemini, node_modules, README files).

⸻

3. Scope

Features
	•	Database
	•	SQLite database.
	•	Table name prefix: cezar_.
	•	Main table: cezar_friends.
	•	CRUD Operations
	•	Create: Add new friend via modal.
	•	Read/Search: View/search friends by name, email, number, or URL.
	•	Update: Edit friend via modal.
	•	Delete: Remove friend via interactive confirmation toast.
	•	UI/UX
	•	Modals hidden on page load. Open on Add/Edit click; close on Cancel or clicking outside.
	•	Per-record Edit/Delete buttons beside each row.
	•	Search bar for filtering by name/email/number/URL.
	•	URL displayed as clickable hyperlink (opens in new tab).
	•	Toast messages for success/error/confirmation.

⸻

4. Database Schema

Table: cezar_friends

Field	Type	Constraints	Notes
id	INTEGER	PRIMARY KEY, AUTOINCREMENT	Unique identifier
name	VARCHAR(100)	NOT NULL	Friend’s name
email	VARCHAR(255)	NOT NULL, UNIQUE	Friend’s email
number	VARCHAR(15)	NOT NULL, UNIQUE (E.164 format)	Phone number
url	VARCHAR(255)	NULL, UNIQUE	Friend’s profile/website


⸻

5. User Stories
	1.	Add Friend: User adds a friend using a modal. See success toast after saving.
	2.	View Friends: User views all friends in a clean table; URLs are clickable.
	3.	Search: User searches friends by name, email, number, or URL (case-insensitive).
	4.	Edit Friend: User edits a friend via modal; sees success toast on save.
	5.	Delete Friend: User clicks Delete → interactive confirmation toast with Yes/Cancel → shows success toast if confirmed.
	6.	Modal Behavior: Modals hidden by default; open on Add/Edit click; close on Cancel or clicking outside.
	7.	Edge Cases:
	•	Duplicate email/number prevented; error toast shown.
	•	Optional URL displayed as blank or “—” if not provided.
	•	Deleting last record handled gracefully; table empty state visible.

⸻



⸻

7. Implementation Notes
	•	Backend: PHP + SQLite (no APIs).
	•	Frontend:
	•	HTML5 + CSS3 + JS.
	•	Bootstrap 5 recommended (modals, toasts, responsive grid).
	•	Table: URL column clickable (opens new tab).
	•	Per-record Edit/Delete buttons.
	•	Modals:
	•	Hidden on load; open only on button click; close on Cancel or outside click.
	•	Title dynamic: “Add Friend” or “Edit Friend”.
	•	Edit modal pre-fills friend data.
	•	Toasts:
	•	Positioned top-right.
	•	Interactive delete toast: Yes/Cancel buttons.
	•	Success toasts for Add/Edit/Delete auto-hide after 3–5 seconds.
	•	Validation:
	•	Email format.
	•	Phone number in E.164 format (+639XXXXXXXXX).
	•	URL format (https://...) if provided.
	•	Prevent duplicate email or number.
	•	Interaction:
	•	Table refresh after CRUD.
	•	Smooth UX for modals and toasts.
	•	Empty table handled gracefully.
	•	Accessibility:
	•	Keyboard navigable modals and buttons.
	•	Screen-reader friendly toast messages.
	•	Responsive layout for all screen sizes.
	•	Edge Cases:
	•	Duplicate entries → error toast.
	•	Optional URL blank handled.
	•	Last record deletion → table empty state visible.
	•	Multiple toasts stacked correctly.
	•	Project Organization:
	•	All project files created in a dedicated folder.
	•	Avoid including unrelated files/folders (e.g., .gemini, node_modules, README files) in this folder.