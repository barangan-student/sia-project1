document.addEventListener('DOMContentLoaded', function() {
    const addFriendModal = new bootstrap.Modal(document.getElementById('addFriendModal'));
    const editFriendModal = new bootstrap.Modal(document.getElementById('editFriendModal'));

    let friends = [];

    function fetchFriends(search = '') {
        fetch(`friends.php?search=${search}`)
            .then(response => response.json())
            .then(data => {
                friends = data;
                renderFriends();
            });
    }

    function renderFriends() {
        const tableBody = document.querySelector('.table tbody');
        tableBody.innerHTML = '';
        friends.forEach(friend => {
            const row = `
                <tr>
                    <td>${friend.name}</td>
                    <td>${friend.email}</td>
                    <td>${friend.number}</td>
                    <td><a href="${friend.url}" target="_blank">${friend.url}</a></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editFriend(${friend.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteFriend(${friend.id})">Delete</button>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }

    function clearErrors(formType) {
        const formId = formType === 'add' ? 'addFriendForm' : 'editFriendForm';
        const form = document.getElementById(formId);
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        form.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
    }

    function displayError(inputElement, message) {
        inputElement.classList.add('is-invalid');
        inputElement.nextElementSibling.textContent = message;
    }

    function validateForm(formType, name, email, number, url) {
        clearErrors(formType);
        let isValid = true;

        const nameInput = document.getElementById(formType === 'add' ? 'name' : 'editName');
        const emailInput = document.getElementById(formType === 'add' ? 'email' : 'editEmail');
        const numberInput = document.getElementById(formType === 'add' ? 'number' : 'editNumber');
        const urlInput = document.getElementById(formType === 'add' ? 'url' : 'editUrl');

        if (!name) {
            displayError(nameInput, 'Name is required.');
            isValid = false;
        }
        if (!email) {
            displayError(emailInput, 'Email is required.');
            isValid = false;
        } else if (!/^[\w-]+(?:\.[\w-]+)*@(?:[\w-]+\.)+[a-zA-Z]{2,7}$/.test(email)) {
            displayError(emailInput, 'Invalid email.');
            isValid = false;
        }
        if (!number) {
            displayError(numberInput, 'Number is required.');
            isValid = false;
        } else if (!/^\+[1-9]\d{1,14}$/.test(number) || number.length < 2 || number.length > 16) {
            displayError(numberInput, 'Invalid phone number. Use E.164 format.');
            isValid = false;
        }
        if (url && !/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/.test(url)) {
            displayError(urlInput, 'Invalid URL.');
            isValid = false;
        }

        return isValid;
    }

    document.getElementById('search').addEventListener('input', function() {
        fetchFriends(this.value);
    });

    document.getElementById('saveFriend').addEventListener('click', function() {
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const number = document.getElementById('number').value;
        const url = document.getElementById('url').value;

        if (!validateForm('add', name, email, number, url)) {
            return;
        }

        const formData = new FormData();
        formData.append('name', name);
        formData.append('email', email);
        formData.append('number', number);
        formData.append('url', url);

        fetch('friends.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addFriendModal.hide();
                fetchFriends();
                showToast(data.message, 'success');
            } else {
                // Server-side validation errors
                if (data.message.includes('Duplicate')) {
                    if (data.message.includes('email')) {
                        displayError(document.getElementById('email'), data.message);
                    }
                    if (data.message.includes('number')) {
                        displayError(document.getElementById('number'), data.message);
                    }
                } else {
                    showToast(data.message, 'danger');
                }
            }
        });
    });

    window.editFriend = function(id) {
        clearErrors('edit'); // Clear errors when opening edit modal
        fetch(`friends.php?id=${id}`)
            .then(response => response.json())
            .then(friend => {
                document.getElementById('editFriendId').value = friend.id;
                document.getElementById('editName').value = friend.name;
                document.getElementById('editEmail').value = friend.email;
                document.getElementById('editNumber').value = friend.number;
                document.getElementById('editUrl').value = friend.url;
                editFriendModal.show();
            });
    }

    document.getElementById('updateFriend').addEventListener('click', function() {
        const id = document.getElementById('editFriendId').value;
        const name = document.getElementById('editName').value;
        const email = document.getElementById('editEmail').value;
        const number = document.getElementById('editNumber').value;
        const url = document.getElementById('editUrl').value;

        if (!validateForm('edit', name, email, number, url)) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('id', id);
        formData.append('name', name);
        formData.append('email', email);
        formData.append('number', number);
        formData.append('url', url);

        fetch('friends.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                editFriendModal.hide();
                fetchFriends();
                showToast(data.message, 'success');
            } else {
                // Server-side validation errors
                if (data.message.includes('Duplicate')) {
                    if (data.message.includes('email')) {
                        displayError(document.getElementById('editEmail'), data.message);
                    }
                    if (data.message.includes('number')) {
                        displayError(document.getElementById('editNumber'), data.message);
                    }
                } else {
                    showToast(data.message, 'danger');
                }
            }
        });
    });

    function showToast(message, type) {
        const toastContainer = document.querySelector('.toast-container');
        const toast = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-progress"></div>
            </div>
        `;
        toastContainer.innerHTML += toast;
        const toastEl = toastContainer.lastElementChild;
        const bsToast = new bootstrap.Toast(toastEl, { delay: 3000 });
        bsToast.show();
    }

    window.deleteFriend = function(id) {
        const toastContainer = document.querySelector('.toast-container');
        // Remove any existing delete confirmation toasts
        toastContainer.querySelectorAll('.delete-confirm-toast').forEach(t => t.remove());

        const toast = `
            <div class="toast delete-confirm-toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body">
                    Are you sure you want to delete this friend?
                    <div class="mt-2 pt-2 border-top">
                        <button type="button" class="btn btn-primary btn-sm" id="confirmDelete">Yes, Delete</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="toast">Cancel</button>
                    </div>
                </div>
            </div>
        `;
        toastContainer.innerHTML += toast;
        const toastEl = toastContainer.lastElementChild;
        const bsToast = new bootstrap.Toast(toastEl);
        bsToast.show();

        // Use event delegation or ensure the event listener is added only once
        const confirmDeleteBtn = toastEl.querySelector('#confirmDelete');
        confirmDeleteBtn.onclick = function() {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            fetch('friends.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchFriends();
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message, 'danger');
                }
                // These lines should be executed regardless of success or failure
                bsToast.hide();
                toastEl.addEventListener('hidden.bs.toast', function () {
                    toastEl.remove();
                }, { once: true });
            });
        }

        // Handle cancel button click to remove the toast
        toastEl.querySelector('[data-bs-dismiss="toast"]').onclick = function() {
            bsToast.hide();
            toastEl.addEventListener('hidden.bs.toast', function () {
                toastEl.remove();
            }, { once: true }); // Use { once: true } to ensure it runs only once
        };
    }

    fetchFriends();
});

