<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerOutlet;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::withCount('outlets', 'purchaseOrders')
            ->orderBy('nama')
            ->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:255',
            'nama_perusahaan' => 'required|string|max:255',
            'alamat'          => 'required|string',
            'payment_method'  => 'required|in:CASH,TOP7,TOP14,TOP30',
            'outlets'         => 'nullable|array',
            'outlets.*'       => 'nullable|string|max:255',
        ]);

        $customer = Customer::create([
            'nama'            => $validated['nama'],
            'nama_perusahaan' => $validated['nama_perusahaan'],
            'alamat'          => $validated['alamat'],
            'payment_method'  => $validated['payment_method'],
        ]);

        // Create outlets if provided
        if (!empty($validated['outlets'])) {
            foreach (array_filter($validated['outlets']) as $namaOutlet) {
                $customer->outlets()->create(['nama_outlet' => $namaOutlet]);
            }
        }

        return redirect()->route('customers.index')
            ->with('success', "Customer {$customer->nama} berhasil ditambahkan.");
    }

    public function show(Customer $customer): View
    {
        $customer->load(['outlets', 'purchaseOrders.outlet', 'invoices']);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        $customer->load('outlets');
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:255',
            'nama_perusahaan' => 'required|string|max:255',
            'alamat'          => 'required|string',
            'payment_method'  => 'required|in:CASH,TOP7,TOP14,TOP30',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Data customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->purchaseOrders()->exists()) {
            return back()->with('error', 'Customer tidak dapat dihapus karena memiliki Purchase Order.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil dihapus.');
    }

    /**
     * AJAX: get outlets by customer_id
     */
    public function outlets(Customer $customer)
    {
        return response()->json(
            $customer->outlets()->select('id', 'nama_outlet')->orderBy('nama_outlet')->get()
        );
    }
}
