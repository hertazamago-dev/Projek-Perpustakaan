<?php
namespace App\Http\Controllers\Member;
use App\Models\Temp;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;
class TempController extends Controller
{
    public function tambahKeranjang(Request $request)
    {
        // Cek apakah buku yang diklik booking dengan user yang sedang login sudah ada di tabel temp atau belum
        $cek_keranjang = Temp::where([
            'id_buku' => $request->id,
            'id_user' =>
                auth()->user()->id
        ])->count();
        $cek_pinjam = DB::table('pinjam as a')
            ->join('pinjam_detail as b', 'a.no_pinjam', '=', 'b.no_pinjam')
            ->where('a.id_user', auth()->user()->id)
            ->where('b.id_buku', $request->id)
            ->where('b.status', 'Pinjam')
            ->count();
        // Jika buku yang diklik booking sudah ada di temp
        if ($cek_keranjang > 0 || $cek_pinjam > 0) {
            return redirect()->route('member.index')->with('error', 'Buku sudah ada di keranjang atau sedanag dipinjam');
        }
        // Cek apakah ada data buku yang belum diambil (masih booking)
        $cek_booking = Booking::where('id_user', auth()->user()->id)->count();
        if ($cek_booking > 0) {
            return redirect()->route('member.index')->with('error', 'Masih ada buku yang belum diambil, silakan ambil buku atau menunggu pembatalan otomatis');
        }
        // Cek jumlah buku dalam keranjang
        $cek_limit = Temp::where(['id_user' => auth()->user()->id])->count();
        // Cek jumlah buku yang sedang dipinjam
        $total_pinjam = DB::table('pinjam as a')
            ->join('pinjam_detail as b', 'a.no_pinjam', '=', 'b.no_pinjam')
            ->where('a.id_user', auth()->user()->id)
            ->where('b.status', 'Pinjam')
            ->count();
        // Jika jumlah keranjang + booking + pinjam sama dengan/lebih dari 3
        if (($total_pinjam + $cek_booking + $cek_limit) >= 3) {
            return redirect()->route('member.index')->with('error', 'Total buku di keranjang, booking dan sedang pinjam tidak boleh lebih dari 3');
        }
        $data['id_buku'] = $request->id;
        $data['id_user'] = auth()->user()->id;
        Temp::create($data);
        return redirect()->route('member.index')->with('success', 'Buku berhasil ditambahkan ke keranjang!');
    }
    public function dataKeranjang(User $user)
    {
        $data_keranjang = Temp::with('buku', 'buku.kategori')->where('id_user', $user->id)->get();
        if (count($data_keranjang) == 0) {
            return redirect()->route('member.index')->with('info', 'Tidak ada buku di keranjang');
        }
        return view('member.data_keranjang', compact('data_keranjang'));
    }
}