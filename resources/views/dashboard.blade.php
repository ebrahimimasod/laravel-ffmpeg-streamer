@extends('layouts.app')

@section('content')
    <div class="h-screen w-full bg-gray-100 flex items-center justify-center p-6">
        <div class="w-full h-full bg-white rounded-lg shadow-lg p-8">
            <!-- Header Section -->
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center text-red-500 hover:text-red-700">
                        Logout
                    </button>
                </form>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Upload Form -->
            <div class="mb-8">
                <div class="bg-blue-50 p-6 rounded-lg shadow-inner">
                    <h2 class="text-2xl font-semibold text-blue-800 mb-4">Upload New File</h2>
                    <form action="{{ route('media.upload') }}" method="POST" enctype="multipart/form-data"
                          class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6">
                        @csrf
                        <!-- File Input -->
                        <div class="flex-1 w-full">
                            <label for="file" class="block text-gray-700 mb-2">Select File</label>
                            <input type="file" name="file" id="file" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('file')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Visibility Select -->
                        <div class="flex-1 w-full">
                            <label for="visibility" class="block text-gray-700 mb-2">Visibility</label>
                            <select name="visibility" id="visibility" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="public">Public</option>
                                <option value="private">Private</option>
                            </select>
                            @error('visibility')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="w-full md:w-auto">
                            <button type="submit"
                                    class="w-full md:w-auto bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4v16m8-8H4"/>
                                </svg>
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Files Table -->
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Your Files</h2>

                @if($mediaFiles->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg overflow-hidden">
                            <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-center">#</th>
                                <th class="py-3 px-6 text-left">File Name</th>
                                <th class="py-3 px-6 text-left">UUID</th>
                                <th class="py-3 px-6 text-center">Visibility</th>
                                <th class="py-3 px-6 text-center">Uploaded At</th>
                                <th class="py-3 px-6 text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                            @foreach($mediaFiles as $index => $media)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-center">{{ $mediaFiles->firstItem() + $index }}</td>
                                    <td class="py-3 px-6 text-left flex items-center">
                                        {{ $media->name }}
                                    </td>
                                    <td class="">
                                        {{$media->uuid}}
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        @if($media->visibility === 'public')
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">Public</span>
                                        @else
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Private</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-center">{{ $media->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center space-x-2">
                                            <!-- Download Link -->
                                            <a href="{{ route('media.download', $media->uuid) }}"
                                               class="text-blue-500 hover:text-blue-700 flex items-center">
                                                Download
                                            </a>

                                            <!-- Delete Form -->
                                            <form action="{{ route('media.destroy', $media->uuid) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this file?')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-500 hover:text-red-700 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1"
                                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $mediaFiles->links('pagination::tailwind') }}
                    </div>
                @else
                    <div class="p-6 bg-gray-50 rounded-lg shadow-inner">
                        <p class="text-gray-600">No files uploaded yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
