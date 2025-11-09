@extends('layouts.app')

@section('title', 'Tambah Absensi')

@push('styles')
<!-- Force reload CSS -->
<link rel="stylesheet" href="https://cdn.tailwindcss.com?v={{ time() }}">
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js?v={{ time() }}" defer></script>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}"
                       class="inline-flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors mb-2">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Kembali
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">✨ Tambah Absensi (NEW)</h1>
                    <p class="text-gray-600 mt-1">Pilih tanggal dan catat kehadiran karyawan</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Hari Ini</div>
                    <div class="text-lg font-semibold text-gray-900" x-data x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })"></div>
                </div>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden"
             x-data="absensiForm()"
             x-init="init()">

            <!-- Card Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg p-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Form Absensi Karyawan</h2>
                            <p class="text-blue-100 text-sm">Catat kehadiran harian dengan cepat dan akurat</p>
                        </div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                        <div class="text-xs text-blue-100">Total Karyawan</div>
                        <div class="text-2xl font-bold text-white" x-text="employees.length"></div>
                    </div>
                </div>
            </div>

            <!-- Form Body -->
            <div class="p-8 space-y-6">
                <!-- Tanggal Input -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        📅 Tanggal Absensi
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           x-model="tanggal"
                           :max="new Date().toISOString().split('T')[0]"
                           required
                           class="w-full md:w-96 px-4 py-3 text-gray-900 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all font-medium"
                           :class="errors.tanggal ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20' : ''">
                    <p x-show="errors.tanggal" x-text="errors.tanggal" class="text-red-600 text-sm mt-1.5 font-medium" x-cloak></p>
                </div>

                <!-- Search Bar -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        🔍 Cari Karyawan
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               x-model="searchQuery"
                               @input="filterEmployees()"
                               placeholder="Ketik nama karyawan untuk mencari..."
                               class="w-full pl-12 pr-12 py-3.5 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all text-gray-900 placeholder-gray-400">
                        <div x-show="searchQuery"
                             @click="clearSearch()"
                             class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer group">
                            <svg class="h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1.5" x-show="searchQuery" x-cloak>
                        Menampilkan <span class="font-semibold text-blue-600" x-text="filteredEmployees.length"></span> dari <span x-text="employees.length"></span> karyawan
                    </p>
                </div>

                <!-- Filter Buttons -->
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="text-sm font-semibold text-gray-700">Filter Cepat:</span>
                    <button @click="setFilter('all')"
                            :class="filterBy === 'all' ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all hover:scale-105">
                        Semua (<span x-text="employees.length"></span>)
                    </button>
                    <button @click="setFilter('karyawan_gudang')"
                            :class="filterBy === 'karyawan_gudang' ? 'bg-green-600 text-white shadow-lg shadow-green-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all hover:scale-105">
                        🏪 Karyawan Gudang (<span x-text="employees.filter(e => e.jabatan === 'karyawan_gudang').length"></span>)
                    </button>
                    <button @click="setFilter('karyawan')"
                            :class="filterBy === 'karyawan' ? 'bg-purple-600 text-white shadow-lg shadow-purple-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all hover:scale-105">
                        🏠 Karyawan Kandang (<span x-text="employees.filter(e => e.jabatan === 'karyawan').length"></span>)
                    </button>
                    <button @click="setFilter('mandor')"
                            :class="filterBy === 'mandor' ? 'bg-orange-600 text-white shadow-lg shadow-orange-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all hover:scale-105">
                        👷 Mandor (<span x-text="employees.filter(e => e.jabatan === 'mandor').length"></span>)
                    </button>
                </div>

                <!-- Table -->
                <div class="border-2 border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto" style="max-height: 500px;">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-4 text-left">
                                        <input type="checkbox"
                                               @change="toggleAll($event.target.checked)"
                                               :checked="selectedEmployees.length === filteredEmployees.length && filteredEmployees.length > 0"
                                               class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer">
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Karyawan</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kandang</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Gaji Pokok</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status Kehadiran</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Gaji Hari Ini</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="employee in filteredEmployees" :key="employee.id">
                                    <tr class="hover:bg-blue-50/50 transition-colors"
                                        :class="selectedEmployees.includes(employee.id) ? 'bg-blue-50' : ''">
                                        <td class="px-4 py-4">
                                            <input type="checkbox"
                                                   :checked="selectedEmployees.includes(employee.id)"
                                                   @change="toggleEmployee(employee.id, $event.target.checked)"
                                                   class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-semibold text-gray-900" x-text="employee.nama"></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full"
                                                  :class="{
                                                      'bg-green-100 text-green-800': employee.jabatan === 'karyawan_gudang',
                                                      'bg-purple-100 text-purple-800': employee.jabatan === 'karyawan',
                                                      'bg-orange-100 text-orange-800': employee.jabatan === 'mandor'
                                                  }"
                                                  x-text="formatJabatan(employee.jabatan)">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <span x-text="employee.lokasi?.nama_lokasi || '-'"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <span x-text="employee.kandang?.nama_kandang || '-'"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-sm font-bold text-gray-900" x-text="formatRupiah(employee.gaji_pokok)"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select x-model="employeeStatus[employee.id]"
                                                    @change="calculateGaji(employee.id)"
                                                    :disabled="!selectedEmployees.includes(employee.id)"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium"
                                                    :class="!selectedEmployees.includes(employee.id) ? 'bg-gray-100 cursor-not-allowed opacity-60' : 'bg-white'">
                                                <option value="">Pilih Status</option>
                                                <option value="full">✓ Full Day</option>
                                                <option value="setengah_hari">⚡ Half Day</option>
                                                <option value="off">✗ Off</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-sm font-bold transition-colors"
                                                  :class="{
                                                      'text-green-600': employeeStatus[employee.id] === 'full',
                                                      'text-yellow-600': employeeStatus[employee.id] === 'setengah_hari',
                                                      'text-red-600': employeeStatus[employee.id] === 'off',
                                                      'text-gray-400': !employeeStatus[employee.id]
                                                  }"
                                                  x-text="formatRupiah(employeeGaji[employee.id] || 0)">
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredEmployees.length === 0">
                                    <td colspan="8" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-500">Tidak ada karyawan ditemukan</p>
                                            <p class="text-sm text-gray-400 mt-1">Coba ubah filter atau kata kunci pencarian</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-1">Karyawan Dipilih</div>
                                <div class="text-3xl font-bold text-blue-900" x-text="selectedEmployees.length"></div>
                            </div>
                            <div class="bg-blue-500 rounded-full p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs font-semibold text-green-600 uppercase tracking-wide mb-1">Total Gaji Pokok</div>
                                <div class="text-2xl font-bold text-green-900" x-text="formatRupiah(totalGajiPokok())"></div>
                            </div>
                            <div class="bg-green-500 rounded-full p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs font-semibold text-purple-600 uppercase tracking-wide mb-1">Total Gaji Hari Ini</div>
                                <div class="text-2xl font-bold text-purple-900" x-text="formatRupiah(totalGajiHariIni())"></div>
                            </div>
                            <div class="bg-purple-500 rounded-full p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}"
                       class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Batal
                    </a>

                    <button @click="submitForm()"
                            :disabled="!canSubmit()"
                            :class="canSubmit() ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-lg shadow-blue-500/50 hover:shadow-xl hover:shadow-blue-500/50 hover:scale-105' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                            class="inline-flex items-center px-8 py-3 font-bold rounded-xl transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!loading">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" x-show="loading" x-cloak>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="loading ? 'Menyimpan...' : '💾 Simpan Absensi'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js Script -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('absensiForm', () => ({
            // Data
            tanggal: new Date().toISOString().split('T')[0],
            employees: @json($allEmployees ?? []),
            filteredEmployees: [],
            selectedEmployees: [],
            employeeStatus: {},
            employeeGaji: {},
            searchQuery: '',
            filterBy: 'all',
            loading: false,
            errors: {},

            // Initialize
            init() {
                this.filteredEmployees = this.employees;
                console.log('✅ Form Tailwind Loaded -', this.employees.length, 'employees');
            },

            // Filter employees
            filterEmployees() {
                let filtered = this.employees;

                // Filter by type
                if (this.filterBy !== 'all') {
                    filtered = filtered.filter(e => e.jabatan === this.filterBy);
                }

                // Filter by search query
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(e =>
                        e.nama.toLowerCase().includes(query) ||
                        (e.lokasi?.nama_lokasi || '').toLowerCase().includes(query) ||
                        (e.kandang?.nama_kandang || '').toLowerCase().includes(query)
                    );
                }

                this.filteredEmployees = filtered;
            },

            // Set filter and apply
            setFilter(type) {
                this.filterBy = type;
                this.filterEmployees();
            },

            // Clear search
            clearSearch() {
                this.searchQuery = '';
                this.filterEmployees();
            },

            // Toggle single employee
            toggleEmployee(id, checked) {
                if (checked) {
                    if (!this.selectedEmployees.includes(id)) {
                        this.selectedEmployees.push(id);
                    }
                    // Set default status to full
                    if (!this.employeeStatus[id]) {
                        this.employeeStatus[id] = 'full';
                        this.calculateGaji(id);
                    }
                } else {
                    this.selectedEmployees = this.selectedEmployees.filter(empId => empId !== id);
                    delete this.employeeStatus[id];
                    delete this.employeeGaji[id];
                }
            },

            // Toggle all employees
            toggleAll(checked) {
                if (checked) {
                    this.filteredEmployees.forEach(emp => {
                        if (!this.selectedEmployees.includes(emp.id)) {
                            this.selectedEmployees.push(emp.id);
                            this.employeeStatus[emp.id] = 'full';
                            this.calculateGaji(emp.id);
                        }
                    });
                } else {
                    this.filteredEmployees.forEach(emp => {
                        this.selectedEmployees = this.selectedEmployees.filter(id => id !== emp.id);
                        delete this.employeeStatus[emp.id];
                        delete this.employeeGaji[emp.id];
                    });
                }
            },

            // Calculate gaji
            calculateGaji(employeeId) {
                const employee = this.employees.find(e => e.id === employeeId);
                if (!employee) return;

                const status = this.employeeStatus[employeeId];
                const gajiPokok = employee.gaji_pokok || 0;

                if (status === 'full') {
                    this.employeeGaji[employeeId] = gajiPokok / 30;
                } else if (status === 'setengah_hari') {
                    this.employeeGaji[employeeId] = gajiPokok / 60;
                } else if (status === 'off') {
                    this.employeeGaji[employeeId] = 0;
                } else {
                    this.employeeGaji[employeeId] = 0;
                }
            },

            // Format rupiah
            formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(amount || 0);
            },

            // Format jabatan
            formatJabatan(jabatan) {
                const mapping = {
                    'karyawan_gudang': 'Karyawan Gudang',
                    'karyawan': 'Karyawan Kandang',
                    'mandor': 'Mandor'
                };
                return mapping[jabatan] || jabatan;
            },

            // Calculate totals
            totalGajiPokok() {
                return this.selectedEmployees.reduce((total, id) => {
                    const emp = this.employees.find(e => e.id === id);
                    return total + (emp?.gaji_pokok || 0);
                }, 0);
            },

            totalGajiHariIni() {
                return this.selectedEmployees.reduce((total, id) => {
                    return total + (this.employeeGaji[id] || 0);
                }, 0);
            },

            // Validation
            canSubmit() {
                return this.tanggal &&
                       this.selectedEmployees.length > 0 &&
                       this.selectedEmployees.every(id => this.employeeStatus[id]) &&
                       !this.loading;
            },

            // Submit form
            async submitForm() {
                if (!this.canSubmit()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'Mohon lengkapi semua data sebelum menyimpan',
                        confirmButtonColor: '#3B82F6'
                    });
                    return;
                }

                this.loading = true;
                this.errors = {};

                const employeesData = this.selectedEmployees.map(id => ({
                    id: id,
                    status: this.employeeStatus[id],
                    pembibitan_id: null
                }));

                try {
                    const response = await fetch('{{ route(auth()->user()->isManager() ? "manager.absensis.bulk-store" : "admin.absensis.bulk-store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            tanggal: this.tanggal,
                            employees: employeesData
                        })
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.message || `Berhasil menyimpan ${employeesData.length} absensi karyawan`,
                            confirmButtonColor: '#22C55E',
                            timer: 2000
                        });
                        window.location.href = '{{ route(auth()->user()->isManager() ? "manager.absensis.index" : "admin.absensis.index") }}';
                    } else {
                        throw new Error(result.message || 'Terjadi kesalahan');
                    }
                } catch (error) {
                    console.error('❌ Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: error.message || 'Terjadi kesalahan saat menyimpan data',
                        confirmButtonColor: '#EF4444'
                    });
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
