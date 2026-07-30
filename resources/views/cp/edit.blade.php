<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Edit Capaian Pembelajaran</h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('cp.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">&larr;
                Batal & Kembali</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <form action="{{ route('cp.update', $cp->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                @if($errors->any())
                <div class="p-4 bg-rose-50 text-rose-700 rounded-lg text-sm font-medium">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kode CP (Unik)</label>
                        <input type="text" name="kode_cp" value="{{ old('kode_cp', $cp->kode_cp) }}"
                            class="w-full text-sm border-slate-300 rounded-lg focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mata Pelajaran</label>
                        <input type="text" name="mata_pelajaran"
                            value="{{ old('mata_pelajaran', $cp->mata_pelajaran) }}"
                            class="w-full text-sm border-slate-300 rounded-lg focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Fase</label>
                        <input type="text" name="fase" value="{{ old('fase', $cp->fase) }}"
                            class="w-full text-sm border-slate-300 rounded-lg focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Elemen</label>
                        <input type="text" name="elemen" value="{{ old('elemen', $cp->elemen) }}"
                            class="w-full text-sm border-slate-300 rounded-lg focus:ring-indigo-500" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi CP</label>
                    <textarea name="deskripsi_cp" rows="5"
                        class="w-full text-sm border-slate-300 rounded-lg focus:ring-indigo-500"
                        required>{{ old('deskripsi_cp', $cp->deskripsi_cp) }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>