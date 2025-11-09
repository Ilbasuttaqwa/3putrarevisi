@extends('layouts.app')

@section('title', 'Edit Absensi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-pencil"></i> Edit Absensi</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h5 class="card-title mb-0">Form Edit Absensi</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route(auth()->user()->isManager() ? 'manager.absensis.update' : 'admin.absensis.update', $absensi) }}" id="editAbsensiForm">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="employee_id" class="form-label">Karyawan <span class="text-danger">*</span></label>
                    <select class="form-control @error('employee_id') is-invalid @enderror"
                            id="employee_id" name="employee_id" required>
                        <option value="">Pilih Karyawan</option>
                        @php
                            $groupedEmployees = $allEmployees->groupBy('jabatan');
                        @endphp

                        @if(isset($groupedEmployees['karyawan_gudang']))
                            <optgroup label="Karyawan Gudang">
                                @foreach($groupedEmployees['karyawan_gudang'] as $employee)
                                    @php
                                        $currentValue = 'employee_' . $absensi->employee_id;
                                        $isSelected = old('employee_id', $currentValue) == $employee->id;
                                    @endphp
                                    <option value="{{ $employee->id }}"
                                            data-jabatan="{{ $employee->jabatan }}"
                                            data-gaji="{{ $employee->gaji_pokok }}"
                                            data-source="{{ $employee->source ?? 'unknown' }}"
                                            {{ $isSelected ? 'selected' : '' }}>
                                        {{ $employee->nama }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if(isset($groupedEmployees['karyawan']))
                            <optgroup label="Karyawan Kandang">
                                @foreach($groupedEmployees['karyawan'] as $employee)
                                    @php
                                        $currentValue = 'employee_' . $absensi->employee_id;
                                        $isSelected = old('employee_id', $currentValue) == $employee->id;
                                    @endphp
                                    <option value="{{ $employee->id }}"
                                            data-jabatan="{{ $employee->jabatan }}"
                                            data-gaji="{{ $employee->gaji_pokok }}"
                                            data-source="{{ $employee->source ?? 'unknown' }}"
                                            {{ $isSelected ? 'selected' : '' }}>
                                        {{ $employee->nama }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if(isset($groupedEmployees['mandor']))
                            <optgroup label="Mandor">
                                @foreach($groupedEmployees['mandor'] as $employee)
                                    @php
                                        $currentValue = 'employee_' . $absensi->employee_id;
                                        $isSelected = old('employee_id', $currentValue) == $employee->id;
                                    @endphp
                                    <option value="{{ $employee->id }}"
                                            data-jabatan="{{ $employee->jabatan }}"
                                            data-gaji="{{ $employee->gaji_pokok }}"
                                            data-source="{{ $employee->source ?? 'unknown' }}"
                                            {{ $isSelected ? 'selected' : '' }}>
                                        {{ $employee->nama }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                    @error('employee_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                           id="tanggal" name="tanggal" value="{{ old('tanggal', $absensi->tanggal->format('Y-m-d')) }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="status_full" value="full" {{ old('status', $absensi->status) == 'full' ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_full">Full Day</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="status_half" value="setengah_hari" {{ old('status', $absensi->status) == 'setengah_hari' ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_half">½ Hari</label>
                    </div>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="gaji_pokok_display" class="form-label">Gaji Pokok (Saat Ini)</label>
                    <input type="text" class="form-control" id="gaji_pokok_display" readonly value="{{ 'Rp ' . number_format($absensi->gaji_pokok_saat_itu ?? 0, 0, ',', '.') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="gaji_hari_itu_display" class="form-label">Gaji Hari Itu (Saat Ini)</label>
                    <input type="text" class="form-control" id="gaji_hari_itu_display" readonly value="{{ 'Rp ' . number_format($absensi->gaji_hari_itu ?? 0, 0, ',', '.') }}">
                </div>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Catatan:</strong> Data gaji akan otomatis disesuaikan dengan data gaji karyawan terbaru saat Anda menyimpan perubahan.
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}"
                   class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Edit Absensi form script loaded with Select2');

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

    // Form submission - show confirmation
    const form = document.getElementById('editAbsensiForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Menyimpan...';
        });
    }
});
</script>
@endpush
