const apiUrl = '/tasks'; // Adjust to your API route
let currentPage = 0;
const pageSize = 10;
let allTags = [];
// Initialize modal only once


// Load tasks on page load
window.addEventListener('DOMContentLoaded', loadTasks);

async function loadTasks() {
    const tbody = document.getElementById('taskTableBody');
    const loadingRow = document.getElementById('loadingRow');

    // Clear previous rows (except loading)
    tbody.innerHTML = '';
    if (loadingRow) tbody.appendChild(loadingRow);
    if (loadingRow) loadingRow.style.display = '';

    // Filters
    const status = document.getElementById('filterStatus').value;
    const priority = document.getElementById('filterPriority').value;
    const keyword = document.getElementById('filterKeyword').value;
    const from = document.getElementById('filterFrom').value;
    const to = document.getElementById('filterTo').value;

    // Deleted toggle from localStorage
    const showingDeleted = localStorage.getItem('showingDeleted') === 'true';

    // Build query parameters
    let query = `?page=${currentPage}&limit=${pageSize}`;
    if (status) query += `&status=${status}`;
    if (priority) query += `&priority=${priority}`;
    if (keyword) query += `&keyword=${keyword}`;
    if (from && to) query += `&from=${from}&to=${to}`;

    // Add deleted flags
    if (showingDeleted) {
        query += '&showDeleted=true&hideDeleted=false';
    } else {
        query += '&showDeleted=false&hideDeleted=true';
    }

    try {
        const res = await fetch(apiUrl + query);
        if (!res.ok) throw new Error('Failed to fetch tasks');

        const data = await res.json();
        renderTable(data?.tasks?.items);
        renderPagination(data?.tasks?._meta);
        allTags = data?.allTags || [];
    } catch (err) {
        console.error(err);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Failed to load data</td></tr>`;
    } finally {
        if (loadingRow) loadingRow.style.display = 'none';
    }
}



function renderTable(tasks) {
    const tbody = document.getElementById('taskTableBody');
    tbody.innerHTML = '';

    if (!tasks || tasks.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center">No tasks available.</td>
            </tr>
        `;
        return;
    }

    tasks.forEach(task => {
        tbody.innerHTML += `
            <tr>
                <td>${task.title}</td>
                <td>${task.status}</td>
                <td>${task.priority}</td>
                <td>${task.due_date || ''}</td>
               <td>
                ${task.tags && task.tags.length > 0
                ? task.tags.map(tag => tag.name).join(', ')
                : 'N/A'}
                </td>

                <td>
                    <button class="btn btn-sm btn-warning edit-task" data-id="${task.id}">Edit</button>
                    <button onclick="deleteTask(${task.id})" class="btn btn-sm btn-${task.is_deleted ? 'dark' : 'danger'}">
                        ${task.is_deleted ? 'Retrieve' : 'Delete'}
                    </button>
                    <button onclick="toggleStatus(${task.id})" class="btn btn-sm btn-info">Toggle Status</button>
                </td>
            </tr>
        `;
    });
}


$(document).ready(function () {
    const select = $('#taskTags');

    // Initialize Select2 once, inside the modal
    $('#addTaskModal, #updateTaskModal').on('shown.bs.modal', function () {

        select.empty();

        allTags.forEach(tag => {
            select.append(new Option(tag.name, tag.id, false, false));
        });

        select.select2({
            placeholder: 'Select tags',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#addTaskModal') // important for modals
        });
    });

});










async function deleteTask(id) {

    let isShowingDeleted = localStorage.getItem('showingDeleted') === 'true';

    if (isShowingDeleted) {

    }
    Swal.fire({
        title: 'Are you sure?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: `Yes, ${isShowingDeleted ? 'Retrieve' : 'Delete'} it!`
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const deleteUrl = isShowingDeleted ? `task/retrieve?id=${id}` : `${apiUrl}/${id}`;
                let response = await fetch(deleteUrl, {
                    method: `${isShowingDeleted ? 'PATCH' : 'DELETE'}`
                });

                if (response.ok) {
                    Swal.fire(
                        `${isShowingDeleted ? 'Retrieved' : 'Deleted'}!`,
                        `The task has been ${isShowingDeleted ? 'recovered' : 'deleted'}.`,
                        'success'
                    );
                    loadTasks();
                } else {
                    Swal.fire(
                        'Error!',
                        `Failed to ${isShowingDeleted ? 'retrieve' : 'delete'} the task.`,
                        'error'
                    );
                }
            } catch (e) {
                Swal.fire(
                    'Error!',
                    'Something went wrong.',
                    'error'
                );
            }
        }
    });
}

async function toggleStatus(id) {
    await fetch(`${apiUrl}/${id}/toggle-status`, {
        method: 'PATCH',
        headers: {
            'Authorization': 'Bearer demo-token'
        }
    });
    loadTasks();
}

function renderPagination(meta) {
    const ul = document.getElementById('pagination');
    ul.innerHTML = '';
    for (let i = 0; i < meta.pageCount; i++) {
        ul.innerHTML += `<li class="page-item ${i === meta.currentPage ? 'active' : ''}">
            <a class="page-link" href="#" onclick="currentPage=${i}; loadTasks(); return false;">${i + 1}</a>
        </li>`;
    }
}

// Filter button
document.getElementById('filterBtn').addEventListener('click', () => {
    currentPage = 0;
    loadTasks();
});


//  Create Tasks
document.getElementById('addTaskForm').addEventListener('submit', async function (e) {
    e.preventDefault(); // prevent normal form submit

    const newTask = {
        title: document.getElementById('taskTitle').value,
        status: document.getElementById('taskStatus').value,
        priority: document.getElementById('taskPriority').value,
        tags: $('#taskTags').val() || [],
        due_date: document.getElementById('taskDueDate').value
    };

    try {
        const res = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(newTask)
        });

        if (!res.ok) throw new Error('Failed to add task');

        clearFields();


        // Reset the form
        document.getElementById('addTaskForm').reset();

        const addModalEl = document.getElementById('addTaskModal');
        const addModal = bootstrap.Modal.getInstance(addModalEl) || new bootstrap.Modal(addModalEl);
        addModal.hide();
        // Reload table without page reload
        currentPage = 0; // optional: reset to first page
        loadTasks();
    } catch (err) {
        alert('Error adding task: ' + err.message);
        console.error(err);
    }
});

// Edit Task
document.addEventListener('click', async function (e) {
    if (e.target.classList.contains('edit-task')) {
        const taskId = e.target.dataset.id;

        try {
            const res = await fetch(`${apiUrl}/${taskId}`);
            const task = await res.json();

            // Prefill text inputs
            document.getElementById('updateTaskId').value = task.id;
            document.getElementById('updateTaskTitle').value = task.title;
            document.getElementById('updateTaskStatus').value = task.status;
            document.getElementById('updateTaskPriority').value = task.priority;
            document.getElementById('updateTaskDueDate').value = task.due_date || '';

            // Select2 tags
            const select = $('#updateTaskTags');
            select.empty();

            const taskTags = task.tags || []; // fallback to empty array if undefined

            allTags.forEach(tag => {
                const isSelected = taskTags.some(t => t.id == tag.id); // safe now
                const option = new Option(tag.name, tag.id, isSelected, isSelected);
                select.append(option);
            });

            // Initialize or refresh Select2
            if (select.hasClass('select2-hidden-accessible')) {
                select.trigger('change.select2'); // refresh selected values
            } else {
                select.select2({
                    placeholder: 'Select tags',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#updateTaskModal')
                });
            }


            // Show modal
            const updateModalEl = document.getElementById('updateTaskModal');
            const updateModal = bootstrap.Modal.getOrCreateInstance(updateModalEl);
            updateModal.show();

        } catch (err) {
            alert('Error fetching task details');
            console.error(err);
        }
    }
});



// Update Task
document.getElementById('updateTaskForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const taskId = document.getElementById('updateTaskId').value;

    // Get selected tags using jQuery Select2
    const tags = $('#updateTaskTags').val() || []; // returns array of selected values

    const updatedTask = {
        title: document.getElementById('updateTaskTitle').value,
        status: document.getElementById('updateTaskStatus').value,
        priority: document.getElementById('updateTaskPriority').value,
        tags: tags,
        due_date: document.getElementById('updateTaskDueDate').value
    };

    try {
        const res = await fetch(`${apiUrl}/${taskId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(updatedTask)
        });

        const data = await res.json();

        if (!res.ok) {
            showAlert('error', data.message || 'Failed to save task');
            return;
        }

        // Hide modal
        const updateModalEl = document.getElementById('updateTaskModal');
        const updateModal = bootstrap.Modal.getInstance(updateModalEl);
        updateModal.hide();

        // Refresh table
        loadTasks();
        showAlert('success', 'Task updated successfully!');
    } catch (err) {
        alert('Error updating task');
        console.error(err);
    }
});



document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('showDeleted');

    let isShowingDeleted = localStorage.getItem('showingDeleted') === 'true';

    applyToggleState(isShowingDeleted);

    btn.addEventListener('click', function () {
        isShowingDeleted = !isShowingDeleted;

        localStorage.setItem('showingDeleted', isShowingDeleted);

        applyToggleState(isShowingDeleted);
        loadTasks();
    });

    function applyToggleState(state) {
        if (state) {
            btn.textContent = 'Show Active';
            btn.dataset.showingDeleted = 'true';
        } else {
            btn.textContent = 'Show Deleted';
            btn.dataset.showingDeleted = 'false';
        }

        currentPage = 0;
    }
});



function clearFields() {
    document.getElementById('filterKeyword').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterPriority').value = '';
    document.getElementById('filterFrom').value = '';
    document.getElementById('filterTo').value = '';
}