<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Member;
use App\Models\Hobby;

class MemberController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email|unique:members',
            'phone' => 'required|numeric',
            'hobbies' => 'required|array',
            'hobbies.*.nama_hobby' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Proses simpan member dan hobbies
        $member = Member::create([
            'nama' => $request->input('nama'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ]);

        foreach ($request->input('hobbies') as $hobbyData) {
            $hobby = new Hobby(['nama_hobby' => $hobbyData['nama_hobby']]);
            $member->hobbies()->save($hobby);
        }

        return response()->json(['message' => 'Member created successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
         
        // Cek apakah user yang mengupdate data adalah pemilik data
        $member = Member::findOrFail($id);
        if ($user->id !== $member->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'phone' => 'required|numeric',
            'hobbies' => 'required|array',
            'hobbies.*.nama_hobby' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Proses update data
        $member->update([
            'nama' => $request->input('nama'),
            'phone' => $request->input('phone'),
        ]);

        // Hapus semua hobbies yang terkait
        $member->hobbies()->delete();

        // Tambahkan hobbies yang baru
        foreach ($request->input('hobbies') as $hobbyData) {
            $hobby = new Hobby(['nama_hobby' => $hobbyData['nama_hobby']]);
            $member->hobbies()->save($hobby);
        }

        return response()->json(['message' => 'Member updated successfully']);
    }

    public function show($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
         
        // Cek apakah user yang mengakses data adalah pemilik data
        $member = Member::findOrFail($id);
        if ($user->id !== $member->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Proses tampilkan data
        $member = Member::with('hobbies')->findOrFail($id);
        return response()->json(['data' => $member]);
    }
}