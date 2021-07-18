<?php

namespace App\Http\Controllers;

use App\City;
use App\Repositories\AgentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Propaganistas\LaravelPhone\PhoneNumber;
use Symfony\Component\HttpClient\HttpClient;

class AgentController extends ApiController
{
    /**
     * AgentController constructor.
     * @param AgentRepository $repo
     */
    public function __construct(AgentRepository $repo) {
        $this->repo = $repo;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate([
            'city_id' => ['nullable', 'integer'],
            'district_id' => ['nullable', 'integer'],
        ]);
        $data = $this->repo->getAll();
        return $this->collectionData($data);
    }

    public function showNearMe(Request $request)
    {
        $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $mapsApiKey = env('MAPS_API_KEY');
        $geoRequestUrl = 'https://maps.googleapis.com/maps/api/geocode/json';

        $http = Http::get("{$geoRequestUrl}?latlng={$request['latitude']},{$request['longitude']}&key={$mapsApiKey}");
        $geo = $http->json();

        $result = $geo['results'][0];
        $city = null;
        foreach ($result['address_components'] as $component) {
            if (in_array('administrative_area_level_2', $component['types'])) {
                $city = $component;
            }
        }
        if (!$city) {
            return $this->errorResponse('Parameter not found', 404);
        }

        $city = City::where('name', 'like', "%{$city['long_name']}%")->firstOrFail();
        $response = $this->repo->getALlNearMe($city->id);
        return $this->collectionData($response);
    }

    /**
     * @param Request $request
     */
    private function requestValidation(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'contact' => ['required', 'phone:ID'],
            'open_hour' => ['required', 'date_format:H:i'],
            'close_hour' => ['required', 'date_format:H:i'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id,deleted_at,NULL'],
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getPayload(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $data['contact'] = PhoneNumber::make($request['contact'], 'ID')->formatE164();
        return $data;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->requestValidation($request);
        $payload = $this->getPayload($request);
        $response = $this->repo->create($payload);
        return $this->singleData($response, 201);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeAddress(Request $request, int $id)
    {
        $request->validate([
            'address' => ['required', 'string'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'postal_code' => ['required', 'string'],
        ]);

        $agent = $this->repo->getById($id);
        $user = $request->user();
        if (!$this->isOwner($agent, $user)) {
            return $this->errorResponse('You are not allowed to update this resource', 403);
        }

        if ($agent->address) {
            $agent->address()->update($request->all());
        } else {
            $agent->address()->create($request->all());
        }
        $agent->refresh();

        return $this->singleData($agent, 201);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->repo->getById($id);
        return $this->singleData($data);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $this->requestValidation($request);

        $agent = $this->repo->getById($id);
        $user = $request->user();
        if (!$this->isOwner($agent, $user)) {
            return $this->errorResponse('You are not allowed to update this resource', 403);
        }

        $payload = $this->getPayload($request);
        $response = $this->repo->update($id, $payload);

        return $this->singleData($response);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $data = $this->repo->delete($id);
        return $this->deleteMessage($data);
    }
}
