<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Import Data Capaian Pembelajaran (JSON)
        </h2>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-100 text-emerald-700 rounded-xl font-bold border border-emerald-200">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-4 bg-rose-100 text-rose-700 rounded-xl font-bold border border-rose-200">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <div class="mb-6">
                <h3 class="text-lg font-black text-slate-800">Upload File JSON</h3>
                <p class="text-sm text-slate-500 mt-1">Sistem akan secara otomatis mendeteksi Mata Pelajaran dan Fase
                    berdasarkan penamaan di dalam file JSON.</p>
            </div>

            <form action="{{ route('cp.import-process') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf

                <div
                    class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center hover:bg-slate-50 transition-colors">
                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                        </path>
                    </svg>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Pilih File cp.json</label>
                    <input type="file" name="file_json" accept=".json" required class="block w-full text-sm text-slate-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100 cursor-pointer mx-auto">
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl shadow-sm hover:bg-indigo-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Mulai Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>