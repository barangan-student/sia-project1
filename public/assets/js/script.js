document.addEventListener('DOMContentLoaded', () => {

    const friendModal = new bootstrap.Modal(document.getElementById('friendModal'));
    const friendForm = document.getElementById('friendForm');
    const friendModalLabel = document.getElementById('friendModalLabel');
    const formAction = document.getElementById('formAction');
    const friendId = document.getElementById('friendId');
    const toastContainer = document.querySelector('.toast-container');
    const liveSearchInput = document.getElementById('liveSearchInput');
    const tableBody = document.querySelector('.table tbody');

    // --- Debounce function for live search ---
    const debounce = (func, delay) => {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    };

    // --- Live Search Event Handler ---
    const handleLiveSearch = debounce((event) => {
        const searchTerm = event.target.value;
        fetch(`index.php?action=search&term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.text())
            .then(html => {
                tableBody.innerHTML = html;
            })
            .catch(() => {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading search results.</td></tr>';
            });
    }, 300);

    if(liveSearchInput) {
        liveSearchInput.addEventListener('input', handleLiveSearch);
    }


    // --- Helper function to show toasts ---
    const showToast = (message, type = 'success', friendName = '', friendIdToDelete = null) => {
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');

        let toastBody = '';
        if (type === 'danger-confirmation') {
            toastEl.className = 'toast align-items-center text-bg-danger border-0';
            toastBody = `
                <div class="d-flex">
                    <div class="toast-body">
                        Delete <strong>${friendName}</strong>?
                    </div>
                    <button type="button" class="btn btn-sm btn-light me-2 m-auto" id="confirmDeleteBtn">Yes</button>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
        } else {
            toastBody = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
        }
        
        toastEl.innerHTML = toastBody;
        toastContainer.appendChild(toastEl);

        const toast = new bootstrap.Toast(toastEl, { autohide: type !== 'danger-confirmation' });
        toast.show();

        if (type === 'danger-confirmation') {
            document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
                handleDelete(friendIdToDelete);
                toast.hide();
            });
        }
        
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    };

    // --- Modal Handling ---
    document.getElementById('addFriendBtn').addEventListener('click', () => {
        friendForm.reset();
        friendModalLabel.textContent = 'Add Friend';
        formAction.value = 'create';
        friendId.value = '';
        friendModal.show();
    });

    document.querySelector('.table').addEventListener('click', (e) => {
        if (e.target.classList.contains('editBtn')) {
            const btn = e.target;
            friendForm.reset();
            friendModalLabel.textContent = 'Edit Friend';
            formAction.value = 'update';
            friendId.value = btn.dataset.id;
            document.getElementById('name').value = btn.dataset.name;
            document.getElementById('email').value = btn.dataset.email;
            document.getElementById('phone').value = btn.dataset.phone;
            document.getElementById('url').value = btn.dataset.url;
            friendModal.show();
        }
    });

    // --- Form Submission (Create/Update) ---
    friendForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(friendForm);
        const action = formData.get('action');

        fetch('index.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.friend) {
                friendModal.hide();
                showToast(data.success, 'success');
                if (action === 'create') {
                    appendFriendToTable(data.friend);
                } else if (action === 'update') {
                    updateFriendInTable(data.friend);
                }
            } else {
                showToast(data.error || 'An error occurred.', 'danger');
            }
        })
        .catch(() => showToast('An unexpected error occurred.', 'danger'));
    });

    // --- Delete Handling ---
    document.querySelector('.table').addEventListener('click', (e) => {
        if (e.target.classList.contains('deleteBtn')) {
            const btn = e.target;
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            showToast(`Delete ${name}?`, 'danger-confirmation', name, id);
        }
    });

    const handleDelete = (id) => {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        fetch('index.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.success, 'success');
                document.getElementById(`friend-${id}`).remove();
            } else {
                showToast(data.error, 'danger');
            }
        })
        .catch(() => showToast('An unexpected error occurred.', 'danger'));
    };

    const generateFriendRowHTML = (friend) => {
        const urlLink = friend.url ? `<a href="${friend.url}" target="_blank" rel="noopener noreferrer">${friend.url}</a>` : '&mdash;';
        return `
            <td>${friend.name}</td>
            <td>${friend.email}</td>
            <td>${friend.phone}</td>
            <td>${urlLink}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-secondary editBtn" 
                        data-id="${friend.id}"
                        data-name="${friend.name}"
                        data-email="${friend.email}"
                        data-phone="${friend.phone}"
                        data-url="${friend.url}">
                    Edit
                </button>
                <button class="btn btn-sm btn-outline-danger deleteBtn" 
                        data-id="${friend.id}"
                        data-name="${friend.name}">
                    Delete
                </button>
            </td>
        `;
    };

    const appendFriendToTable = (friend) => {
        const tableBody = document.querySelector('.table tbody');
        const newRow = document.createElement('tr');
        newRow.id = `friend-${friend.id}`;
        newRow.innerHTML = generateFriendRowHTML(friend);
        
        // If the "No friends found" message is present, remove it
        const noFriendsRow = tableBody.querySelector('td[colspan="5"]');
        if (noFriendsRow) {
            noFriendsRow.parentElement.remove();
        }

        tableBody.appendChild(newRow);
    };

    const updateFriendInTable = (friend) => {
        const row = document.getElementById(`friend-${friend.id}`);
        if (row) {
            row.innerHTML = generateFriendRowHTML(friend);
        }
    };
});
