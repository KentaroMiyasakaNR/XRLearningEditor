<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MediaController extends Controller
{
    /**
     * コンストラクタでミドルウェアを設定
     */
    public function __construct()
    {
        // このコントローラーでは、すべてのレスポンスをJSONとして扱う
        // ミドルウェアはroutesファイルで設定する必要があります
    }

    /**
     * メディア一覧を取得
     */
    public function index(Request $request)
    {
        // JSONレスポンスを強制
        $request->headers->set('Accept', 'application/json');
        
        try {
            // 環境情報をログに記録（デバッグ用）
            Log::info('メディア一覧取得: 環境 = ' . app()->environment());
            
            // 現在のログインユーザーIDを取得
            $userId = Auth::id();
            
            // データベースからログインユーザーに紐づくメディア情報とuser_idがnullのメディアを取得
            $videos = Media::where('type', 'videos')
                         ->where(function($query) use ($userId) {
                             $query->where('user_id', $userId)
                                   ->orWhereNull('user_id');
                         })
                         ->get(['id', 'filename', 'title', 'description', 'created_at', 'updated_at']);
                         
            $images = Media::where('type', 'images')
                         ->where(function($query) use ($userId) {
                             $query->where('user_id', $userId)
                                   ->orWhereNull('user_id');
                         })
                         ->get(['id', 'filename', 'title', 'description', 'created_at', 'updated_at']);

            return response()->json([
                'videos' => $videos,
                'images' => $images
            ]);
        } catch (\Exception $e) {
            // 詳細なエラー情報をログに記録
            Log::error('メディアファイル一覧取得エラー: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'メディアファイルの取得に失敗しました: ' . $e->getMessage(),
                'videos' => [],
                'images' => []
            ], 200); // 200を返すことでフロントエンドの処理を継続させる
        }
    }

    /**
     * 新しいメディア情報を登録
     */
    public function store(Request $request)
    {
        // JSONレスポンスを強制
        $request->headers->set('Accept', 'application/json');
        
        try {
            // 環境情報をログに記録（デバッグ用）
            Log::info('メディア情報登録: 環境 = ' . app()->environment(), [
                'type' => $request->input('type')
            ]);
            
            $request->validate([
                'filename' => 'required|string|max:255',
                'type' => 'required|in:videos,images',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'url' => 'nullable|url|max:2048',
            ], [
                'filename.required' => 'ファイル名は必須です。',
                'filename.max' => 'ファイル名は255文字以内で入力してください。',
                'type.required' => 'メディアタイプは必須です。',
                'type.in' => '無効なメディアタイプです。',
                'title.max' => 'タイトルは255文字以内で入力してください。',
                'url.url' => '有効なURLを入力してください。',
                'url.max' => 'URLは2048文字以内で入力してください。',
            ]);

            // 既存のエントリを確認
            $existingMedia = Media::where('filename', $request->filename)
                                  ->where('type', $request->type)
                                  ->first();
            
            if ($existingMedia) {
                return response()->json([
                    'message' => 'このファイル名は既に登録されています。',
                    'media' => $existingMedia
                ], 422);
            }

            // 新しいメディア情報を登録
            $media = Media::create([
                'filename' => $request->filename,
                'type' => $request->type,
                'title' => $request->title ?? $request->filename,
                'description' => $request->description,
                'url' => $request->url,
                'user_id' => Auth::id(),
            ]);

            Log::info('メディア情報を登録しました', ['id' => $media->id, 'filename' => $media->filename]);

            return response()->json([
                'success' => true,
                'message' => 'メディア情報を登録しました。',
                'media' => $media
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // バリデーションエラーをそのまま返す
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // その他のエラー
            Log::error('メディア情報登録エラー: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'メディア情報の登録に失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * メディア情報を削除
     */
    public function destroy(Request $request)
    {
        // JSONレスポンスを強制
        $request->headers->set('Accept', 'application/json');
        
        try {
            $request->validate([
                'id' => 'required|exists:media,id',
            ]);

            $media = Media::findOrFail($request->id);
            $media->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'メディア情報を削除しました。'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('メディア情報削除エラー: ' . $e->getMessage());
            return response()->json([
                'message' => 'メディア情報の削除に失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * メディア情報を更新
     */
    public function update(Request $request)
    {
        // JSONレスポンスを強制
        $request->headers->set('Accept', 'application/json');
        
        try {
            $request->validate([
                'id' => 'required|exists:media,id',
                'filename' => 'required|string|max:255',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'url' => 'nullable|url|max:2048',
            ], [
                'id.required' => 'メディアIDは必須です。',
                'id.exists' => '指定されたメディアIDは存在しません。',
                'filename.required' => 'ファイル名は必須です。',
                'filename.max' => 'ファイル名は255文字以内で入力してください。',
                'title.max' => 'タイトルは255文字以内で入力してください。',
                'url.url' => '有効なURLを入力してください。',
                'url.max' => 'URLは2048文字以内で入力してください。',
            ]);

            $media = Media::findOrFail($request->id);
            
            // 別のメディアで同じファイル名が使用されていないか確認（同じIDは除外）
            $existingMedia = Media::where('filename', $request->filename)
                                ->where('type', $media->type)
                                ->where('id', '!=', $media->id)
                                ->first();
            
            if ($existingMedia) {
                return response()->json([
                    'message' => 'このファイル名は既に別のメディアで使用されています。',
                ], 422);
            }

            // メディア情報を更新
            $media->update([
                'filename' => $request->filename,
                'title' => $request->title ?? $request->filename,
                'description' => $request->description,
                'url' => $request->url,
            ]);

            Log::info('メディア情報を更新しました', ['id' => $media->id, 'filename' => $media->filename]);

            return response()->json([
                'success' => true,
                'message' => 'メディア情報を更新しました。',
                'media' => $media
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // バリデーションエラーをそのまま返す
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // その他のエラー
            Log::error('メディア情報更新エラー: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'メディア情報の更新に失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }
} 