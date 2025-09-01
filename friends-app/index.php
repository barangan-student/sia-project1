<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="bg-dark text-white text-center p-3">
        <div class="container">
            <h1>Bryan Barangan’s Friends</h1>
        </div>
    </header>
    <div class="container">
        <h1>Friends</h1>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="flex-grow-1 me-3">
                <input type="text" id="search" class="form-control" placeholder="Search friends...">
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFriendModal">Add Friend</button>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Number</th>
                        <th>URL</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Friend rows will be inserted here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Friend Modal -->
    <div class="modal fade" id="addFriendModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Friend</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addFriendForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" required placeholder="e.g., John Doe">
                            <div class="invalid-feedback" id="nameError"></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required placeholder="e.g., john.doe@example.com">
                            <div class="invalid-feedback" id="emailError"></div>
                        </div>
                        <div class="mb-3">
                            <label for="number" class="form-label">Number</label>
                            <input type="text" class="form-control" id="number" required placeholder="e.g., +1234567890">
                            <div class="invalid-feedback" id="numberError"></div>
                        </div>
                        <div class="mb-3">
                            <label for="url" class="form-label">URL</label>
                            <input type="url" class="form-control" id="url" placeholder="e.g., https://www.example.com">
                            <div class="invalid-feedback" id="urlError"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveFriend">Save Friend</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Friend Modal -->
    <div class="modal fade" id="editFriendModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Friend</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editFriendForm">
                        <input type="hidden" id="editFriendId">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" required placeholder="e.g., John Doe">
                            <div class="invalid-feedback" id="editNameError"></div>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" required placeholder="e.g., john.doe@example.com">
                            <div class="invalid-feedback" id="editEmailError"></div>
                        </div>
                        <div class="mb-3">
                            <label for="editNumber" class="form-label">Number</label>
                            <input type="text" class="form-control" id="editNumber" required placeholder="e.g., +1234567890">
                            <div class="invalid-feedback" id="editNumberError"></div>
                        </div>
                        <div class="mb-3">
                            <label for="editUrl" class="form-label">URL</label>
                            <input type="url" class="form-control" id="editUrl" placeholder="e.g., https://www.example.com">
                            <div class="invalid-feedback" id="editUrlError"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateFriend">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <!-- Toasts will be added here -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
