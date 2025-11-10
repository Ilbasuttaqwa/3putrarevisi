@extends('layouts.app')

@section('title', 'Tambah Absensi')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css">

<style>
    :root {
        --primary-color: #16a34a;
        --primary-hover: #15803d;
        --bg-light: #f1f5f9;
        --text-dark: #1e293b;
        --border-color: #e2e8f0;
    }

    body {
        background-color: var(--bg-light);
    }

    .form-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #15803d 100%);
        color: white;
        padding: 24px 32px;
        border-bottom: 4px solid #15803d;
    }

    .form-body {
        padding: 32px;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-control:focus,
    .select2-container--bootstrap-5 .select2-selection:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(22, 163, 74, 0.25);
    }

    .btn-primary {
        background: var(--primary-color);
        border-color: var(--primary-color);
        padding: 12px 24px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
    }

    .employee-table {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .employee-table thead {
        background: var(--primary-color);
        color: white;
    }

    .employee-table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        padding: 16px 12px;
        border: none;
    }

    .employee-table td {
        padding: 12px;
        vertical-align: middle;
    }

    .employee-table tbody tr:hover {
        background-color: rgba(22, 163, 74, 0.05);
    }

    .role-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .role-gudang { background: #dbeafe; color: #1e40af; }
    .role-kandang { background: #fce7f3; color: #be185d; }
    .role-mandor { background: #fef3c7; color: #92400e; }
    .role-pembibitan { background: #d1fae5; color: #065f46; }

    .summary-card {
        border-radius: 12px;
        padding: 20px;
        border-left: 4px solid;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .summary-primary {
        background: #ecfdf5;
        border-left-color: #10b981;
    }

    .summary-secondary {
        background: #eff6ff;
        border-left-color: #3b82f6;
    }

    .summary-warning {
        background: #fef3c7;
        border-left-color: #f59e0b;
    }

    .role-divider {
        background: var(--bg-light);
        font-weight: 700;
        color: var(--primary-color);
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 0.5px;
    }

    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px;
        border: 2px solid var(--border-color);
        min-height: 45px;
    }

    .flatpickr-input {
        border-radius: 8px;
        border: 2px solid var(--border-color);
        padding: 12px 16px;
    }

    .status-select {
        border-radius: 8px;
        border: 2px solid var(--border-color);
        padding: 8px 12px;
        font-weight: 500;
    }

    .status-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(22, 163, 74, 0.25);
    }

    .gaji-amount {
        font-weight: 700;
        font-size: 15px;
    }

    .gaji-full { color: #059669; }
    .gaji-half { color: #d97706; }
    .gaji-off { color: #dc2626; }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: #94a3b8;
    }

    .empty-state svg {
        width: 80px;
        height: 80px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .form-header {
            padding: 20px;
        }
        .form-body {
            padding: 20px;
        }
        .employee-table {
            font-size: 13px;
        }
        .employee-table th,
        .employee-table td {
            padding: 8px 6px;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-11">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <!-- Main Form Card -->
            <div class="form-card">
                <!-- Header -->
                <div class="form-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="mb-1">
                                <i class="bi bi-clipboard-check me-2"></i>
                                Tambah Absensi Karyawan
                            </h3>
                            <p class="mb-0 opacity-90" style="font-size: 14px;">
                                Catat kehadiran karyawan dengan cepat dan akurat
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-white text-primary" style="font-size: 13px; padding: 8px 12px;">
                                <i class="bi bi-people-fill me-1"></i>
                                <span id="totalEmployees">0</span> Karyawan
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Body -->
                <div class="form-body">
                    <form id="absensiForm" method="POST" action="{{ route(auth()->user()->isManager() ? 'manager.absensis.bulk-store' : 'admin.absensis.bulk-store') }}">
                        @csrf

                        <!-- Tanggal Absensi -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-calendar3 text-primary me-1"></i>
                                    Tanggal Absensi <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       id="tanggalAbsensi"
                                       name="tanggal"
                                       class="form-control flatpickr-input"
                                       placeholder="Pilih tanggal..."
                                       required
                                       readonly>
                                <small class="text-muted">Format: Hari, DD Bulan YYYY</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-funnel text-primary me-1"></i>
                                    Filter Berdasarkan Role
                                </label>
                                <select id="roleFilter" class="form-select" style="border-radius: 8px; padding: 12px;">
                                    <option value="all">Semua Role</option>
                                    <option value="karyawan_gudang">Karyawan Gudang</option>
                                    <option value="karyawan">Karyawan Kandang</option>
                                    <option value="mandor">Mandor</option>
                                </select>
                            </div>
                        </div>

                        <!-- Pilih Karyawan -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-search text-primary me-1"></i>
                                Cari dan Pilih Karyawan <span class="text-danger">*</span>
                            </label>
                            <select id="employeeSelect"
                                    class="form-select"
                                    multiple
                                    data-placeholder="Cari karyawan berdasarkan nama atau role...">
                                <!-- Options will be loaded via AJAX -->
                            </select>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Ketik nama karyawan atau pilih dari daftar. Tekan Ctrl untuk memilih beberapa karyawan sekaligus.
                            </small>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row g-3 mb-4" id="summaryCards" style="display: none;">
                            <div class="col-md-4">
                                <div class="summary-card summary-primary">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <small class="text-muted d-block mb-1">Karyawan Dipilih</small>
                                            <h4 class="mb-0" id="selectedCount">0</h4>
                                        </div>
                                        <i class="bi bi-people-fill text-success" style="font-size: 32px; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-card summary-secondary">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <small class="text-muted d-block mb-1">Total Gaji Pokok</small>
                                            <h5 class="mb-0" id="totalGajiPokok">Rp 0</h5>
                                        </div>
                                        <i class="bi bi-cash-stack text-primary" style="font-size: 32px; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-card summary-warning">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <small class="text-muted d-block mb-1">Gaji Hari Ini</small>
                                            <h5 class="mb-0" id="totalGajiHariIni">Rp 0</h5>
                                        </div>
                                        <i class="bi bi-wallet2 text-warning" style="font-size: 32px; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Employee Table -->
                        <div id="employeeTableContainer" style="display: none;">
                            <div class="table-responsive">
                                <table class="table employee-table mb-4">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th style="width: 20%;">Nama Karyawan</th>
                                            <th style="width: 10%;">Role</th>
                                            <th style="width: 12%;" class="text-end">Gaji Pokok</th>
                                            <th style="width: 15%;">Status Kehadiran</th>
                                            <th style="width: 18%;">Pembibitan</th>
                                            <th style="width: 12%;" class="text-end">Gaji Hari Ini</th>
                                            <th style="width: 8%;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employeeTableBody">
                                        <!-- Rows will be added here dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div id="emptyState" class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h5 class="mb-2">Belum Ada Karyawan Dipilih</h5>
                            <p class="mb-0">Gunakan kolom pencarian di atas untuk memilih karyawan</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center pt-4 border-top" id="actionButtons" style="display: none !important;">
                            <button type="button" class="btn btn-outline-danger" id="clearAllBtn">
                                <i class="bi bi-x-circle me-1"></i>
                                Hapus Semua
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                <i class="bi bi-save me-2"></i>
                                Simpan Absensi (<span id="submitCount">0</span> Karyawan)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
$(document).ready(function() {
    // Store selected employees data
    let selectedEmployees = [];
    let allEmployeesData = [];
    let allPembibitans = @json($pembibitans ?? []);

    console.log('📋 Pembibitans loaded:', allPembibitans.length);
    console.log('📋 Pembibitans data:', allPembibitans);

    if (allPembibitans.length === 0) {
        console.warn('⚠️ WARNING: No pembibitans data found!');
    }

    // Initialize Flatpickr
    flatpickr("#tanggalAbsensi", {
        locale: "id",
        dateFormat: "Y-m-d",
        defaultDate: new Date(),
        altInput: true,
        altFormat: "l, d F Y", // Format: Senin, 09 November 2025
        maxDate: new Date(),
        onChange: function(selectedDates, dateStr, instance) {
            console.log('Tanggal dipilih:', dateStr);
        }
    });

    // Initialize Select2
    $('#employeeSelect').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Cari karyawan berdasarkan nama atau role...',
        allowClear: true,
        closeOnSelect: false,
        ajax: {
            url: '{{ route(auth()->user()->isManager() ? "manager.absensis.get-employees" : "admin.absensis.get-employees") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    role: $('#roleFilter').val()
                };
            },
            processResults: function (data) {
                allEmployeesData = data.employees || [];

                // Group by role
                const grouped = {};
                data.employees.forEach(emp => {
                    const role = emp.jabatan || 'other';
                    if (!grouped[role]) {
                        grouped[role] = [];
                    }
                    grouped[role].push({
                        id: emp.id,
                        text: emp.nama + ' - ' + formatJabatan(emp.jabatan),
                        data: emp
                    });
                });

                // Convert to Select2 format with groups
                const results = [];
                const roleLabels = {
                    'karyawan_gudang': '🏪 Karyawan Gudang',
                    'karyawan': '🏠 Karyawan Kandang',
                    'mandor': '👷 Mandor'
                };

                Object.keys(grouped).forEach(role => {
                    results.push({
                        text: roleLabels[role] || role,
                        children: grouped[role]
                    });
                });

                return { results: results };
            },
            cache: true
        }
    });

    // Handle employee selection
    $('#employeeSelect').on('select2:select', function (e) {
        const employeeData = e.params.data.data;
        if (!selectedEmployees.find(emp => emp.id === employeeData.id)) {
            selectedEmployees.push({
                ...employeeData,
                status: 'full', // Default status
                pembibitan_id: null // Default no pembibitan
            });
            updateTable();
        }
    });

    // Handle employee removal from select
    $('#employeeSelect').on('select2:unselect', function (e) {
        const employeeId = e.params.data.id;
        selectedEmployees = selectedEmployees.filter(emp => emp.id !== employeeId);
        updateTable();
    });

    // Handle role filter change
    $('#roleFilter').on('change', function() {
        $('#employeeSelect').val(null).trigger('change');
        selectedEmployees = [];
        updateTable();
    });

    // Clear all button
    $('#clearAllBtn').on('click', function() {
        Swal.fire({
            title: 'Hapus Semua?',
            text: "Semua karyawan yang dipilih akan dihapus",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#employeeSelect').val(null).trigger('change');
                selectedEmployees = [];
                updateTable();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Semua karyawan dihapus',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    });

    // Update table when data changes
    function updateTable() {
        const tbody = $('#employeeTableBody');
        tbody.empty();

        if (selectedEmployees.length === 0) {
            $('#employeeTableContainer').hide();
            $('#summaryCards').hide();
            $('#actionButtons').hide();
            $('#emptyState').show();
            $('#submitBtn').prop('disabled', true);
            return;
        }

        $('#emptyState').hide();
        $('#employeeTableContainer').show();
        $('#summaryCards').show();
        $('#actionButtons').show();

        // Group by role for display
        const roleOrder = ['karyawan_gudang', 'karyawan', 'mandor'];
        const groupedEmployees = {};

        selectedEmployees.forEach(emp => {
            const role = emp.jabatan || 'other';
            if (!groupedEmployees[role]) {
                groupedEmployees[role] = [];
            }
            groupedEmployees[role].push(emp);
        });

        let rowNumber = 1;
        roleOrder.forEach(role => {
            if (groupedEmployees[role] && groupedEmployees[role].length > 0) {
                // Add role divider
                const dividerRow = `
                    <tr class="role-divider">
                        <td colspan="8" style="padding: 12px;">
                            <i class="bi bi-${getRoleIcon(role)} me-2"></i>
                            ${formatJabatan(role)} (${groupedEmployees[role].length})
                        </td>
                    </tr>
                `;
                tbody.append(dividerRow);

                // Add employees
                groupedEmployees[role].forEach(emp => {
                    const row = createEmployeeRow(emp, rowNumber);
                    tbody.append(row);
                    rowNumber++;
                });
            }
        });

        updateSummary();
        validateForm();
    }

    // Create employee table row
    function createEmployeeRow(emp, rowNumber) {
        const gajiPokok = emp.gaji_pokok || 0;
        const gajiHariIni = calculateGaji(gajiPokok, emp.status);

        // Generate pembibitan options
        let pembibitanOptions = '<option value="">Pilih Pembibitan (Opsional)</option>';
        console.log(`🔧 Creating row for ${emp.nama}, available pembibitans:`, allPembibitans.length);

        allPembibitans.forEach(pembibitan => {
            const lokasiInfo = pembibitan.lokasi ? pembibitan.lokasi.nama_lokasi : '-';
            const kandangInfo = pembibitan.kandang ? pembibitan.kandang.nama_kandang : '-';
            const selected = emp.pembibitan_id == pembibitan.id ? 'selected' : '';
            pembibitanOptions += `<option value="${pembibitan.id}" ${selected}>${pembibitan.judul} (${lokasiInfo} - ${kandangInfo})</option>`;
        });

        console.log(`✅ Pembibitan dropdown HTML length for ${emp.nama}:`, pembibitanOptions.length);

        return `
            <tr data-employee-id="${emp.id}">
                <td>${rowNumber}</td>
                <td>
                    <strong>${emp.nama}</strong>
                    ${emp.lokasi ? `<br><small class="text-muted">Lokasi: ${emp.lokasi.nama_lokasi || '-'}</small>` : ''}
                </td>
                <td>
                    <span class="role-badge role-${emp.jabatan}">
                        ${formatJabatan(emp.jabatan)}
                    </span>
                </td>
                <td class="text-end">
                    <strong>${formatRupiah(gajiPokok)}</strong>
                </td>
                <td>
                    <select class="form-select status-select" data-employee-id="${emp.id}" onchange="updateEmployeeStatus('${emp.id}', this.value)">
                        <option value="full" ${emp.status === 'full' ? 'selected' : ''}>✓ Full Day</option>
                        <option value="setengah_hari" ${emp.status === 'setengah_hari' ? 'selected' : ''}>⚡ Half Day</option>
                        <option value="off" ${emp.status === 'off' ? 'selected' : ''}>✗ Off</option>
                    </select>
                </td>
                <td>
                    <select class="form-select form-select-sm pembibitan-select" data-employee-id="${emp.id}" onchange="updateEmployeePembibitan('${emp.id}', this.value)" style="font-size: 13px;">
                        ${pembibitanOptions}
                    </select>
                </td>
                <td class="text-end">
                    <span class="gaji-amount gaji-${emp.status === 'full' ? 'full' : emp.status === 'setengah_hari' ? 'half' : 'off'}" id="gaji-${emp.id}">
                        ${formatRupiah(gajiHariIni)}
                    </span>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEmployee('${emp.id}')" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    // Calculate gaji based on status
    function calculateGaji(gajiPokok, status) {
        if (status === 'full') {
            return gajiPokok / 30;
        } else if (status === 'setengah_hari') {
            return (gajiPokok / 30) * 0.5;
        } else {
            return 0;
        }
    }

    // Update employee status
    window.updateEmployeeStatus = function(employeeId, newStatus) {
        const emp = selectedEmployees.find(e => e.id === employeeId);
        if (emp) {
            emp.status = newStatus;
            const gajiPokok = emp.gaji_pokok || 0;
            const gajiHariIni = calculateGaji(gajiPokok, newStatus);

            // Update gaji display
            const gajiElement = $(`#gaji-${employeeId}`);
            gajiElement.text(formatRupiah(gajiHariIni));
            gajiElement.removeClass('gaji-full gaji-half gaji-off');
            gajiElement.addClass(`gaji-${newStatus === 'full' ? 'full' : newStatus === 'setengah_hari' ? 'half' : 'off'}`);

            updateSummary();
        }
    };

    // Update employee pembibitan
    window.updateEmployeePembibitan = function(employeeId, pembibitanId) {
        const emp = selectedEmployees.find(e => e.id === employeeId);
        if (emp) {
            emp.pembibitan_id = pembibitanId || null;
            console.log(`📝 Pembibitan updated for ${emp.nama}:`, pembibitanId || 'None');
        }
    };

    // Remove employee
    window.removeEmployee = function(employeeId) {
        selectedEmployees = selectedEmployees.filter(emp => emp.id !== employeeId);

        // Also remove from select2
        const selectedValues = $('#employeeSelect').val() || [];
        const newValues = selectedValues.filter(val => val !== employeeId);
        $('#employeeSelect').val(newValues).trigger('change');

        updateTable();
    };

    // Update summary cards
    function updateSummary() {
        const totalSelected = selectedEmployees.length;
        const totalGajiPokok = selectedEmployees.reduce((sum, emp) => sum + (emp.gaji_pokok || 0), 0);
        const totalGajiHariIni = selectedEmployees.reduce((sum, emp) => {
            return sum + calculateGaji(emp.gaji_pokok || 0, emp.status);
        }, 0);

        $('#selectedCount').text(totalSelected);
        $('#totalEmployees').text(totalSelected);
        $('#submitCount').text(totalSelected);
        $('#totalGajiPokok').text(formatRupiah(totalGajiPokok));
        $('#totalGajiHariIni').text(formatRupiah(totalGajiHariIni));
    }

    // Validate form
    function validateForm() {
        const isValid = selectedEmployees.length > 0 && $('#tanggalAbsensi').val();
        $('#submitBtn').prop('disabled', !isValid);
    }

    // Format currency
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

    // Format jabatan
    function formatJabatan(jabatan) {
        const labels = {
            'karyawan_gudang': 'Karyawan Gudang',
            'karyawan': 'Karyawan Kandang',
            'mandor': 'Mandor'
        };
        return labels[jabatan] || jabatan;
    }

    // Get role icon
    function getRoleIcon(role) {
        const icons = {
            'karyawan_gudang': 'building',
            'karyawan': 'house-door',
            'mandor': 'person-badge'
        };
        return icons[role] || 'person';
    }

    // Form submission
    $('#absensiForm').on('submit', function(e) {
        e.preventDefault();

        if (selectedEmployees.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal 1 karyawan untuk melanjutkan',
                confirmButtonColor: '#16a34a'
            });
            return;
        }

        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

        const formData = {
            tanggal: $('#tanggalAbsensi').val(),
            employees: selectedEmployees.map(emp => ({
                id: emp.id,
                status: emp.status,
                pembibitan_id: emp.pembibitan_id || null
            }))
        };

        console.log('📤 Submitting absensi data:', formData);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('✅ Server response:', response);

                if (response.success && response.success_count > 0) {
                    // Success: At least 1 record saved
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || `Berhasil menyimpan ${response.success_count} absensi karyawan`,
                        confirmButtonColor: '#16a34a',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ route(auth()->user()->isManager() ? "manager.absensis.index" : "admin.absensis.index") }}';
                    });
                } else if (response.success_count > 0 && response.error_count > 0) {
                    // Partial success: Some saved, some errors
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sebagian Berhasil',
                        html: `
                            <p><strong>Berhasil:</strong> ${response.success_count} absensi</p>
                            <p><strong>Gagal:</strong> ${response.error_count} absensi</p>
                            <hr>
                            <small>${response.errors ? response.errors.join('<br>') : ''}</small>
                        `,
                        confirmButtonColor: '#16a34a',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '{{ route(auth()->user()->isManager() ? "manager.absensis.index" : "admin.absensis.index") }}';
                    });
                } else {
                    // All failed
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: `
                            <p>${response.message || 'Gagal menyimpan data absensi'}</p>
                            ${response.errors && response.errors.length > 0 ?
                                '<hr><small style="color: #dc2626;">' + response.errors.join('<br>') + '</small>' :
                                ''
                            }
                        `,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'OK'
                    });
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data',
                    confirmButtonColor: '#dc2626'
                });
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    console.log('✅ Absensi form initialized successfully');
});
</script>
@endpush
