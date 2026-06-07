<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengguna\StoreUserRequest;
use App\Http\Requests\Pengguna\UpdateUserRequest;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();
        $role = $request->string('role')->trim()->toString();
        $status = $request->string('status')->trim()->toString();
        $sortBy = $request->string('sort')->trim()->toString();
        $sortDir = $request->string('dir')->trim()->toString();
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'asc';
        $perPage = min(100, max(10, (int) $request->input('per_page', 10)));
        $allowedSorts = ['name', 'role', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'name';
        }

        $users = User::query()
            ->with('pegawai')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('name', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%')
                        ->orWhereHas('pegawai', fn ($pegawai) => $pegawai->where('nama', 'like', '%' . $q . '%'));
                });
            })
            ->when($role !== '', fn ($query) => $query->where('role', $role))
            ->when($status !== '', fn ($query) => $query->where('is_active', $status === 'aktif'))
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('pengguna.user.index', compact('users', 'q', 'role', 'status', 'sortBy', 'sortDir', 'perPage'));
    }

    public function create(): View
    {
        return view('pengguna.user.create', [
            'pegawaiList' => $this->pegawaiList(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        User::create($data);

        return redirect()->route('pengguna.user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('pengguna.user.edit', [
            'user' => $user,
            'pegawaiList' => $this->pegawaiList($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        if ((int) auth()->id() === (int) $user->id && ! $data['is_active']) {
            return back()->withInput()->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang digunakan.');
        }

        $user->update($data);

        return redirect()->route('pengguna.user.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ((int) auth()->id() === (int) $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        try {
            $user->delete();
        } catch (QueryException) {
            return back()->with('error', 'User tidak dapat dihapus karena masih terhubung dengan data lain.');
        }

        return redirect()->route('pengguna.user.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function destroyBulk(Request $request): RedirectResponse
    {
        $currentId = (int) auth()->id();
        $ids = array_filter(
            array_map('intval', (array) $request->input('ids', [])),
            fn ($id) => $id > 0 && $id !== $currentId
        );

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih, atau Anda tidak dapat menghapus akun sendiri.');
        }

        try {
            $jumlah = User::whereIn('id', $ids)->delete();

            return redirect()->route('pengguna.user.index')
                ->with('success', $jumlah . ' user berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException) {
            return back()->with('error', 'Sebagian data tidak dapat dihapus karena masih digunakan oleh data lain.');
        }
    }

    private function pegawaiList(?User $user = null)
    {
        return Pegawai::query()
            ->where('status', Pegawai::STATUS_AKTIF)
            ->where(function ($query) use ($user) {
                $query->doesntHave('user');

                if ($user?->pegawai_id) {
                    $query->orWhere('id', $user->pegawai_id);
                }
            })
            ->orderBy('nama')
            ->get();
    }
}
