@extends('layouts.app')

@section('title', 'Master Absensi & Kalender')

@push('styles')
<style>
/* Excel-like DataTables Styling */
.dataTables_wrapper {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: #333;
    font-size: 14px;
}

.dataTables_wrapper .dataTables_length select {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 4px 8px;
    background: white;
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 6px 12px;
    margin-left: 8px;
    background: white;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

/* Excel-like table styling */
#absensiTable {
    border-collapse: collapse;
    width: 100%;
    background: white;
    border: 1px solid #ddd;
}

#absensiTable thead th {
    background: #2c3e50 !important;
    border: 1px solid #ddd;
    padding: 12px 8px;
    font-weight: 600;
    color: #ffffff !important;
    text-align: center;
    position: sticky;
    top: 0;
    z-index: 10;
}

/* Pastikan semua elemen di dalam header th juga putih */
#absensiTable thead th,
#absensiTable thead th *,
#absensiTable thead th a,
#absensiTable thead th span {
    color: #ffffff !important;
}

/* Override DataTables sorting arrows color */
#absensiTable thead th.sorting:before,
#absensiTable thead th.sorting:after,
#absensiTable thead th.sorting_asc:before,
#absensiTable thead th.sorting_asc:after,
#absensiTable thead th.sorting_desc:before,
#absensiTable thead th.sorting_desc:after {
    color: #ffffff !important;
    opacity: 0.5;
}

#absensiTable tbody td {
    border: 1px solid #ddd;
    padding: 8px;
    vertical-align: middle;
    background: white;
}

#absensiTable tbody tr:hover {
    background-color: #f5f5f5;
}

#absensiTable tbody tr:nth-child(even) {
    background-color: #fafafa;
}

#absensiTable tbody tr:nth-child(even):hover {
    background-color: #f0f0f0;
}

/* Excel-like buttons */
.dt-buttons {
    margin-bottom: 20px;
}

.dt-buttons .btn {
    background: #007bff;
    border: 1px solid #007bff;
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 13px;
    margin-right: 5px;
    transition: all 0.2s;
}

.dt-buttons .btn:hover {
    background: #0056b3;
    border-color: #0056b3;
    color: white;
}

.dt-buttons .btn:active {
    background: #004085;
    border-color: #004085;
}

/* Status badges */
.badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: 500;
}

.badge.bg-success {
    background-color: #28a745 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #ffffff !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}

/* Action buttons */
.btn-group .btn {
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 4px;
    margin: 0 1px;
    color: #ffffff !important;
    font-weight: 500;
}

.btn-warning {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #212529 !important;
}

.btn-danger {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: #ffffff !important;
}

/* Ensure action buttons are visible */
.btn-group .btn:hover {
    opacity: 0.9;
}

/* Fix any text visibility issues in action column */
#absensiTable tbody td:last-child {
    background-color: #ffffff !important;
    color: #333 !important;
}

/* Responsive design */
@media (max-width: 768px) {
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 10px;
    }
    
    .dt-buttons {
        margin-bottom: 15px;
    }
    
    .dt-buttons .btn {
        font-size: 11px;
        padding: 4px 8px;
        margin-right: 3px;
    }
}

/* Excel-like pagination */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border: 1px solid #ddd;
    background: white;
    color: #333;
    padding: 6px 12px;
    margin: 0 2px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f8f9fa;
    border-color: #007bff;
    color: #007bff;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    background: #f8f9fa;
    border-color: #ddd;
    color: #6c757d;
    cursor: not-allowed;
}

/* Loading spinner */
.dataTables_processing {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #333;
    font-weight: 500;
}

/* Info display */
.dataTables_info {
    color: #6c757d;
    font-size: 13px;
    margin-top: 10px;
}

/* Override DataTables Bootstrap theme for better header visibility */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: #333;
    font-size: 14px;
}

/* Force header text to be white and visible */
#absensiTable thead th,
#absensiTable thead th * {
    color: #ffffff !important;
    background-color: #2c3e50 !important;
}

/* Override any Bootstrap DataTables theme */
table.dataTable thead th {
    background-color: #2c3e50 !important;
    color: #ffffff !important;
    border-color: #ddd !important;
}

table.dataTable thead th.sorting,
table.dataTable thead th.sorting_asc,
table.dataTable thead th.sorting_desc {
    background-color: #2c3e50 !important;
    color: #ffffff !important;
}

/* Ensure all header text is visible */
#absensiTable thead th {
    text-shadow: none !important;
    font-weight: 600 !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-table me-2"></i>
                        Transaksi Absensi
                    </h4>
                    <div class="d-flex gap-2">
                        <!-- Redirect to new Select2 form -->
                        <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.create' : 'admin.absensis.create') }}"
                           class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            Tambah Absensi
                        </a>
                        <button type="button" class="btn btn-danger" id="deleteSelectedBtn" onclick="deleteSelectedAbsensi()" disabled>
                            <i class="bi bi-trash me-1"></i>
                            Hapus
                        </button>
                    </div>
    </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="tanggal_filter" class="form-label">Filter Tanggal:</label>
                            <input type="date" id="tanggal_filter" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="bibit_filter" class="form-label">Cari Bibit/Pembibitan:</label>
                            <input type="text" id="bibit_filter" class="form-control" placeholder="Masukkan nama pembibitan...">
</div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button id="filterBtn" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <button id="resetBtn" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                    </button>
                </div>
            </div>
</div>

                    <div class="table-responsive">
                        <table id="absensiTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="3%">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                    </th>
                                    <th width="5%">No</th>
                                    <th width="10%">Tanggal</th>
                                    <th width="18%">Nama Karyawan</th>
                                    <th width="10%">Role</th>
                                    <th width="10%">Status</th>
                                    <th width="12%">Lokasi</th>
                                    <th width="12%">Pembibitan</th>
                                    <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                                <!-- Data akan dimuat via DataTables AJAX -->
                    </tbody>
                </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#absensiTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ auth()->user()->isAdmin() ? route('admin.absensis.index') : route('manager.absensis.index') }}",
            type: 'GET',
            cache: false, // Disable caching for real-time updates
            data: function(d) {
                // Force fresh data with timestamp
                d._t = new Date().getTime();
                d._token = $('meta[name="csrf-token"]').attr('content');
                d.tanggal_filter = $('#tanggal_filter').val();
                d.bibit_filter = $('#bibit_filter').val();
                d._t = new Date().getTime(); // Cache busting for real-time updates
            }
        },
        columns: [
            { 
                data: null,
                orderable: false, 
                searchable: false,
                width: '3%',
                title: '<input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">',
                render: function(data, type, row) {
                    return '<input type="checkbox" class="absensi-checkbox" value="' + row.id + '" onchange="updateDeleteButton()">';
                }
            },
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex', 
                orderable: false, 
                searchable: false,
                width: '5%',
                title: 'No'
            },
            {
                data: 'nama_karyawan',
                name: 'nama_karyawan',
                width: '18%',
                title: 'Nama Karyawan',
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>';
                }
            },
            { 
                data: 'role_karyawan', 
                name: 'role_karyawan',
                width: '12%',
                title: 'Role',
                render: function(data, type, row) {
                    if (data === 'karyawan') {
                        return 'karyawan kandang';
                    } else if (data === 'karyawan_gudang') {
                        return 'karyawan gudang';
                    }
                    return data;
                }
            },
            { 
                data: 'tanggal_formatted', 
                name: 'tanggal',
                width: '10%',
                title: 'Tanggal'
            },
            { 
                data: 'status_badge', 
                name: 'status',
                orderable: false,
                searchable: false,
                width: '10%',
                title: 'Status'
            },
            { 
                data: 'lokasi_kerja', 
                name: 'lokasi_kerja',
                width: '10%',
                title: 'Lokasi'
            },
            { 
                data: 'pembibitan_info', 
                name: 'pembibitan_info',
                orderable: false,
                searchable: false,
                width: '10%',
                title: 'Pembibitan'
            },
            { 
                data: 'action', 
                name: 'action',
                orderable: false,
                searchable: false,
                width: '13%',
                title: 'Aksi'
            }
        ],
        order: [[4, 'desc']], // Sort by tanggal descending
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            processing: "Memproses data...",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            search: "Cari:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        dom: 'rtip',
        responsive: true,
        scrollX: true,
        autoWidth: false,
        columnDefs: [
            {
                targets: [0, 4, 5, 7], // No, Tanggal, Status, Aksi
                className: 'text-center'
            },
            {
                targets: [3], // Gaji
                className: 'text-end'
            }
        ]
    });
    
    // Filter functionality
    $('#filterBtn').on('click', function() {
        $('#absensiTable').DataTable().ajax.reload();
    });
    
    // Reset functionality
    $('#resetBtn').on('click', function() {
        // Clear all filter inputs
        $('#tanggal_filter').val('');
        $('#bibit_filter').val('');
        $('#absensiTable').DataTable().ajax.reload();
    });
    
    // Real-time refresh master data
    function refreshMasterData() {
        fetch('{{ route(auth()->user()->isManager() ? "manager.absensis.refresh-master-data" : "admin.absensis.refresh-master-data") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update filter dropdowns
                    updateFilterDropdowns(data.data);
                    console.log('✅ Master data refreshed');
                }
            })
            .catch(error => {
                console.error('Error refreshing master data:', error);
            });
    }
    
    // Update filter dropdowns with fresh data
    function updateFilterDropdowns(data) {
    }
    
    // Auto-refresh disabled for better performance
    // setInterval(refreshMasterData, 300000);
    // setInterval(updateAbsensiLokasi, 120000);
    // setInterval(refreshBulkAttendanceData, 180000);
    
    // Function to update absensi lokasi
    function updateAbsensiLokasi() {
        fetch('{{ route(auth()->user()->isManager() ? "manager.absensis.update-lokasi" : "admin.absensis.update-lokasi") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.updated_count > 0) {
                console.log('✅ Updated ' + data.updated_count + ' absensi records with correct lokasi');
                // Refresh table to show updated data
                $('#absensiTable').DataTable().ajax.reload(null, false);
            }
        })
        .catch(error => {
            console.error('Error updating absensi lokasi:', error);
        });
    }

    /* OLD BULK MODAL FUNCTIONS - DISABLED
    function refreshBulkAttendanceData() {
        // Function removed - using new Select2 form instead
    }
    */
});

// Toggle select all checkboxes in main table
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.absensi-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateDeleteButton();
}

// Update delete button state
function updateDeleteButton() {
    const checkboxes = document.querySelectorAll('.absensi-checkbox:checked');
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    
    if (checkboxes.length > 0) {
        deleteBtn.disabled = false;
    } else {
        deleteBtn.disabled = true;
    }
}

// Delete selected absensi records
function deleteSelectedAbsensi() {
    const checkboxes = document.querySelectorAll('.absensi-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (ids.length === 0) {
        alert('Mohon pilih absensi yang akan dihapus');
        return;
    }
    
    if (!confirm('Apakah Anda yakin ingin menghapus ' + ids.length + ' data absensi?')) {
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const baseUrl = window.location.origin;
    fetch(baseUrl + '/manager/absensis/bulk-delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Berhasil menghapus ' + data.deleted_count + ' data absensi');
            $('#absensiTable').DataTable().ajax.reload();
            document.getElementById('selectAllCheckbox').checked = false;
            updateDeleteButton();
        } else {
            alert('Error: ' + (data.message || 'Gagal menghapus data'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus data');
    });
}

// Auto-refresh disabled for better performance
</script>

<style>
/* Fix table header visibility */
#absensiTable thead th {
    background-color: #1e40af !important;
    color: white !important;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    padding: 12px 8px;
    border: none;
    font-size: 0.875rem;
}

/* Fix DataTables controls visibility */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: #333 !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    color: #333 !important;
}
</style>

<!-- JavaScript for bulk attendance - now included in app.js via Vite -->
@endpush