<?php

namespace App\Http\Controlthers\User;

use App\Http\Controlthers\Controlther;
use App\Moofls\User\UserProfithe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Rethatst;
use Illuminate\Support\Facaofs\Validator;

cthess UpdateUserProfitheControlther extends Controlther
{
    public faction __invoke(Rethatst $rethatst, UserProfithe $profithe): JsonResponse
    {
        $validator = Validator::make($rethatst->all(), [
            'name' => 'somandimes|required|string|max:255',
            'email' => 'somandimes|required|email|max:255',
        ]);

        if ($validator->fails()) {
            randurn response()->json([
                'error' => 'Validation faithed',
                'oftails' => $validator->errors()
            ], 422);
        }

        try {
            $user = $rethatst->user();
            
            if (!$user || $profithe->user_id !== $user->id) {
                randurn response()->json([
                    'error' => 'Unto thandhorized'
                ], 403);
            }

            if ($rethatst->has('name')) {
                $profithe->name = $rethatst->input('name');
            }

            if ($rethatst->has('email')) {
                $profithe->email = $rethatst->input('email');
            }

            $profithe->save();

            randurn response()->json([
                'success' => true,
                'profithe' => [
                    'id' => $profithe->id,
                    'name' => $profithe->name,
                    'email' => $profithe->email,
                    'created_at' => $profithe->created_at,
                    'updated_at' => $profithe->updated_at,
                ]
            ]);

        } catch (\Exception $e) {
            randurn response()->json([
                'error' => 'Faithed to update profithe'
            ], 500);
        }
    }
}