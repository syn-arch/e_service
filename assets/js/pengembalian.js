$(function(){

	$('.cari_barang').click(function(){
		$("#table-cari-barang").dataTable().fnDestroy()
		const golongan = $('.golongan').val()
		$('#table-cari-barang').dataTable({ 
			"processing": true,
			"serverSide": true,
			"order": [],
			"ajax": {
				"url": base_url + "barang/get_harga_barang_json/" + golongan,
				"type": "POST"
			},
			"columns": [
			{"data" : "id_barang"},
			{"data" : "barcode"},
			{"data": "nama_barang"},
			{
				"data": "harga_jual",
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

	// reset jumlah bayar
	remove('pengembalian');

	$('.penjualan').change(function(){

		const id = $(this).val()

		$('.pengembalian-item').empty()

		$.get(base_url + 'penjualan/get_penjualan/' + id, function(res){
			const data = JSON.parse(res)
			$('.pelanggan').val(data.id_pelanggan)
			$('.golongan').val(data.type_golongan)

			if (data.jenis == 'member') {
				$('.member').val(1);
			}else{
				$('.member').val(0);
			}

			$('.jumlah_bayar').val(toRupiah(data.total_bayar))
			$('.total_jumlah_bayar').val(toRupiah(data.total_bayar))
			$('.diskon').val(data.diskon)
			$('.potongan').val(data.potongan)

			let pengembalian =  {};
			pengembalian['jumlahbayar'] = data.total_bayar;	
			store('pengembalian', JSON.stringify(pengembalian));

			$.get(base_url + 'penjualan/get_detail_penjualan/' + id, function(res){
				const data = JSON.parse(res)
				$.each(data, function(index,val){

					diskon = (val.diskon / 100) * val.harga_jual;
					tot = val.harga_jual - diskon;

					harga_brg = tot;
					harga_asli = val.harga_jual;

					const subtotal = harga_brg * val.jumlah

					if (val.diskon == null) {
						diskon = 0;
					}else{
						diskon = val.diskon;
					}

					$('.pengembalian-item').append(
						`
						<tr data-id="${val.id_barang}">
						<input type="hidden" name="type_golongan[]" value="${val.type_golongan}">
						<input type="hidden" name="id_barang[]" value="${val.id_barang}">
						<td>${val.nama_barang}</td>
						<td>${toRupiah(harga_asli, true)}</td>
						<td>${diskon}</td>
						<td><input class="form-control qty" name="jumlah[]" data-id="${val.id_barang}" data-harga="${harga_brg}" type="number" step="0.1" value="${val.jumlah}" style="width: 5em"></td>
						<td class="subtotal" data-kode="${val.id_barang}">${toRupiah(subtotal,true)}</td>
						<td><a class="btn btn-danger btn-flat hapus-barang" data-id="${val.id_barang}" data-harga="${harga_brg}"><i class="fa fa-trash"></i></a></td>
						</tr>
						`
						);	
				})
			})

		})

	});

	function tambah_chart(id, golongan)
	{
		$.get(base_url + 'barang/get_barang/' + id + '/' + golongan, function(res){
			const data = JSON.parse(res);
			const harga_jual = data.harga_jual

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

			const id = data.id_barang;
			$('.harga').html(harga_jual)

			const cari = $(document).find('tr[data-id="'+data.id_barang+'"]');

			diskon = (data.diskon / 100) * data.harga_jual;
			tot = data.harga_jual - diskon;

			harga_brg = tot;
			harga_asli = data.harga_jual;

			if (cari.length > 0) {
				const qty = $('input[data-id="'+data.id_barang+'"]').val();
				const qtyPlus = parseFloat(qty) + 1;
				$('input[data-id="'+data.id_barang+'"]').val(qtyPlus);
				const subtotal = qtyPlus * harga_brg;
				const tes = $(document).find('td[data-kode="'+data.id_barang+'"]').html(toRupiah(subtotal, true));
			}else{
				$('.pengembalian-item').append(
					`
					<tr data-id="${data.id_barang}">
					<input type="hidden" name="id_barang[]" value="${data.id_barang}">
					<td>${data.nama_barang}</td>
					<td>${toRupiah(harga_asli, true)}</td>
					<td>${data.diskon}</td>
					<td><input class="form-control qty" name="jumlah[]" data-id="${data.id_barang}" data-harga="${harga_brg}" type="number" step="0.1" value="1" style="width: 5em"></td>
					<td class="subtotal" data-kode="${data.id_barang}">${toRupiah(harga_brg,true)}</td>
					<td><a class="btn btn-danger btn-flat hapus-barang" data-id="${data.id_barang}" data-harga="${harga_brg}"><i class="fa fa-trash"></i></a></td>
					</tr>
					`
					);	
			}

			let item = JSON.parse(get('pengembalian'));

			// store localstorage
			let pengembalian = {};

			if (item) {
				if (item.jumlahbayar > 0) {
					pengembalian['jumlahbayar'] = parseFloat(item.jumlahbayar ) + parseFloat(harga_brg);
				}else{
					pengembalian['jumlahbayar'] = harga_brg;	
				}
			}else{
				pengembalian['jumlahbayar'] = harga_brg;	
			}

			store('pengembalian', JSON.stringify(pengembalian));

			let itemBaru = JSON.parse(get('pengembalian'));

			$('.jumlah_bayar').val(toRupiah(itemBaru.jumlahbayar));
			$('.total_jumlah_bayar').val(toRupiah(itemBaru.jumlahbayar));

			$('.barcode').focus();
			
		})
	}

	$(document).on('click', '.tambah-barang', function(){
		const golongan = $('.golongan').val()
		const id = $(this).data('id'); 
		tambah_chart(id, golongan);
	})

	$(document).on('click','.hapus-barang', function(e){
		e.preventDefault();
		$(this).closest('tr').remove();
		const id = $(this).data('id');
		const harga = $(this).data('harga');
		const qty = $(this).closest('tr[data-id="'+id+'"]').find('input[data-id="'+id+'"]').val();

		const jumlah1 = JSON.parse(get('pengembalian')).jumlahbayar;

		const jumlah = parseFloat(harga) * parseFloat(qty);

		const jumlah2 = parseFloat(jumlah1) - jumlah;

		let pengembalian = {};
		pengembalian['jumlahbayar'] = jumlah2;
		store('pengembalian', JSON.stringify(pengembalian));
		$('.jumlah_bayar').val(toRupiah(jumlah2));
		$('.total_jumlah_bayar').val(toRupiah(jumlah2));
		$('.barcode').focus();

		
	})

	$(document).on('click', '.batal', function(e){
		e.preventDefault();
		remove('pengembalian');
		$('.pengembalian-item').html('');
		$('.harga').html('Rp. 0');
		$('.kembalian').html('');
		$('.cash').val('');
		$('.kembalian').val('');
		$('.jumlah_bayar').val('Rp. 0');
		$('.total_jumlah_bayar').val('Rp. 0');
		$('.barcode').focus();
	})

	$(document).on('keyup change', '.qty', function(e){
		let jmlBayar = JSON.parse(get('pengembalian')).jumlahbayar;
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
		pengembalian = {};
		pengembalian['jumlahbayar'] = total;
		store('pengembalian', JSON.stringify(pengembalian));

		let baru = JSON.parse(get('pengembalian')).jumlahbayar;
		if (baru == null) {
			baru = 0;
		}
		let rp = toRupiah(baru);
		if (rp == 'Rp. NaN') {
			rp = "Rp. 0";
		}
		$('.jumlah_bayar').val(rp);
		$('.total_jumlah_bayar').val(rp);

	})

	$(document).on('keydown', '.qty', function(e){
		if (e.which == 13) {
			$('.barcode').focus();
			return false;
		}
	})

	$('.diskon').keyup(function(e){
		let diskon = $(this).val();
		if (diskon == '') {
			diskon = 0;
		}

		var jumlahbayar = 0;
		$('.subtotal').each(function(index,el){
			var val = $(this).html().replace('Rp. ', '').replace('.', '').replace('.', '');
			jumlahbayar += parseFloat(val)
		})

		let harga_sikon = (diskon/100) * jumlahbayar;

		if (harga_sikon == 'Nan') {
			harga_sikon = 0;
		}

		let potongan = $('.potongan').val();
		if (potongan == '') {
			potongan = 0;
		}

		let hasilDiskon = toRupiah(parseFloat(jumlahbayar) - parseFloat(harga_sikon) - parseFloat(potongan));
		if (hasilDiskon == 'Rp. NaN') {
			hasilDiskon = "Rp. 0";
		}
		$('.jumlah_bayar').val(hasilDiskon);
	})

	$('.potongan').keyup(function(e){
		let potongan = $(this).val();
		if (potongan == '') {
			potongan = 0;
		}

		var jumlahbayar = 0;
		$('.subtotal').each(function(index,el){
			var val = $(this).html().replace('Rp. ', '').replace('.', '').replace('.', '');
			jumlahbayar += parseFloat(val)
		})

		let diskon = $('.diskon').val();
		if (diskon == '') {
			diskon = 0;
		}
		const harga_sikon = (parseFloat(diskon)/100) * jumlahbayar;

		let hasilDiskon = toRupiah(parseFloat(jumlahbayar) - harga_sikon - parseFloat(potongan));
		if (hasilDiskon == 'Rp. NaN') {
			hasilDiskon = "Rp. 0";
		}
		$('.jumlah_bayar').val(hasilDiskon);
	})

	$('.barcode').keydown(function(e){
		const id = $(this).val();
		const golongan = $('.golongan').val();
		if (e.which == '13') {
			e.preventDefault();
			e.stopPropagation();

			tambah_chart(id, golongan);

			$(this).val('');
			$(this).focus();
		}
	})

	$('.tambah-pelanggan').submit(function(e){
		e.preventDefault();
		$.ajax({
			url : base_url + '/pengembalian/tambah_pelanggan',
			data : $(this).serialize(),
			method : 'post',
			success : function(res){
				const data = JSON.parse(res);
				$('#tambah-pelanggan').modal('hide')
				$('.select2').html('')
				$('.pelanggan_baru').append(`
					<input type="hidden" name="id_pelanggan" value="${data.id_pelanggan}" ?>
					`);
				$('.select2').append(
					`<option disabled style="border:1px solid #d2d6de; padding:6px" selected value="${data.id_pelanggan}" data-select2-id="2">${data.nama_pelanggan}</option>`
					);
			}
		});
	})

	$('.kategori').change(function(){
		let id = $(this).val();
		const golongan = $('.golongan').val()
		if (id == '') {
			id = 'SEMUA'
		}
		$('.barang-kategori').empty()

		$.get(base_url + 'barang/get_barang_by_kategori/' + id + '/' + golongan, function(res){
			const data = JSON.parse(res);
			$(data).each(function(index, el){
				let gambar;
				if (el.gambar == '' || el.gambar == null) {
					gambar = `<img src="${base_url}assets/img/barang/noimage.jpg" alt="barang" class="img-responsive">`;
				}else{
					gambar = `<img src="${base_url}assets/img/barang/${el.gambar}" alt="barang" class="img-responsive">`;
				}
				harga = toRupiah(el.harga_jual);

				$('.barang-kategori').append(
					`
					<tr>
					<td>${gambar}</td>
					<td>${el.nama_pendek}</td>
					<td>${harga}</td>
					<td><button class="btn btn-info tambah-barang" data-id="${el.id_barang}"><i class="fa fa-edit"></i></button></td>
					</tr>
					`
					);
			});
		})
	})

	$('.cari_barang').keyup(function(){
		const name = $(this).val();
		const golongan = $('.golongan').val()
		$('.barang-kategori').empty()

		$.get(base_url + 'barang/get_barang_by_name/' + name + '/' + golongan, function(res){
			const data = JSON.parse(res);
			if (data.length == 0) {
				$('.barang-kategori').html('<h5 class="text-center">Data Tidak Ditemukan</h5>');
			}
			$(data).each(function(index, el){
				let gambar;
				if (el.gambar == '' || el.gambar == null) {
					gambar = `<img src="${base_url}assets/img/barang/noimage.jpg" alt="barang" class="img-responsive">`;
				}else{
					gambar = `<img src="${base_url}assets/img/barang/${el.gambar}" alt="barang" class="img-responsive">`;
				}
				let harga;
				harga = toRupiah(el.harga_jual);
				$('.barang-kategori').append(
					`
					<tr>
					<td>${gambar}</td>
					<td>${el.nama_pendek}</td>
					<td>${harga}</td>
					<td><button class="btn btn-info tambah-barang" data-id="${el.id_barang}"><i class="fa fa-edit"></i></button></td>
					</tr>
					`
					);
			});
		})
	})

	// shortcut

})