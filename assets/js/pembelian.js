$(function(){

	// reset jumlah bayar
	remove('pembelianitem');

	if (judul == 'Ubah Pembelian') {
		let pembelianitem = {};
		pembelianitem['jumlahbayar'] = total_bayar_rp;	
		store('pembelianitem', JSON.stringify(pembelianitem));
	}

	$('#table-cari-barang').dataTable({ 
		"processing": true,
		"serverSide": true,
		"order": [],
		"ajax": {
			"url": base_url + "barang/get_barang_json/",
			"type": "POST"
		},
		"columns": [
		{"data" : "id_barang"},
		{"data" : "barcode"},
		{"data": "nama_barang"},
		{
			"data": "harga_pokok",
			render: $.fn.dataTable.render.number( '.', '.', 0, '')
		},
		{
			"data": "id_barang",
			render : function(data, type, row){
				return `<button class="btn btn-info tambah-barang" data-id="${data}"><i class="fa fa-cart-plus"></i></button>`
			}
		}
		],
	})

	// slight update to account for browsers not supporting e.which
	function disableF5(e) { 
		if ((e.which || e.keyCode) == 116){
			e.preventDefault(); 	
			swal({
				title: "Apakah anda yakin?",
				text: "Transaksi akan dibatalkan!",
				icon: "warning",
				buttons: true,
				dangerMode: true,
			}).then((willDelete) => {
				if (willDelete) {
					location.reload(true)
				}
			});
		} 
	};

	// disable refresh
	// $(document).on("keydown", disableF5);


	$('.diskon_wrap').hide();
	$('.potongan_wrap').hide();
	$('.total_wrap').hide();

	$('.no_debit').hide()
	$('.no_kredit').hide()
	$('.lampiran').hide()

	$('.metode_pembayaran').change(function(){
		const val = $(this).val()
		if (val == 'Cash') {
			$('.no_debit').hide()
			$('.no_kredit').hide()
			$('.lampiran').hide()
		}else{
			$('.no_debit').show()
			$('.no_kredit').show()
			$('.lampiran').show()
		}
	})

	function tambah_chart(id, qty = '1')
	{
		if (qty == '') {
			qty = 1;
		}

		$.get(base_url + 'master/get_barang/' + id, function(res){
			const data = JSON.parse(res);

			if (data == null) {
				swal({
					title: "Error!",
					text:  "Barang tidak ditemukan!",
					icon: "error",
					timer : 1500
				});
				$('.barcode').focus();
				return
			}

			const cari = $(document).find('tr[data-id="'+data.id_barang+'"]');

			if (cari.length > 0) {
				const qty = $('input[data-id="'+data.id_barang+'"]').val();
				const qtyPlus = parseFloat(qty) + 1;
				$('input[data-id="'+data.id_barang+'"]').val(qtyPlus);
				const subtotal = qtyPlus * data.harga_pokok;
				const tes = $(document).find('td[data-kode="'+data.id_barang+'"]').html(toRupiah(subtotal, true));
			}else{
				$('.pembelian-item').append(
					`
					<tr data-id="${data.id_barang}">
					<input type="hidden" name="id_barang[]" value="${data.id_barang}">
					<td>${data.nama_pendek}</td>
					<td>${toRupiah(data.harga_pokok, true)}</td>
					<td><input class="form-control qty" name="jumlah[]" data-id="${data.id_barang}" data-harga="${data.harga_pokok}" type="number" value="${qty}" style="width: 5em"></td>
					<td class="subtotal" data-kode="${data.id_barang}">${toRupiah(data.harga_pokok,true)}</td>
					<td><a class="btn btn-danger btn-flat hapus-barang" data-id="${data.id_barang}" data-harga="${data.harga_pokok}"><i class="fa fa-trash"></i></a></td>
					</tr>
					`
					);	
			}

			let item = JSON.parse(get('pembelianitem'));

			// store localstorage
			let pembelianitem = {};

			if (item) {
				if (item.jumlahbayar > 0) {
					pembelianitem['jumlahbayar'] = parseFloat(item.jumlahbayar ) + parseFloat(data.harga_pokok);
				}else{
					pembelianitem['jumlahbayar'] = data.harga_pokok;	
				}
			}else{
				pembelianitem['jumlahbayar'] = data.harga_pokok;	
			}

			store('pembelianitem', JSON.stringify(pembelianitem));

			let itemBaru = JSON.parse(get('pembelianitem'));

			$('.jumlah_bayar').val(toRupiah(itemBaru.jumlahbayar));
			$('.total_jumlah_bayar').val(toRupiah(itemBaru.jumlahbayar));

			$('.qty_brg').val('');
			$('.qty_brg').focus();

			updateKembalian();
			
		})
	}

	function updateKembalian()
	{
		let item = JSON.parse(get('pembelianitem'));
		const cash = $(document).find('.cash').val();
		if (parseFloat(cash) > 0) {
			const baru = cash - item.jumlahbayar;
			$('.kembalian').val(toRupiah(baru));
		}	
	}

	$('.pelanggan').change(function(){

		$('#id_kategori').val('pilih');
		$('.barang-kategori').html('');

		$.get(base_url + 'master/get_pelanggan/' + $(this).val(), function(res){
			const data = JSON.parse(res)
			if (data.jenis == 'member') {
				$('.member').val(1);
				$('.diskon_wrap').show();
				$('.potongan_wrap').show();
				$('.diskon_member').text(pengaturan.diskon_member)
				$('.potongan_member').text(toRupiah(pengaturan.potongan_member))
			}else{
				$('.member').val(0);
				$('.diskon_wrap').hide();
				$('.potongan_wrap').hide();
				$('.diskon_member').text('')
				$('.potongan_member').text('')
			}
		})
	})

	$(document).on('click', '.tambah-barang', function(){
		const id = $(this).data('id'); 
		tambah_chart(id);
	})

	$(document).on('click','.hapus-barang', function(e){
		e.preventDefault();
		$(this).closest('tr').remove();
		$('.harga').html('Rp. 0');
		const id = $(this).data('id');
		const harga = $(this).data('harga');
		const qty = $(this).closest('tr[data-id="'+id+'"]').find('input[data-id="'+id+'"]').val();

		const jumlah = parseFloat(harga) * parseFloat(qty);

		const jumlah1 = JSON.parse(get('pembelianitem')).jumlahbayar;
		const jumlah2 = parseFloat(jumlah1) - jumlah;

		let pembelianitem = {};
		pembelianitem['jumlahbayar'] = jumlah2;
		store('pembelianitem', JSON.stringify(pembelianitem));
		$('.jumlah_bayar').val(toRupiah(jumlah2));
		$('.total_jumlah_bayar').val(toRupiah(jumlah2));
		$('.barcode').focus();
		updateKembalian();
	})

	$(document).on('click', '.batal', function(e){
		e.preventDefault();
		remove('pembelianitem');
		$('.pembelian-item').html('');
		$('.harga').html('Rp. 0');
		$('.kembalian').html('');
		$('.cash').val('');
		$('.kembalian').val('');
		$('.jumlah_bayar').val('Rp. 0');
		$('.total_jumlah_bayar').val('Rp. 0');
		$('.barcode').focus();
	})


	$('.cash').keyup(function(e){
		const cash = $(this).val();
		const jumlah = parseFloat($('.jumlah_bayar').val().replace('Rp. ', '',).replace('.', '').replace('.', ''));

		let kembalian = toRupiah(parseFloat(cash) - parseFloat(jumlah));
		if (kembalian == 'Rp. NaN') {
			kembalian = "Rp. 0";
		}
		$('.kembalian').val(kembalian);
	})

	$(document).on('keyup change', '.qty', function(e){
		let jmlBayar = JSON.parse(get('pembelianitem')).jumlahbayar;
		if (jmlBayar == null) {
			jmlBayar = 0;
		}
		let jumlah = $(this).val()
		if (jumlah == '') {
			jumlah = 0;
		}
		const id = $(this).data('id')
		let subtotal = $(document).find('td[data-kode="'+id+'"]').html();
		let subN = subtotal.replace('Rp. ', '').replace('.', '').replace('.', '');
		const harga = $(this).data('harga');
		const jumlahHarga = parseFloat(jumlah) * parseFloat(harga);

		$(document).find('td[data-kode="'+id+'"]').html(toRupiah(jumlahHarga, true));

		var total = 0;
		$('.subtotal').each(function(index,el){
			var val = $(this).html().replace('Rp. ', '').replace('.', '').replace('.', '');
			total += parseFloat(val)
		})
		pembelianitem = {};
		pembelianitem['jumlahbayar'] = total;
		store('pembelianitem', JSON.stringify(pembelianitem));

		let baru = JSON.parse(get('pembelianitem')).jumlahbayar;
		if (baru == null) {
			baru = 0;
		}
		let rp = toRupiah(baru);
		if (rp == 'Rp. NaN') {
			rp = "Rp. 0";
		}
		$('.jumlah_bayar').val(rp);
		$('.total_jumlah_bayar').val(rp);
		updateKembalian();

	})

	$(document).on('keydown', '.qty', function(e){
		if (e.which == 13) {
			$('.barcode').focus();
			return false;
		}
	})


	$('.barcode').keydown(function(e){
		const id = $(this).val();
		if (e.which == '13') {
			e.preventDefault();
			e.stopPropagation();
			tambah_chart(id);
			$(this).val('');
			$(this).focus();
		}
	})

	$('.kategori').change(function(){
		let id = $(this).val();
		if (id == '') {
			id = 'SEMUA'
		}
		$('.barang-kategori').empty()

		$.get(base_url + 'master/get_barang_by_kategori/' + id, function(res){
			const data = JSON.parse(res);
			$(data).each(function(index, el){
				let gambar;
				if (el.gambar == '' || el.gambar == null) {
					gambar = `<img src="${base_url}assets/img/barang/noimage.jpg" alt="barang" class="img-responsive">`;
				}else{
					gambar = `<img src="${base_url}assets/img/barang/${el.gambar}" alt="barang" class="img-responsive">`;
				}

				$('.barang-kategori').append(
					`
					<tr>
					<td>${gambar}</td>
					<td>${el.nama_pendek}</td>
					<td>${toRupiah(el.harga_pokok,true)}</td>
					<td><button class="btn btn-info tambah-barang" data-id="${el.id_barang}"><i class="fa fa-edit"></i></button></td>
					</tr>
					`
					);
			});
		})
	})

	$('.cari_barang_nama').keyup(function(e){
		const name = $(this).val();
		$('.barang-kategori').empty()

		if (e.keyCode == 13) {
			$.get(base_url + 'barang/get_barang_by_barcode/' + name, function(res){
				const data = JSON.parse(res);
				if (data.length == 0) {
					$('.barang-kategori').html('<tr><td colspan="4"><h5 class="text-center">Data Tidak Ditemukan</td></tr>');
				}
				$(data).each(function(index, el){
					let gambar;
					if (el.gambar == '' || el.gambar == null) {
						gambar = `<img src="${base_url}assets/img/barang/noimage.jpg" alt="barang" class="img-responsive">`;
					}else{
						gambar = `<img src="${base_url}assets/img/barang/${el.gambar}" alt="barang" class="img-responsive">`;
					}
					
					$('.barang-kategori').append(
						`
						<tr>
						<td>${gambar}</td>
						<td>${el.nama_pendek}</td>
						<td>${toRupiah(el.harga_pokok,true)}</td>
						<td><button class="btn btn-info tambah-barang" data-id="${el.id_barang}"><i class="fa fa-edit"></i></button></td>
						</tr>
						`
						);
				});
			})
		}else{
			$.get(base_url + 'barang/get_barang_by_name/' + name, function(res){
				const data = JSON.parse(res);
				if (data.length == 0) {
					$('.barang-kategori').html('<tr><td colspan="4"><h5 class="text-center">Data Tidak Ditemukan</td></tr>');
				}
				$(data).each(function(index, el){
					let gambar;
					if (el.gambar == '' || el.gambar == null) {
						gambar = `<img src="${base_url}assets/img/barang/noimage.jpg" alt="barang" class="img-responsive" width="100%">`;
					}else{
						gambar = `<img src="${base_url}assets/img/barang/${el.gambar}" alt="barang" class="img-responsive" width="100%">`;
					}

					$('.barang-kategori').append(
						`
						<tr>
						<td>${gambar}</td>
						<td>${el.nama_pendek}</td>
						<td>${toRupiah(el.harga_pokok,true)}</td>
						<td><button class="btn btn-info tambah-barang" data-id="${el.id_barang}"><i class="fa fa-edit"></i></button></td>
						</tr>
						`
						);
				});
			})
		}

		

	})


	// shortcut

})