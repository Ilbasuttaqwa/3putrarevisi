@extends('layouts.app')

@section('title', 'Tambah Absensi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-plus-circle"></i> Tambah Absensi</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h5 class="card-title mb-0">Form Tambah Absensi</h5>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Ada masalah:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route(auth()->user()->isManager() ? 'manager.absensis.store' : 'admin.absensis.store') }}" id="absensiForm">
            @csrf

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="employee_id" class="form-label">Karyawan <span class="text-danger">*</span></label>
                    <select class="form-control @error('employee_id') is-invalid @enderror"
                            id="employee_id" name="employee_id" required>
                        <option value="">Pilih Karyawan</option>
                        @if(isset($allEmployees) && $allEmployees->count() > 0)
                            @php
                                $groupedEmployees = $allEmployees->groupBy('jabatan');
                            @endphp

                            @if(isset($groupedEmployees['karyawan_gudang']))
                                <optgroup label="Karyawan Gudang">
                                    @foreach($groupedEmployees['karyawan_gudang'] as $employee)
                                        <option value="{{ $employee->id }}"
                                                data-jabatan="{{ $employee->jabatan }}"
                                                data-gaji="{{ $employee->gaji_pokok }}"
                                                data-source="{{ $employee->source ?? 'unknown' }}"
                                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->nama }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif

                            @if(isset($groupedEmployees['karyawan']))
                                <optgroup label="Karyawan Kandang">
                                    @foreach($groupedEmployees['karyawan'] as $employee)
                                        <option value="{{ $employee->id }}"
                                                data-jabatan="{{ $employee->jabatan }}"
                                                data-gaji="{{ $employee->gaji_pokok }}"
                                                data-source="{{ $employee->source ?? 'unknown' }}"
                                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->nama }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif

                            @if(isset($groupedEmployees['mandor']))
                                <optgroup label="Mandor">
                                    @foreach($groupedEmployees['mandor'] as $employee)
                                        <option value="{{ $employee->id }}"
                                                data-jabatan="{{ $employee->jabatan }}"
                                                data-gaji="{{ $employee->gaji_pokok }}"
                                                data-source="{{ $employee->source ?? 'unknown' }}"
                                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->nama }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @else
                            <option value="" disabled>Tidak ada data karyawan</option>
                        @endif
                    </select>

                    @error('employee_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if(config('app.debug'))
                        <small class="text-muted">
                            Debug: Total karyawan: {{ isset($allEmployees) ? $allEmployees->count() : 0 }}
                        </small>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="pembibitan_id" class="form-label">Pembibitan</label>
                    <select class="form-control @error('pembibitan_id') is-invalid @enderror"
                            id="pembibitan_id" name="pembibitan_id">
                        <option value="">Pilih Pembibitan</option>
                        @foreach($pembibitans as $pembibitan)
                            <option value="{{ $pembibitan->id }}" {{ old('pembibitan_id') == $pembibitan->id ? 'selected' : '' }}>
                                {{ $pembibitan->judul }} - {{ $pembibitan->kandang->nama_kandang ?? 'Tidak ada kandang' }} ({{ $pembibitan->lokasi->nama_lokasi ?? 'Tidak ada lokasi' }})
                            </option>
                        @endforeach
                    </select>
                    @error('pembibitan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                           id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="status_full" value="full" {{ old('status') == 'full' ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_full">Full Day</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="status_half" value="setengah_hari" {{ old('status') == 'setengah_hari' ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_half">½ Hari</label>
                    </div>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="gaji_pokok_saat_itu_display" class="form-label">Gaji Pokok</label>
                    <input type="text" class="form-control" id="gaji_pokok_saat_itu_display" readonly>
                    <input type="hidden" id="gaji_pokok_saat_itu" name="gaji_pokok_saat_itu">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="gaji_hari_itu_display" class="form-label">Gaji Perhari</label>
                    <input type="text" class="form-control" id="gaji_hari_itu_display" readonly>
                    <input type="hidden" id="gaji_hari_itu" name="gaji_hari_itu">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}"
                   class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Absensi form script loaded with Select2');

    // Initialize Select2 with search functionality
    $('#employee_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Cari karyawan...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Tidak ada karyawan yang ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    });

    // Initialize Flatpickr for date input
    flatpickr("#tanggal", {
        dateFormat: "Y-m-d",
        defaultDate: new Date(),
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            }
        }
    });

    const employeeSelect = document.getElementById('employee_id');
    const gajiPokokDisplay = document.getElementById('gaji_pokok_saat_itu_display');
    const gajiPokokInput = document.getElementById('gaji_pokok_saat_itu');
    const gajiHariItuDisplay = document.getElementById('gaji_hari_itu_display');
    const gajiHariItuInput = document.getElementById('gaji_hari_itu');
    const statusRadios = document.querySelectorAll('input[name="status"]');

    // Trigger auto-fill on page load if employee is already selected
    if (employeeSelect && employeeSelect.value) {
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        if (selectedOption.value) {
            const gaji = parseFloat(selectedOption.getAttribute('data-gaji')) || 0;

            if (gajiPokokDisplay && gajiPokokInput) {
                gajiPokokDisplay.value = formatCurrency(gaji);
                gajiPokokInput.value = gaji;
            }

            calculateGajiHariItu();
        }
    }

    // Auto-fill gaji pokok when employee is selected
    $('#employee_id').on('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const gaji = parseFloat(selectedOption.getAttribute('data-gaji')) || 0;

            if (gajiPokokDisplay && gajiPokokInput) {
                gajiPokokDisplay.value = formatCurrency(gaji);
                gajiPokokInput.value = gaji;
            }

            calculateGajiHariItu();
        } else {
            if (gajiPokokDisplay && gajiPokokInput) {
                gajiPokokDisplay.value = '';
                gajiPokokInput.value = '';
            }
            if (gajiHariItuDisplay && gajiHariItuInput) {
                gajiHariItuDisplay.value = '';
                gajiHariItuInput.value = '';
            }
        }
    });

    // Calculate gaji hari when status changes
    statusRadios.forEach(radio => {
        radio.addEventListener('change', calculateGajiHariItu);
    });

    function calculateGajiHariItu() {
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        if (!selectedOption.value) {
            return;
        }

        const gajiPokok = parseFloat(selectedOption.getAttribute('data-gaji')) || 0;
        const selectedStatus = document.querySelector('input[name="status"]:checked');

        if (selectedStatus && gajiHariItuDisplay && gajiHariItuInput) {
            let gajiHariItu = 0;
            if (selectedStatus.value === 'full') {
                gajiHariItu = gajiPokok / 30;
            } else if (selectedStatus.value === 'setengah_hari') {
                gajiHariItu = (gajiPokok / 30) / 2;
            }

            gajiHariItuDisplay.value = formatCurrency(gajiHariItu);
            gajiHariItuInput.value = gajiHariItu.toFixed(2);
        }
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Form submission with AJAX
    const form = document.getElementById('absensiForm');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const employeeId = document.getElementById('employee_id').value;
            const tanggal = document.getElementById('tanggal').value;
            const status = document.querySelector('input[name="status"]:checked');
            const gajiPokok = document.getElementById('gaji_pokok_saat_itu').value;
            const gajiHariItu = document.getElementById('gaji_hari_itu').value;

            if (!employeeId || !tanggal || !status || !gajiPokok || !gajiHariItu) {
                showToast('error', 'Mohon lengkapi semua field yang diperlukan');
                return false;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Menyimpan...';

            try {
                const formData = new FormData(form);

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    showToast('success', 'Data absensi berhasil disimpan!');
                    setTimeout(() => {
                        window.location.href = result.redirect || '{{ route(auth()->user()->isManager() ? "manager.absensis.index" : "admin.absensis.index") }}';
                    }, 1000);
                } else {
                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                    if (result.message) {
                        errorMessage = result.message;
                    } else if (response.status === 409) {
                        errorMessage = 'Data absensi untuk karyawan ini pada tanggal tersebut sudah ada';
                    } else if (response.status === 422) {
                        errorMessage = 'Data yang dimasukkan tidak valid';
                    }

                    showToast('error', errorMessage);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }

            } catch (error) {
                console.error('Form submission error:', error);

                let errorMessage = 'Terjadi kesalahan: ' + error.message;
                if (error.message.includes('409')) {
                    errorMessage = 'Data absensi untuk karyawan ini pada tanggal tersebut sudah ada';
                }

                showToast('error', errorMessage);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
});
</script>
@endpush
