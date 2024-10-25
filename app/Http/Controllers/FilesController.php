<?php

namespace App\Http\Controllers;

use App\Models\FIles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function store(Request $request) {
        try {
            $data = $request->validate([
                'files' => 'required|array', 
                'files.*' => 'file|mimes:png,doc,docx,pdf,jpg,jpeg,zip' 
            ]);
    
            if (empty($data['files'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No files uploaded',
                ]);
            }
    
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not an authorized user'
                ]);
            }

            $fileUsl = [];

            foreach ($data['files'] as $file) {
                $path = $file->store('files','public');
                $urls = Storage::url($path);
                array_push($fileUsl, $urls);
                $fileM = FIles::create([
                    'name' => $file->getClientOriginalName(),
                    'file_path' => $urls,
                    'user_id' => $user->id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully',
            ]);

        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'success' => false,
                'message' => $e,
            ]);
        }
    }

    public function updateFile(Request $request,$id) {
        $data = $request->validate([
            'name' => 'required|string|min:1'
        ]);
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'You are not an authorized user'
            ]);
        }

        $files = Files::findOrFail($id);
        
        if ($files->user_id != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'this is not your file',
            ]);
        } else {
            $files->name = $data["name"];
            $files->save();
            return response()->json([
                'succes' => true,
                'message' => "Renamed",
            ]);
        }
    }

    public function destroy($id) {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'You are not an authorized user'
            ]);
        }

        $files = Files::findOrFail($id);
        if ($files->user_id != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'this is not your file',
            ]);
        } else {
            $filePath = str_replace("storage/files/","storage/app/public/files/", $files->file_path);
            Log::info( Storage::disk('public')->delete($filePath));
            $files->delete();
            return response()->json([
                'success' => true,
                'message' => "File already deleted",
            ]);
        }
    }

    public function download($id) {
        try {
            $user = Auth::user();
    
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'unauthorized'
                ]);
            }
    
            $files = Files::findOrFail($id);

            if ($user->id != $files->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'you do not have access to this file',
                ]);
            }

            Log::info($files->file_path);
    
            if (!file_exists($files->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'file not found',
                ]);
            }
    
            return response()->download($files->file_path, $files->name);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' =>  $e,
            ]);
        }
    }
}
