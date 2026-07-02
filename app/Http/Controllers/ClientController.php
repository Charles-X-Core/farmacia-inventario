<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $clients = Client::where('company_id', $user->company_id)
            ->with('typeDocument')
            ->get();

        return view('clients.index', compact('clients'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'type_document_id' => 'nullable|exists:type_documents,id',
            'document' => 'nullable|string|max:20',
            'nit' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        if ($request->type_document_id && $request->document) {
            $typeName = \App\Models\TypeDocument::find($request->type_document_id)?->name;
            $doc = $request->document;

            if ($typeName === 'RUC' && strlen($doc) < 11) {
                return back()->withErrors(['document' => 'El RUC debe tener mínimo 11 dígitos.'])->withInput();
            }
            if (in_array($typeName, ['DNI', 'PASAPORTE', 'CARNE DE EXTRANJERIA']) && strlen($doc) < 7) {
                return back()->withErrors(['document' => 'El documento debe tener mínimo 7 dígitos.'])->withInput();
            }
        }

        Client::create([
            'company_id' => $user->company_id,
            'name' => $request->name,
            'last_name' => $request->last_name,
            'type_document_id' => $request->type_document_id,
            'document' => $request->document,
            'nit' => $request->document ?? $request->nit,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
        ]);

        return back()->with('success', 'Cliente registrado.');
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $client->update($request->only([
            'name', 'last_name', 'type_document_id', 'document', 'nit', 'phone', 'email', 'address'
        ]));

        return back()->with('success', 'Cliente actualizado.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return back()->with('success', 'Cliente eliminado.');
    }

    public function search(Request $request)
    {
        $user = auth()->user();
        $term = $request->q;

        $clients = Client::where('company_id', $user->company_id)
            ->where('status', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('document', 'like', "%{$term}%")
                  ->orWhere('nit', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get();

        return response()->json($clients);
    }
}
