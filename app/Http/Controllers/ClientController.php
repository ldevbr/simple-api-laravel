<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Api\v1\Resources\ClientResource;
use App\Api\v1\Requests\StoreClientRequest;
use App\Api\v1\Requests\UpdateClientRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $apiResource = request()->apiNamespace.'Resources\ClientResource';

        abort_if(!auth()->user()->tokenCan('client:index'), 403, 'Not authorized');
        return $apiResource::collection(Client::with('user')->paginate(50));
        //new $apiResource();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        abort_if(!auth()->user()->tokenCan('client:store'), 403, 'Not authorized');

        DB::transaction(function () use($request) {
            $user = User::create([
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password'))
            ]);

            $user->client()->create([
                'name' => $request->get('name')
            ]);

            return response()->json(status: JsonResponse::HTTP_CREATED);
        });


    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $apiResource = request()->apiNamespace.'Resources\ClientResource';

        return new $apiResource($client->load('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        DB::transaction(function () use($request, $client) {
            $clientName = $request->get('name', $client->name);
            $userEmail = $request->get('email', $client->user->email);
            $userPassword = $request->get('password', $client->user->password);

            $client->update([
                'name' => $clientName
            ]);

            $client->user->update([
                'email' => $userEmail,
                'password' => Hash::make($userPassword)
            ]);
        });

        return response()->json(status: JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json(status: JsonResponse::HTTP_NO_CONTENT);
    }
}
