<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Upload file (chia khúc) + xoá file ở card "Upload file" trong /admin/cai-dat.
 * Xem App\Services\FileUploadService.
 */
class FileUploadController extends Controller
{
    public function chunk(Request $request): JsonResponse
    {
        $chunk = $request->file('chunk');

        if (! $chunk) {
            return response()->json(['ok' => false, 'message' => 'Không nhận được dữ liệu file.'], 422);
        }

        try {
            $file = FileUploadService::receiveChunk(
                (string) $request->input('upload_id'),
                (int) $request->input('chunk_index'),
                (int) $request->input('total_chunks'),
                (string) $request->input('file_name'),
                (int) $request->input('total_size'),
                $chunk
            );
        } catch (RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['ok' => true, 'done' => $file !== null, 'file' => $file]);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            FileUploadService::delete((string) $request->input('name'));
        } catch (RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['ok' => true]);
    }
}
