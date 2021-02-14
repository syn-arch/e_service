const base_url = $('meta[name="base_url"]').attr('content');

function get(t) {
	if ("undefined" != typeof Storage) return localStorage.getItem(t);
	alert("Please use a modern browser as this site needs localstroage!");
}
function store(t, e) {
	"undefined" != typeof Storage ? localStorage.setItem(t, e) : alert("Please use a modern browser as this site needs localstroage!");
}
function remove(t) {
	"undefined" != typeof Storage ? localStorage.removeItem(t) : alert("Please use a modern browser as this site needs localstroage!");
}

function toRupiah(angka = '0', idr = false)
{
	var rupiah = '';		
	if (angka == null) {
		angka = '0';
	}
	var angkarev = angka.toString().split('').reverse().join('');
	for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
		if (idr == true) {
			return rupiah.split('',rupiah.length-1).reverse().join('');
		}else{
			return 'Rp. '+rupiah.split('',rupiah.length-1).reverse().join('');
		}
	}

	$('.buka_laci').click(function(e){
		e.preventDefault();
		$.get(base_url + 'penjualan/buka_laci', function(){
			swal({
				title: "Berhasil!",
				text:  "Laci Berhasil Dibuka",
				icon: "success",
				timer : 1500
			});
		})
	})

// select2
$('.select2').select2()

// datatable
$('.datatable').dataTable();

$('.datatable-stok-opname').dataTable({
	"pageLength" : 50,
});

// ubah akses role
$('.ubah_menu').click(function(){
	const id_menu = $(this).data('menu');
	const id_role = $(this).data('role');

	$.ajax({
		url : `${base_url}petugas/ubah_akses_role/${id_menu}/${id_role}`,
		method : 'post',
		success : function() {
			swal('Berhasil', 'Data berhasil diubah', 'success');
			window.location.reload(true)
		}
	})
})

$('._closeRegister').click(function(){
	swal({
		title: "Apakah anda yakin?",
		text: "Tutup register tidak dapat dikembalikan!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	}).then((willDelete) => {
		if (willDelete) {
			window.location = $(this).data('href')
		}
	});
})

// role
$(document).on('click', '.hapus_role', function(){
	hapus($(this).data('href'))
})

$(document).on('click', '.hapus_backup', function(){
	hapus($(this).data('href'))
})

$(document).on('click', '.hapus_register', function(){
	hapus($(this).data('href'))
})

$(document).on('click', '.hapus_semua_barang', function(){
	hapus($(this).data('href'))
})

$(document).on('click', '.hapus_pembayaran', function(){
	hapus($(this).data('href'))
})

$(document).on('click', '.hapus_stok_opname', function(){
	hapus($(this).data('href'))
})

// profit
$('.harga_jual').keyup(function (e) {
	const harga_pokok = $('.harga_pokok').val();
	const harga_jual = $(this).val();
	const profit = parseInt(harga_jual) - parseInt(harga_pokok);
	$('.profit').val(profit)
})