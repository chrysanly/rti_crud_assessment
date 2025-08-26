<?php

/** @var yii\web\View $this */
$this->title = 'Task Manager';
?>

<div class="site-task">

    <div class="container mt-4">
        <div class="card p-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Task Manager</h1>

                <div class="d-flex gap-2">
                    <button class="btn btn-secondary" id="showDeleted">Show Deleted</button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTaskModal">Add Task</button>
                </div>
            </div>

            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" id="filterKeyword" placeholder="Search title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select id="filterStatus" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filterPriority" class="form-control">
                        <option value="">All Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" id="filterFrom" class="form-control">
                </div>
                <div class="col-md-2">
                    <input type="date" id="filterTo" class="form-control">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary" id="filterBtn">Filter</button>
                </div>
            </div>
        </div>

        <div class="card mt-2 p-2">
            <!-- Task Table -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                        <th>Tags</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="taskTableBody">
                    <tr id="loadingRow" style="display:none;">
                        <td colspan="6" class="text-center">Loading...</td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav>
                <ul class="pagination" id="pagination"></ul>
            </nav>
        </div>




        <!-- Add Task Modal -->
        <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="addTaskForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="taskTitle" class="form-label">Title</label>
                                <input type="text" class="form-control" id="taskTitle" required>
                            </div>
                            <div class="mb-3">
                                <label for="taskStatus" class="form-label">Status</label>
                                <select class="form-control" id="taskStatus" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="taskPriority" class="form-label">Priority</label>
                                <select class="form-control" id="taskPriority" required>
                                    <option value="low">low</option>
                                    <option value="medium">medium</option>
                                    <option value="high">high</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="taskTags" class="form-label">Tags</label>
                                <select id="taskTags" multiple="multiple" style="width:100%"></select>
                            </div>

                            <div class="mb-3">
                                <label for="taskDueDate" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="taskDueDate">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Task Modal -->
        <div class="modal fade" id="updateTaskModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="updateTaskForm">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="updateTaskId">

                            <div class="mb-3">
                                <label>Title</label>
                                <input type="text" id="updateTaskTitle" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Status</label>
                                <select id="updateTaskStatus" class="form-control">
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Priority</label>
                                <select id="updateTaskPriority" class="form-control">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>

                             <div class="mb-3">
                                <label for="updateTaskTags" class="form-label">Tags</label>
                                <select id="updateTaskTags" multiple="multiple" style="width:100%"></select>
                            </div>

                            <div class="mb-3">
                                <label>Due Date</label>
                                <input type="date" id="updateTaskDueDate" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>

</div>


