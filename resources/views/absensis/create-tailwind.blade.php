<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Absensi - Modern</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Fade in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.3s ease-out; }
    </style>
</head>
<body class="h-full">
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-50">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}"
                           class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali
                        </a>
                        <span class="text-gray-300">|</span>
                        <h1 class="text-2xl font-bold text-gray-900">Tambah Absensi</h1>
                    </div>
                    <div class="text-sm text-gray-500" x-data="{ now: new Date() }" x-init="setInterval(() => now = new Date(), 1000)">
                        <span x-text="now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
             x-data="absensiForm()"
             x-init="init()">

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden fade-in">
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Form Input Absensi
                    </h2>
                    <p class="text-blue-100 text-sm mt-1">Pilih tanggal dan tambahkan karyawan yang hadir</p>
                </div>

                <!-- Card Body -->
                <div class="p-8">
                    <!-- Date Input -->
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Absensi
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               x-model="tanggal"
                               :max="new Date().toISOString().split('T')[0]"
                               class="w-full md:w-96 px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all text-gray-900 font-medium"
                               :class="errors.tanggal ? 'border-red-500' : ''"
                               required>
                        <p x-show="errors.tanggal" x-text="errors.tanggal" class="text-red-500 text-sm mt-1" x-cloak></p>
                    </div>

                    <!-- Search Bar -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Cari Karyawan
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
                                   placeholder="Ketik nama karyawan, lokasi, atau kandang..."
                                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all">
                            <div x-show="searchQuery"
                                 @click="searchQuery = ''; filterEmployees()"
                                 class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer">
                                <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="mb-6 flex flex-wrap gap-2">
                        <button @click="filterBy = 'all'; filterEmployees()"
                                :class="filterBy === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                            Semua (<span x-text="employees.length"></span>)
                        </button>
                        <button @click="filterBy = 'karyawan_gudang'; filterEmployees()"
                                :class="filterBy === 'karyawan_gudang' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                            Karyawan Gudang (<span x-text="employees.filter(e => e.jabatan === 'karyawan_gudang').length"></span>)
                        </button>
                        <button @click="filterBy = 'karyawan'; filterEmployees()"
                                :class="filterBy === 'karyawan' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                            Karyawan Kandang (<span x-text="employees.filter(e => e.jabatan === 'karyawan').length"></span>)
                        </button>
                        <button @click="filterBy = 'mandor'; filterEmployees()"
                                :class="filterBy === 'mandor' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                            Mandor (<span x-text="employees.filter(e => e.jabatan === 'mandor').length"></span>)
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="border-2 border-gray-200 rounded-xl overflow-hidden">
                        <div class="overflow-x-auto custom-scrollbar" style="max-height: 600px;">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0 z-10">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                            <input type="checkbox"
                                                   @change="toggleAll($event.target.checked)"
                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Karyawan</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tipe</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Lokasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kandang</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Gaji Pokok</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Gaji Hari Ini</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(employee, index) in filteredEmployees" :key="employee.id">
                                        <tr class="hover:bg-blue-50 transition-colors"
                                            :class="selectedEmployees.includes(employee.id) ? 'bg-blue-50' : ''">
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <input type="checkbox"
                                                       :value="employee.id"
                                                       @change="toggleEmployee(employee.id, $event.target.checked)"
                                                       :checked="selectedEmployees.includes(employee.id)"
                                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-semibold text-gray-900" x-text="employee.nama"></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
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
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                                <span x-text="formatRupiah(employee.gaji_pokok)"></span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <select x-model="employeeStatus[employee.id]"
                                                        @change="calculateGaji(employee.id)"
                                                        :disabled="!selectedEmployees.includes(employee.id)"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm transition-all"
                                                        :class="!selectedEmployees.includes(employee.id) ? 'bg-gray-100 cursor-not-allowed' : ''">
                                                    <option value="">Pilih</option>
                                                    <option value="full">Full Day</option>
                                                    <option value="setengah_hari">Half Day</option>
                                                    <option value="off">Off</option>
                                                </select>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <span class="text-sm font-bold"
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
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <div class="text-gray-400">
                                                <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                                </svg>
                                                <p class="text-sm font-medium">Tidak ada karyawan ditemukan</p>
                                                <p class="text-xs mt-1">Coba ubah filter atau kata kunci pencarian</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Total Karyawan Dipilih</div>
                                <div class="text-2xl font-bold text-blue-600" x-text="selectedEmployees.length"></div>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Total Gaji Pokok</div>
                                <div class="text-2xl font-bold text-green-600" x-text="formatRupiah(totalGajiPokok())"></div>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="text-sm text-gray-600 mb-1">Total Gaji Hari Ini</div>
                                <div class="text-2xl font-bold text-purple-600" x-text="formatRupiah(totalGajiHariIni())"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex items-center justify-end space-x-4">
                        <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}"
                           class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all">
                            Batal
                        </a>
                        <button @click="submitForm()"
                                :disabled="!canSubmit()"
                                :class="canSubmit() ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                class="px-8 py-3 font-semibold rounded-xl transition-all shadow-lg flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="loading ? 'Menyimpan...' : 'Simpan Absensi'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function absensiForm() {
            return {
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
                    console.log('Loaded employees:', this.employees.length);
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
                            (e.kandang?.nama_kandang || '').toLowerCase().includes(query) ||
                            this.formatJabatan(e.jabatan).toLowerCase().includes(query)
                        );
                    }

                    this.filteredEmployees = filtered;
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
                        this.employeeGaji[employeeId] = (gajiPokok / 30) / 2;
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
                        minimumFractionDigits: 0
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
                            title: 'Peringatan',
                            text: 'Mohon lengkapi semua data sebelum menyimpan!',
                            confirmButtonColor: '#3b82f6'
                        });
                        return;
                    }

                    this.loading = true;
                    this.errors = {};

                    // Prepare data in format expected by bulkStore
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
                                text: result.message || `Berhasil menyimpan ${result.success_count || employeesData.length} absensi karyawan`,
                                confirmButtonColor: '#3b82f6',
                                timer: 2000
                            });
                            window.location.href = '{{ route(auth()->user()->isManager() ? "manager.absensis.index" : "admin.absensis.index") }}';
                        } else {
                            throw new Error(result.message || 'Terjadi kesalahan');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: error.message || 'Terjadi kesalahan saat menyimpan data',
                            confirmButtonColor: '#ef4444'
                        });
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
