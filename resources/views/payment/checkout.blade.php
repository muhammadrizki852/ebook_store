@extends('layouts.app') {{-- pakai layout utama kamu --}}

@section('content')
<div class="container py-5">
    <div class="card p-4">
        <h3>{{ $book->title }}</h3>
        <p>Harga: Rp {{ number_format($book->price) }}</p>
        <p>Kamu akan diarahkan ke halaman pembayaran Midtrans...</p>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    snap.pay('{{ $snapToken }}', {
        onSuccess: function(result){
            alert("Pembayaran berhasil!");
            window.location.href = "{{ route('my.books') }}"; // ganti ke halaman buku kamu
        },
        onPending: function(result){
            alert("Menunggu pembayaran");
        },
        onError: function(result){
            alert("Pembayaran gagal");
        },
        onClose: function(){
            window.location.href = "{{ url()->previous() }}"; // balik ke halaman buku kalau ditutup
        }
    });
});
</script>
@endsection