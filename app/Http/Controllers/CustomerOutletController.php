<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerOutlet;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CustomerOutletController extends Controller
{
    public function store(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'nama_outlet' => 'required|string|max:255',
        ]);

        $customer->outlets()->create($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', "Outlet {$validated['nama_outlet']} berhasil ditambahkan.");
    }

    public function update(Request $request, Customer $customer, CustomerOutlet $outlet): RedirectResponse
    {
        $validated = $request->validate([
            'nama_outlet' => 'required|string|max:255',
        ]);

        $outlet->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Outlet berhasil diperbarui.');
    }

    public function destroy(Customer $customer, CustomerOutlet $outlet): RedirectResponse
    {
        if ($outlet->customer_id !== $customer->id) {
            abort(403);
        }

        $outlet->delete();

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Outlet berhasil dihapus.');
    }
}
