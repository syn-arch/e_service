$(function(){

	// reset jumlah bayar
	remove('penjualanitem');

	if (judul == 'Ubah Penjualan') {
		let penjualanitem = {};
		penjualanitem['jumlahbayar'] = total_bayar_rp;	
		store('penjualanitem', JSON.stringify(penjualanitem));
	}
	
	$('.cari_barang').click(function(){
		$("#table-cari-barang").dataTable().fnDestroy()
		$('#table-cari-barang').dataTable({ 
			"processing": true,
			"serverSide": true,
			"order": [],
			"ajax": {
				"url": base_url + "barang/get_harga_barang_json/",
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
			{"data": "stok_q"},
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

	function tambah_chart(id, qty = '1', golongan)
	{
		if (qty == '') {
			qty = 1;
		}

		$.get(base_url + 'barang/get_barang/' + id + '/' + golongan, function(res){
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
			
			const harga_jual = data.harga_jual

			const cari = $(document).find('tr[data-id="'+data.id_barang+'"]');

			if (data.diskon == null) {
				diskon = 0;
			}else{
				diskon = data.diskon;
			}

			_rpdiskon = (diskon / 100) * harga_jual;
			tot = harga_jual - _rpdiskon;

			harga_brg = tot;
			harga_asli = harga_jual;

			let subtot = qty * harga_brg;

			const type_golongan = $('.golongan').val();

			if (cari.length > 0) {
				const quantity = $('input[data-id="'+data.id_barang+'"]').val();
				const qtyPlus = parseFloat(quantity) + parseFloat(qty);
				$('input[data-id="'+data.id_barang+'"]').val(qtyPlus);
				const subtotal = qtyPlus * harga_brg;
				const tes = $(document).find('td[data-kode="'+data.id_barang+'"]').html(toRupiah(subtotal, true));
				if (pengaturan.kunci_penjualan == 1) {
					$('a[data-id="'+data.id_barang+'"]').attr('data-qty', qtyPlus);
				}
			}else{

				let html;

				if (pengaturan.kunci_penjualan == 1) {
					html = 
					`
					<tr data-id="${data.id_barang}">
					<input type="hidden" name="id_barang[]" value="${data.id_barang}">
					<input type="hidden" name="type_golongan[]" value="${type_golongan}">
					<td>${data.nama_pendek}</td>
					<td>${toRupiah(harga_asli, true)}</td>
					<td>${diskon}</td>
					<td><input readonly class="form-control qty" name="jumlah[]" data-id="${data.id_barang}" data-harga="${harga_brg}" type="number" value="${qty}" style="width: 5em"></td>
					<td class="subtotal" data-kode="${data.id_barang}">${toRupiah(subtot,true)}</td>
					<td>
					<a class="btn btn-danger fa fa-trash hapus_kunci_brg" data-type="hapus" data-harga="${harga_brg}" data-qty="${qty}" data-id="${data.id_barang}"></a>
					<a class="btn btn-warning fa fa-edit ubah_kunci_brg" data-type="ubah" data-harga="${harga_brg}" data-qty="${qty}" data-id="${data.id_barang}"></a>
					</td>
					</tr>
					`
				}else{
					html = `
					<tr data-id="${data.id_barang}">
					<input type="hidden" name="id_barang[]" value="${data.id_barang}">
					<input type="hidden" name="type_golongan[]" value="${type_golongan}">
					<td>${data.nama_pendek}</td>
					<td>${toRupiah(harga_asli, true)}</td>
					<td>${diskon}</td>
					<td><input class="form-control qty" name="jumlah[]" data-id="${data.id_barang}" data-harga="${harga_brg}" type="number" value="${qty}" style="width: 5em"></td>
					<td class="subtotal" data-kode="${data.id_barang}">${toRupiah(subtot,true)}</td>
					<td><a class="btn btn-danger btn-flat hapus-barang" data-id="${data.id_barang}" data-harga="${harga_brg}"><i class="fa fa-trash"></i></a></td>
					</tr>
					`;
				}

				$('.penjualan-item').append(html);	
			}

			let item = JSON.parse(get('penjualanitem'));

			// store localstorage
			let penjualanitem = {};

			if (item) {
				if (item.jumlahbayar > 0) {
					penjualanitem['jumlahbayar'] = parseFloat(item.jumlahbayar ) + parseFloat(subtot);
				}else{
					penjualanitem['jumlahbayar'] = subtot;	
				}
			}else{
				penjualanitem['jumlahbayar'] = subtot;	
			}

			store('penjualanitem', JSON.stringify(penjualanitem));

			let itemBaru = JSON.parse(get('penjualanitem'));

			$('.jumlah_bayar').val(toRupiah(itemBaru.jumlahbayar));
			$('.total_jumlah_bayar').val(toRupiah(itemBaru.jumlahbayar));

			$('.qty_brg').val('');
			$('.qty_brg').focus();

			updateKembalian();			
		})
	}

	$(document).on('click','.hapus_kunci_brg',function(){
		$('#input_password').modal('show');
		$('.input_password').focus()
		$('.id_brg').val($(this).data('id'));
		$('.harga_brg').val($(this).data('harga'));
		$('.qty_brg').val($(this).data('qty'));
		$('.ubah_brg').val(0)
	})

	$(document).on('click','.ubah_kunci_brg',function(){
		$('#input_password').modal('show');
		$('.input_password').focus()
		$('.id_brg').val($(this).data('id'));
		$('.harga_brg').val($(this).data('harga'));
		$('.qty_brg').val($(this).data('qty'));
		$('.ubah_brg').val(1)
	})

	function hapus_brg()
	{
		$.ajax({
			method : 'post',
			url : base_url + 'penjualan/verify_password',
			data : {
				password : $('.input_password').val()
			},
			success : function(res){
				if (res == 'false') {
					swal({
						title: "Error!",
						text:  "Password yang anda masukan salah!",
						icon: "error",
						timer : 1500
					});
					$('.input_password').val('')
				}else{

					const id = $('.id_brg').val();
					const harga = $('.harga_brg').val();
					const qty = $('.qty_brg').val();

					$('tr[data-id="'+id+'"]').remove();
					$('.harga').html('Rp. 0');

					const jumlah = parseFloat(harga) * parseFloat(qty);

					const jumlah1 = JSON.parse(get('penjualanitem')).jumlahbayar;
					const jumlah2 = parseFloat(jumlah1) - jumlah;

					let penjualanitem = {};
					penjualanitem['jumlahbayar'] = jumlah2;
					store('penjualanitem', JSON.stringify(penjualanitem));
					$('.jumlah_bayar').val(toRupiah(jumlah2));
					$('.total_jumlah_bayar').val(toRupiah(jumlah2));
					$('.barcode').focus();
					updateKembalian();
					$('.input_password').val('')
				}
			}
		})
	}

	function ubah_brg()
	{
		$.ajax({
			method : 'post',
			url : base_url + 'penjualan/verify_password',
			data : {
				password : $('.input_password').val()
			},
			success : function(res){
				if (res == 'false') {
					swal({
						title: "Error!",
						text:  "Password yang anda masukan salah!",
						icon: "error",
						timer : 1500
					});
					$('.input_password').val('')
				}else{
					const id_brg = $('.id_brg').val();
					$('input[data-id="'+id_brg+'"]').removeAttr('readonly')
					$('.input_password').val('')
				}
			}
		})
	}

	$('.input_password').keyup(function(e){
		const ubah = $('.ubah_brg').val()
		if (e.keyCode == 13) {
			e.preventDefault();
			if (ubah == 0) {
				hapus_brg();
			}else{
				ubah_brg();
			}
			$('#input_password').modal('hide');
		}
	})

	function updateKembalian()
	{
		let item = JSON.parse(get('penjualanitem'));
		const cash = $(document).find('.cash').val();
		if (parseFloat(cash) > 0) {
			const baru = cash - item.jumlahbayar;
			$('.kembalian').val(toRupiah(baru));
		}	
	}

	$('.golongan').change(function(){
		$('#id_kategori').val('pilih');
		$('.barang-kategori').html('');
	})

	$(document).on('click', '.tambah-barang', function(){
		const id = $(this).data('id'); 
		const golongan = $('.golongan').val()
		const qty = $('.qty_brg').val();
		tambah_chart(id, qty, golongan);
	})

	$(document).on('click','.hapus-barang', function(e){
		e.preventDefault();
		$(this).closest('tr').remove();
		$('.harga').html('Rp. 0');
		const id = $(this).data('id');
		const harga = $(this).data('harga');
		const qty = $(this).closest('tr[data-id="'+id+'"]').find('input[data-id="'+id+'"]').val();

		const jumlah = parseFloat(harga) * parseFloat(qty);

		const jumlah1 = JSON.parse(get('penjualanitem')).jumlahbayar;
		const jumlah2 = parseFloat(jumlah1) - jumlah;

		let penjualanitem = {};
		penjualanitem['jumlahbayar'] = jumlah2;
		store('penjualanitem', JSON.stringify(penjualanitem));
		$('.jumlah_bayar').val(toRupiah(jumlah2));
		$('.total_jumlah_bayar').val(toRupiah(jumlah2));
		$('.barcode').focus();
		updateKembalian();
	})

	$(document).on('click', '.batal', function(e){
		e.preventDefault();
		remove('penjualanitem');
		$('.penjualan-item').html('');
		$('.harga').html('Rp. 0');
		$('.kembalian').html('');
		$('.cash').val('');
		$('.kembalian').val('');
		$('.qty_brg').val('');
		$('.jumlah_bayar').val('Rp. 0');
		$('.total_jumlah_bayar').val('Rp. 0');
		$('.barcode').focus();
	})

	function updateTotal()
	{
		let total = 0;
		$(document).find('.subtotal').each(function (index, element) {
			total += parseInt($(element).val().replace('Rp. ', '').replace('.', '').replace('.', ''));
		});
		$('.jumlah_bayar').val(total);
	}

	$('.harga_jasa').keyup(function(e){
		let harga_jasa = $(this).val();
		if (harga_jasa == '') {
			harga_jasa = 0;
		}

		let total = 0;
		$(document).find('.subtotal').each(function (index, element) {
			total += parseFloat($(element).text().replace('.', '').replace('.', '').replace('.', ''));
		});

		let jumlah_bayar = toRupiah(parseFloat(harga_jasa) + parseFloat(total));
		if (jumlah_bayar == 'Rp. NaN') {
			jumlah_bayar = "Rp. 0";
		}
		$('.jumlah_bayar').val(jumlah_bayar);
	})


	$('.cash').keyup(function(e){
		const cash = $(this).val();
		const jumlah = parseFloat($('.jumlah_bayar').val().replace('Rp. ', '').replace('.', '').replace('.', ''));

		const member = $('.member').val();

		jumlahAkhir = jumlah;

		let kembalian = toRupiah(parseFloat(cash) - parseFloat(jumlahAkhir));
		if (kembalian == 'Rp. NaN') {
			kembalian = "Rp. 0";
		}
		$('.kembalian').val(kembalian);
	})

	$(document).on('keyup change', '.qty', function(e){
		let jmlBayar = JSON.parse(get('penjualanitem')).jumlahbayar;
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
		penjualanitem = {};
		penjualanitem['jumlahbayar'] = total;
		store('penjualanitem', JSON.stringify(penjualanitem));

		let baru = JSON.parse(get('penjualanitem')).jumlahbayar;
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

	$('.diskon').keyup(function(e){
		let diskon = $(this).val();
		if (diskon == '') {
			diskon = 0;
		}
		const jumlahbayar = JSON.parse(get('penjualanitem')).jumlahbayar;
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
		const jumlahbayar = JSON.parse(get('penjualanitem')).jumlahbayar;

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
		const qty = $('.qty_brg').val();
		const golongan = $('.golongan').val();
		if (e.which == '13') {
			e.preventDefault();
			e.stopPropagation();
			tambah_chart(id, qty, golongan);
			$(this).val('');
			$(this).focus();
		}
	})

	$('.barcode_pelanggan').keydown(function(e){
		if (e.keyCode == 13) {
			e.preventDefault();
			const barcode = $(this).val();
			$.get(base_url + 'pelanggan/get_pelanggan/' + barcode, function(res){
				if (res == 'null') {
					swal({
						title: "Error!",
						text:  "Pelanggan tidak ditemukan!",
						icon: "error",
						timer : 1500
					});
				}else{
					const data = JSON.parse(res)
					$.get(base_url + 'penjualan/get_limit_pelanggan/' + data.id_pelanggan, function(response){
						if (response == 'true') {
							swal({
								title: "Error!",
								text:  "Pelanggan masih memiliki piutang!",
								icon: "error",
								timer : 1500
							});
						}else{
							$('.pelanggan').val(data.id_pelanggan)
						}
					});
				}
			});
			$(this).val('')
		}
	})

	$('.tambah-pelanggan').submit(function(e){
		e.preventDefault();
		$.ajax({
			url : base_url + '/penjualan/tambah_pelanggan',
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
		if (id == '') {
			id = 'SEMUA'
		}
		$('.barang-kategori').empty()

		$.get(base_url + 'barang/get_barang_by_kategori/' + id , function(res){
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
					<td><button class="btn btn-info tambah-barang" data-id="${el.id_barang}"><i class="fa fa-cart-plus"></i></button></td>
					</tr>
					`
					);
			});
		})
	})

	$('.cari_barang_name').keyup(function(e){
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
					let harga;
					harga = toRupiah(el.harga_jual);
					$('.barang-kategori').append(
						`
						<tr>
						<td>${gambar}</td>
						<td>${el.nama_pendek}</td>
						<td>${harga}</td>
						<td><button class="btn btn-info tambah-barang" data-id="${el.id_barang}"><i class="fa fa-cart-plus"></i></button></td>
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
					harga = toRupiah(el.harga_jual);
					$('.barang-kategori').append(
						`
						<tr>
						<td>${gambar}</td>
						<td>${el.nama_pendek}</td>
						<td>${harga}</td>
						<td><button class="btn btn-info tambah-barang" data-id="${el.id_barang}"><i class="fa fa-cart-plus"></i></button></td>
						</tr>
						`
						);
				});
			})
		}

		

	})


	// shortcut

})