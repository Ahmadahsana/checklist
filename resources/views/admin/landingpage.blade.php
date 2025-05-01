@extends('layouts.vertical', ['title' => 'Validasi Pembayaran'])

@section('css')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endsection

@section('content')
    @include("layouts.shared.page-title", ["subtitle" => "landing", "title" => "Ubah konten landing page"])

    <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-md">

        {{-- Alert Session --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        <h1 class="text-2xl font-bold mb-6">Content Landing Page</h1>

        @if ($errors->any())
            <div class="mt-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    <form action="{{ route('admin.landingpage.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
            <input type="text" name="title" id="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('title', $landingpage->title ?? '') }}" required>
        </div>

        <div class="mb-4">
            <label for="image" class="block text-sm font-medium text-gray-700">Gambar</label>
            <input type="file" name="image" id="image" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @if (!empty($landingpage->image))
            {{-- <p>Full path: {{ asset('storage/' . $landingpage->image) }}</p> --}}
    
            <img src="{{ asset('storage/' . $landingpage->image) }}" alt="Current Image" class="mt-2 w-32 h-32 object-cover">
            @endif
        </div>

        <div class="mb-4">
            <label for="content" class="block text-sm font-medium text-gray-700">Konten</label>
            
            <!-- Editor Container -->
            <div id="quill-editor" class="bg-white border border-gray-300 rounded-md" style="min-height: 200px;">
                {!! old('content', $landingpage->content ?? '') !!}
            </div>
        
            <!-- Hidden Textarea to Store HTML -->
            <textarea name="content" id="content" style="display:none;">{{ old('content', $landingpage->content ?? '') }}</textarea>


        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
        </div>

        
    </form>
    </div>
@endsection

@section('script')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

{{-- <script>
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Tulis konten di sini...',
        modules: {
            toolbar: [
                [{ header: [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                ['link', 'image'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['clean']
            ]
        }
    });

    // Saat submit form, isi textarea dengan konten Quill (HTML)
    document.querySelector('form').onsubmit = function() {
        document.querySelector('#content').value = quill.root.innerHTML;
    };
</script> --}}

<script>
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Tulis konten di sini...',
        modules: {
            toolbar: [
                [{ header: [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                ['link', 'image'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['clean']
            ]
        }
    });

    const textarea = document.getElementById('content');

    // Set isi awal ke dalam Quill
    quill.root.innerHTML = textarea.value;

    // Setiap kali isi Quill berubah, update textarea
    quill.on('text-change', function() {
        textarea.value = quill.root.innerHTML.trim();
    });
</script>
@endsection
