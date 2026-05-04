@extends('layouts.admin')

@section('title', 'Media Library')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between">
        <h1 class="text-xl font-bold">Media Library</h1>
        <button id="uploadBtn" class="bg-blue-600 text-white px-3 py-1 rounded">⬆ Upload Files</button>
    </div>

    <!-- Dropzone area -->
    <div class="border-2 border-dashed p-6 text-center my-4 cursor-pointer" id="dropzone">
        📂 Drag & drop files here or click to upload
        <input type="file" id="fileInput" multiple class="hidden">
    </div>

    <!-- Media Grid -->
    <div id="mediaGrid" class="grid grid-cols-4 gap-3"></div>
</div>

<!-- Hidden Modal for Media Picker -->
<div id="mediaModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white w-3/4 h-3/4 mx-auto mt-10 overflow-auto p-3 rounded">
        <div class="flex justify-between"><h2>Select Image</h2><button onclick="closeMediaModal()">×</button></div>
        <div id="modalMediaGrid" class="grid grid-cols-4 gap-2 mt-3"></div>
    </div>
</div>

<script>
    let mediaCallback = null;
    window.addEventListener('open-media-modal', (e) => {
        mediaCallback = e.detail.callback;
        document.getElementById('mediaModal').classList.remove('hidden');
        loadMediaIntoModal();
    });

    function closeMediaModal() {
        document.getElementById('mediaModal').classList.add('hidden');
        mediaCallback = null;
    }

    async function loadMedia() {
        let res = await fetch('{{ route("admin.media.index") }}', {headers: {'Accept': 'application/json'}});
        let data = await res.json();
        renderGrid(data, 'mediaGrid');
    }

    async function loadMediaIntoModal() {
        let res = await fetch('{{ route("admin.media.index") }}', {headers: {'Accept': 'application/json'}});
        let data = await res.json();
        renderGrid(data, 'modalMediaGrid', true);
    }

    function renderGrid(mediaList, containerId, isModal = false) {
        let container = document.getElementById(containerId);
        container.innerHTML = '';
        mediaList.forEach(m => {
            let div = document.createElement('div');
            div.className = 'border rounded p-1 text-center';
            div.innerHTML = `
                <img src="/storage/${m.file_path}" class="h-20 w-full object-cover">
                <div class="text-xs truncate">${m.file_name}</div>
                ${isModal ? `<button onclick="selectImage('/storage/${m.file_path}')" class="bg-blue-500 text-white w-full mt-1 text-xs">Select</button>` : ''}
            `;
            container.appendChild(div);
        });
    }

    function selectImage(url) {
        if(mediaCallback) mediaCallback(url);
        closeMediaModal();
    }

    // Upload logic
    document.getElementById('uploadBtn').onclick = () => document.getElementById('fileInput').click();
    document.getElementById('fileInput').onchange = async (e) => {
        let files = e.target.files;
        let formData = new FormData();
        for(let f of files) formData.append('files[]', f);
        await fetch('{{ route("admin.media.upload") }}', {method: 'POST', body: formData, headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
        loadMedia();
    };

    loadMedia();
</script>
@endsection
