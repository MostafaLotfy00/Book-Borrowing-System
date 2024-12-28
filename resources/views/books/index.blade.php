@extends('master')

@section('books', 'active')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <a href="{{ route('books.export.pdf') }}" class="btn btn-primary mb-3">Export as PDF</a>
        @can('manage_books')
        <a href="#" class="btn btn-success mb-3" id="createBookButton">Create Book</a> <!-- Create Book Button -->
        @endcan
        <h5 class="card-header">Books</h5>
        <div class="table-responsive text-nowrap">
            <table id="books-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Book Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Create Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createForm">
                    <div class="mb-3">
                        <label for="newTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="newTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="newAuthor" class="form-label">Author</label>
                        <input type="text" class="form-control" id="newAuthor" required>
                    </div>
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">Status</label>
                        <select class="form-control" id="newStatus" required>
                            <option value="available">Available</option>
                            <option value="booked">Booked</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="createButton" class="btn btn-primary">Create Book</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="bookId">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" class="form-control" id="author" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" required>
                            <option value="available">Available</option>
                            <option value="booked">Booked</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveButton" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Reserve Modal -->
<div class="modal fade" id="reserveModal" tabindex="-1" aria-labelledby="reserveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reserveModalLabel">Reserve Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reserveForm">
                    <input type="hidden" id="reserveBookId">
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="reserveButton" class="btn btn-primary">Reserve</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Setup CSRF Token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize DataTable
        var table = $('#books-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('books.table') }}",
            columns: [
                { data: 'title', name: 'title' },
                { data: 'author', name: 'author' },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data) {
                        return data.charAt(0).toUpperCase() + data.slice(1); // Capitalize the first letter
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        var editButton = '';
                        var reserveButton = '';
                        
                        // Check if user has permission to edit
                        @can('manage_books')
                            editButton = `
                                <button class="edit-btn btn btn-warning btn-sm" 
                                        data-id="${row.id}" 
                                        data-title="${row.title}" 
                                        data-author="${row.author}" 
                                        data-status="${row.status}">
                                        Edit</button>
                            `;
                        @endcan

                        // Check if book is available
                        if (row.status === 'available') {
                            reserveButton = `
                                <button class="reserve-btn btn btn-primary btn-sm" 
                                        data-id="${row.id}" 
                                        data-title="${row.title}">
                                        Reserve</button>
                            `;
                        }

                        return editButton + reserveButton;
                    }
                }
            ]
        });

        // Open Create Book Modal
        $('#createBookButton').on('click', function() {
            $('#createModal').modal('show');
        });

        // Create new book
        $('#createButton').on('click', function() {
            var newBookData = {
                title: $('#newTitle').val(),
                author: $('#newAuthor').val(),
                status: $('#newStatus').val()
            };

            $.ajax({
                url: '/books',
                type: 'POST',
                data: newBookData,
                success: function(response) {
                    $('#createModal').modal('hide');
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    alert('Error creating book: ' + xhr.responseJSON.message);
                }
            });
        });

        // Open Edit Modal
        $('#books-table').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var title = $(this).data('title');
            var author = $(this).data('author');
            var status = $(this).data('status');

            $('#bookId').val(id);
            $('#title').val(title);
            $('#author').val(author);
            $('#status').val(status.toLowerCase());
            $('#editModal').modal('show');
        });

        // Save changes
        $('#saveButton').on('click', function() {
            var id = $('#bookId').val();
            var updatedData = {
                title: $('#title').val(),
                author: $('#author').val(),
                status: $('#status').val()
            };

            $.ajax({
                url: `/books/${id}`,
                type: 'PUT',
                data: updatedData,
                success: function(response) {
                    $('#editModal').modal('hide');
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    alert('Error updating book: ' + xhr.responseJSON.message);
                }
            });
        });

        // Open Reserve Modal
        $('#books-table').on('click', '.reserve-btn', function() {
            var id = $(this).data('id');
            var title = $(this).data('title');

            $('#reserveBookId').val(id);
            $('#reserveModalLabel').text(`Reserve Book: ${title}`);
            $('#reserveModal').modal('show');
        });

        // Reserve book
        $('#reserveButton').on('click', function() {
            var id = $('#reserveBookId').val();
            var reservationData = {
                start_date: $('#startDate').val(),
                end_date: $('#endDate').val()
            };

            $.ajax({
                url: `/books/${id}/reserve`,
                type: 'POST',
                data: reservationData,
                success: function(response) {
                    $('#reserveModal').modal('hide');
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    alert('Error reserving book: ' + xhr.responseJSON.message);
                }
            });
        });
    });
</script>
@endpush

<meta name="csrf-token" content="{{ csrf_token() }}">
