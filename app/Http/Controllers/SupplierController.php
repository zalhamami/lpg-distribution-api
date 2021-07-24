<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Order;
use App\OrderStatus;
use App\Product;
use App\Repositories\SupplierRepository;
use App\Repositories\UserRepository;
use App\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Propaganistas\LaravelPhone\PhoneNumber;

class SupplierController extends ApiController
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * SupplierController constructor.
     * @param SupplierRepository $repo
     * @param UserRepository $userRepo
     */
    public function __construct(SupplierRepository $repo, UserRepository $userRepo) {
        $this->repo = $repo;
        $this->userRepo = $userRepo;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->repo->getAll();
        return $this->collectionData($data);
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
            'city_id' => ['required', 'integer', 'exists:cities,id,deleted_at,NULL'],
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getPayload(Request $request)
    {
        $data = $request->all();
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
        $request->validate([
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // Create user for supplier
        $user = $this->userRepo->create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);
        $user->assignRole([Role::USER, Role::SUPPLIER]);

        // Create supplier
        $payload = $this->getPayload($request);
        $payload['user_id'] = $user->id;

        $supplier = $this->repo->create($payload);
        $supplier = $this->repo->getById($supplier->id);

        return $this->singleData($supplier, 201);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeStock(Request $request, int $id)
    {
        $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer'],
        ]);

        $supplier = $this->repo->getById($id);

        $product = Product::findOrFail($request['product_id']);
        if ($product->supplier_id !== $supplier->id) {
            return $this->errorResponse('You are not allowed to access this resource', 403);
        }

        $existingStock = $supplier->stocks->filter(function ($item) use (&$request) {
            return $item->product_id === $request['product_id'];
        })->first();

        if ($existingStock) {
            $existingStock->quantity = $request['quantity'];
            $existingStock->save();
            return $this->singleData($existingStock);
        }

        $response = $supplier->stocks()->create([
            'product_id' => $request['product_id'],
            'quantity' => $request['quantity'],
        ]);

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
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'postal_code' => ['required', 'string'],
        ]);

        $supplier = $this->repo->getById($id);
        $user = $request->user();
        if (!$this->isOwner($supplier, $user)) {
            return $this->errorResponse('You are not allowed to update this resource', 403);
        }

        if ($supplier->address) {
            $supplier->address()->update($request->all());
        } else {
            $supplier->address()->create($request->all());
        }
        $supplier->refresh();

        return $this->singleData($supplier, 201);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function order(Request $request, int $id)
    {
        $request->validate([
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'integer'],
            'products.*.quantity' => ['required', 'integer'],
        ]);

        $user = $request->user();
        $agent = Agent::where('user_id', $user->id)->firstOrFail();
        $supplier = $this->repo->getById($id);

        $purchases = [];
        $totalPrice = 0;
        foreach ($request['products'] as $item) {
            $product = Product::find($item['id']);
            if (!$product) {
                continue;
            }

            $stock = $supplier->stocks->filter(function ($item) use (&$product) {
                return $item->product_id === $product->id;
            })->first();

            if (!$stock || ($stock && $stock->quantity < $item['quantity'])) {
                return $this->errorResponse("Product stock insufficient for id {$product->id}", 400);
            }

            array_push($purchases, [
                'data' => $product,
                'quantity' => $item['quantity'],
            ]);
            $totalPrice += $product->price;
        }

        $data = [
            'buyer_id' => $agent->id,
            'buyer_type' => 'App\\Agent',
            'seller_id' => $supplier->id,
            'seller_type' => 'App\\Supplier',
            'total_price' => $totalPrice,
            'tax' => 0,
            'ordered_at' => Carbon::now(),
            'expired_at' => Carbon::now()->addDay(),
        ];
        $order = DB::transaction(function () use (&$data, &$purchases, &$supplier) {
            $order = Order::create($data);

            foreach ($purchases as $purchase) {
                $order->items()->create([
                    'product_id' => $purchase['data']->id,
                    'product_name' => $purchase['data']->name,
                    'product_price' => $purchase['data']->price,
                    'quantity' => $purchase['quantity'],
                ]);

                $stock = $supplier->stocks->filter(function ($item) use (&$purchase) {
                    return $item->product_id === $purchase['data']->id;
                })->first();
                $stock->quantity -= $purchase['quantity'];
                $stock->save();
            }

            $order->status()->create([
                'status' => OrderStatus::CREATED,
            ]);

            return $order;
        });
        $order->refresh();

        return $this->singleData($order, 201);
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

        $supplier = $this->repo->getById($id);
        $user = $request->user();
        if (!$this->isOwner($supplier, $user)) {
            return $this->errorResponse('You are not allowed to update this resource', 403);
        }

        $payload = $this->getPayload($request);
        if ($supplier->user_id) {
            $payload['user_id'] = $supplier->user_id;
        }
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
