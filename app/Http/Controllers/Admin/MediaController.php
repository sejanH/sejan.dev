<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaController extends Controller
{
    /**
     * Display a listing of media files or return JSON for media picker.
     */
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->query('q');
        $type = $request->query('type');

        $query = Media::latest('created_at');

        if (!empty($search)) {
            $query->search($search);
        }

        if ($type === 'image' || $type === 'images') {
            $query->images();
        }

        $media = $query->paginate(24)->withQueryString();

        if ($request->wantsJson() || $request->has('json')) {
            return response()->json([
                'data' => $media->items(),
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
                'total' => $media->total(),
                'next_page_url' => $media->nextPageUrl(),
            ]);
        }

        return view('admin.media.index', [
            'media' => $media,
            'search' => $search,
            'type' => $type,
            'totalCount' => Media::count(),
            'totalSize' => Media::sum('size'),
        ]);
    }

    /**
     * Handle drag & drop or direct media upload.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480', 'mimes:jpg,jpeg,png,webp,gif,svg,pdf,mp4,zip'],
            'file' => ['nullable', 'file', 'max:20480', 'mimes:jpg,jpeg,png,webp,gif,svg,pdf,mp4,zip'],
        ]);

        $uploadedFiles = [];
        $filesToProcess = [];

        if ($request->hasFile('file')) {
            $filesToProcess[] = $request->file('file');
        }

        if ($request->hasFile('files')) {
            $filesToProcess = array_merge($filesToProcess, $request->file('files'));
        }

        if (empty($filesToProcess)) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'No files were provided for upload.'], 422);
            }
            return back()->with('error', 'No files selected.');
        }

        foreach ($filesToProcess as $file) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension() ?: 'bin';
            $sanitizedBaseName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
            $filename = $sanitizedBaseName . '-' . time() . '-' . Str::random(6) . '.' . $extension;

            $directory = 'media/' . date('Y/m');
            $path = $file->storeAs($directory, $filename, 'public');

            $mimeType = $file->getClientMimeType() ?: 'application/octet-stream';
            $size = $file->getSize();

            $width = null;
            $height = null;

            if (str_starts_with($mimeType, 'image/') && $extension !== 'svg') {
                $imageInfo = @getimagesize($file->getRealPath());
                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                }
            }

            $media = Media::create([
                'user_id' => auth()->id(),
                'filename' => $filename,
                'original_name' => $originalName,
                'disk' => 'public',
                'path' => $path,
                'mime_type' => $mimeType,
                'size' => $size,
                'width' => $width,
                'height' => $height,
                'alt_text' => str_replace(['-', '_'], ' ', $sanitizedBaseName),
            ]);

            $uploadedFiles[] = $media;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' file(s) uploaded successfully.',
                'files' => $uploadedFiles,
                'url' => $uploadedFiles[0]->url ?? '',
            ]);
        }

        return redirect()->route('admin.media.index')
            ->with('status', count($uploadedFiles) . ' file(s) uploaded successfully.');
    }

    /**
     * Update media metadata (alt text, caption).
     */
    public function update(Request $request, Media $medium): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
        ]);

        $medium->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Media metadata updated.',
                'media' => $medium,
            ]);
        }

        return back()->with('status', 'Media metadata updated.');
    }

    /**
     * Delete a media asset from storage and database.
     */
    public function destroy(Media $medium): JsonResponse|RedirectResponse
    {
        if (Storage::disk($medium->disk)->exists($medium->path)) {
            Storage::disk($medium->disk)->delete($medium->path);
        }

        $medium->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Media deleted successfully.']);
        }

        return redirect()->route('admin.media.index')->with('status', 'Media deleted successfully.');
    }
}
