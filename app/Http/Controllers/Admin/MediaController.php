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
use Intervention\Image\Laravel\Facades\Image;

class MediaController extends Controller
{
    /**
     * Display a listing of media files or return JSON for media picker.
     */
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->query('q');
        $type = $request->query('type');
        $sort = $request->query('sort', 'latest');
        
        $defaultPerPage = ($request->wantsJson() || $request->has('json') || $request->ajax() || $request->is('admin/media/picker-list')) ? 48 : 24;
        $perPage = (int) $request->query('per_page', $defaultPerPage);
        if ($perPage <= 0 || $perPage > 250) {
            $perPage = $defaultPerPage;
        }

        $query = Media::query();

        // Filter by Type
        if ($type === 'image' || $type === 'images') {
            $query->images();
        } elseif ($type === 'document' || $type === 'documents') {
            $query->documents();
        }

        // Filter by Search Term
        if (!empty($search)) {
            $query->search($search);
        }

        // Sort Order (Default: Latest First)
        match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc')->orderBy('id', 'asc'),
            'name_asc', 'name' => $query->orderBy('original_name', 'asc'),
            'name_desc' => $query->orderBy('original_name', 'desc'),
            'size_desc', 'size' => $query->orderByDesc('size'),
            'size_asc' => $query->orderBy('size', 'asc'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };

        $media = $query->paginate($perPage)->withQueryString();

        if ($request->wantsJson() || $request->has('json') || $request->ajax() || $request->is('admin/media/picker-list')) {
            return response()->json([
                'data' => $media->items(),
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
                'total' => $media->total(),
                'per_page' => $media->perPage(),
                'next_page_url' => $media->nextPageUrl(),
                'prev_page_url' => $media->previousPageUrl(),
            ]);
        }

        $totalBytes = (int) Media::sum('size');
        if ($totalBytes >= 1073741824) {
            $totalSizeFormatted = number_format($totalBytes / 1073741824, 2) . ' GB';
        } elseif ($totalBytes >= 1048576) {
            $totalSizeFormatted = number_format($totalBytes / 1048576, 1) . ' MB';
        } elseif ($totalBytes >= 1024) {
            $totalSizeFormatted = number_format($totalBytes / 1024, 1) . ' KB';
        } else {
            $totalSizeFormatted = $totalBytes . ' B';
        }

        return view('admin.media.index', [
            'media' => $media,
            'search' => $search,
            'type' => $type,
            'sort' => $sort,
            'totalCount' => Media::count(),
            'imagesCount' => Media::images()->count(),
            'documentsCount' => Media::documents()->count(),
            'totalSizeFormatted' => $totalSizeFormatted,
        ]);
    }

    /**
     * Handle drag & drop or direct media upload.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480'],
            'file' => ['nullable', 'file', 'max:20480'],
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

        // Native web image extensions that are directly supported by all browsers
        $nativeWebImages = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        $directory = 'media/' . date('Y/m');

        foreach ($filesToProcess as $file) {
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension() ?: '');
            $sanitizedBaseName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) ?: 'media';
            $mimeType = $file->getClientMimeType() ?: 'application/octet-stream';
            $size = $file->getSize();

            $width = null;
            $height = null;
            $filename = null;
            $path = null;

            $isImage = false;
            $interventionImg = null;

            // Attempt to load through Intervention Image if not an excluded document type
            if ($extension !== 'svg' && !in_array($extension, ['pdf', 'mp4', 'zip', 'doc', 'docx', 'txt', 'csv'])) {
                try {
                    $interventionImg = Image::read($file->getRealPath());
                    $isImage = true;
                    $width = $interventionImg->width();
                    $height = $interventionImg->height();
                } catch (\Throwable $e) {
                    $isImage = false;
                }
            } elseif ($extension === 'svg') {
                $isImage = true;
                $mimeType = 'image/svg+xml';
            }

            if ($isImage && !in_array($extension, $nativeWebImages) && $interventionImg) {
                // Convert unsupported/alternative image formats (BMP, TIFF, AVIF, HEIC, JFIF, TGA, etc.) to JPEG
                $filename = $sanitizedBaseName . '-' . time() . '-' . Str::random(6) . '.jpg';
                $path = $directory . '/' . $filename;
                $encodedJpeg = (string) $interventionImg->toJpeg(90);

                Storage::disk('public')->put($path, $encodedJpeg);

                $mimeType = 'image/jpeg';
                $size = strlen($encodedJpeg);
            } else {
                // Native web image or document file: store directly
                $finalExt = $extension ?: 'bin';
                $filename = $sanitizedBaseName . '-' . time() . '-' . Str::random(6) . '.' . $finalExt;
                $path = $file->storeAs($directory, $filename, 'public');

                if ($isImage && $interventionImg) {
                    $width = $interventionImg->width();
                    $height = $interventionImg->height();
                } elseif (str_starts_with($mimeType, 'image/') && $extension !== 'svg') {
                    $imageInfo = @getimagesize($file->getRealPath());
                    if ($imageInfo) {
                        $width = $imageInfo[0];
                        $height = $imageInfo[1];
                    }
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

            if ($media->is_image && $media->mime_type !== 'image/svg+xml') {
                app(\App\Services\Image\ThumbnailService::class)->generateMediaThumbnail($media);
            }

            $uploadedFiles[] = $media;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' file(s) uploaded successfully.',
                'files' => $uploadedFiles,
                'url' => $uploadedFiles[0]->url ?? '',
                'media' => $uploadedFiles[0] ?? null,
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
