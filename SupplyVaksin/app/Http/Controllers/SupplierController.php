<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

/**
 * Admin  : create, update, delete, list
 * Client : list only — clients never manage supplier relationships
 */
class SupplierController extends BaseController
{
    public function index()
    {
        $suppliers = Supplier::withCount('vaccines')->orderBy('supplier_id')->get();
        return $this->ok($suppliers);
    }

    public function show($id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) return $this->fail('Supplier not found.', 404);
        return $this->ok($supplier);
    }

    public function store(Request $request)
    {
        if ($err = $this->requireAdminOrFail()) return $err;
        if ($err = $this->requireFields($request, ['supplier_id', 'supplier_name'])) return $err;

        try {
            Supplier::create([
                'supplier_id'   => $request->input('supplier_id'),
                'supplier_name' => $request->input('supplier_name'),
                'contact'       => $request->input('contact', ''),
            ]);
            return $this->ok(null, 'Supplier added.');
        } catch (QueryException $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        if ($err = $this->requireAdminOrFail()) return $err;
        if ($err = $this->requireFields($request, ['supplier_name'])) return $err;

        $supplier = Supplier::find($id);
        if (!$supplier) return $this->fail('Supplier not found.', 404);

        $supplier->update([
            'supplier_name' => $request->input('supplier_name'),
            'contact'       => $request->input('contact', ''),
        ]);
        return $this->ok(null, 'Supplier updated.');
    }

    public function destroy($id)
    {
        if ($err = $this->requireAdminOrFail()) return $err;

        $supplier = Supplier::find($id);
        if (!$supplier) return $this->fail('Supplier not found.', 404);

        try {
            $supplier->delete();
            AuditLog::log('delete', 'suppliers', $id, "Deleted supplier #{$id}");
        return $this->ok(null, 'Supplier deleted.');
        } catch (QueryException $e) {
            return $this->fail('Cannot delete: supplier is referenced by one or more vaccines.', 409);
        }
    }
}
