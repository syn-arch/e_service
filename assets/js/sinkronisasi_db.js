 const data_penjualan = $.parseJSON($.ajax({
 	url:  base_url + 'api/get_penjualan',
 	dataType: "json", 
 	async: false
 }).responseText);

 const data_detail_penjualan = $.parseJSON($.ajax({
 	url:  base_url + 'api/get_detail_penjualan',
 	dataType: "json", 
 	async: false
 }).responseText);

  const data_biaya = $.parseJSON($.ajax({
 	url:  base_url + 'api/get_biaya',
 	dataType: "json", 
 	async: false
 }).responseText);

 const data_detail_biaya = $.parseJSON($.ajax({
 	url:  base_url + 'api/get_detail_biaya',
 	dataType: "json", 
 	async: false
 }).responseText);

 const data_pembayaran = $.parseJSON($.ajax({
 	url:  base_url + 'api/get_pembayaran',
 	dataType: "json", 
 	async: false
 }).responseText);

 const data_petugas = $.parseJSON($.ajax({
 	url:  base_url + 'api/get_petugas',
 	dataType: "json", 
 	async: false
 }).responseText);

 const data_stok_outlet = $.parseJSON($.ajax({
 	url:  base_url + 'api/get_stok_outlet',
 	dataType: "json", 
 	async: false
 }).responseText);

 const data_service = $.parseJSON($.ajax({
 	url:  base_url + 'api/get_service',
 	dataType: "json", 
 	async: false
 }).responseText);

 const data_register = $.parseJSON($.ajax({
 	url:  base_url + 'api/get_register',
 	dataType: "json", 
 	async: false
 }).responseText);

 const data_pelanggan = $.parseJSON($.ajax({
 	url:  base_url + 'api/get_pelanggan',
 	dataType: "json", 
 	async: false
 }).responseText);



 $('.download_data_transaksi').click(function(e){
 	e.preventDefault();
 	swal({
 		title: "Apakah anda yakin?",
 		text: "Data yang disinkronkan tidak bisa dikembalikan! \n pastikan koneksi internet anda stabil",
 		icon: "warning",
 		buttons: true,
 		dangerMode: true,
 	}).then((willDelete) => {
 		if (willDelete) {
 			$.ajax({
 				type : 'POST',
 				url : url_server + '/api/get_data_transaksi',
				timeout: 0, // Infinite
				data : {
					id_outlet : id_outlet
				},
				beforeSend: function(){
					$('.loader_wrapper').html(`<div class="loader"></div>`)
				},
				success: function(respon){

					$.ajax({
						url : base_url + 'api/sync_data_transaksi',
						method : 'POST',
						data : {
							pelanggan : JSON.stringify(respon.pelanggan),
							service : JSON.stringify(respon.service),
							register : JSON.stringify(respon.register),
							penjualan : JSON.stringify(respon.penjualan),
							detail_penjualan : JSON.stringify(respon.detail_penjualan),
							biaya : JSON.stringify(respon.biaya),
							detail_biaya : JSON.stringify(respon.detail_biaya),
							pembayaran : JSON.stringify(respon.pembayaran),
							petugas : JSON.stringify(respon.petugas),
							id_outlet : id_outlet
						}
					})

					$('.loader_wrapper').html(``)

					swal('Berhasil','Sinkronisasi Berhasil', 'success');
				},
				error: function(request, status, err){
					$('.loader_wrapper').html(``)
					swal('Gagal',(status == "timeout") ? "Timeout" : "error: " + err, 'error');
				}
			});
 		}
 	});
 });

 $('.upload_data_transaksi').click(function(e){
 	e.preventDefault();
 	swal({
 		title: "Apakah anda yakin?",
 		text: "Data yang disinkronkan tidak dapat dikembalikan! \n pastikan koneksi internet anda stabil",
 		icon: "warning",
 		buttons: true,
 		dangerMode: true,
 	}).then((willDelete) => {
 		if (willDelete) {
 			$.ajax({
 				type : 'POST',
 				url : url_server + '/api/sync_data_transaksi',
				timeout: 0, // Infinite
				data : {
					id_outlet :id_outlet,
					pelanggan : JSON.stringify(data_pelanggan),
					service : JSON.stringify(data_service),
					register : JSON.stringify(data_register),
					penjualan : JSON.stringify(data_penjualan),
					detail_penjualan : JSON.stringify(data_detail_penjualan),
					biaya : JSON.stringify(data_biaya),
					detail_biaya : JSON.stringify(data_detail_biaya),
					pembayaran : JSON.stringify(data_pembayaran)
				},
				beforeSend: function(){
					$('.loader_wrapper').html(`<div class="loader"></div>`)
				},
				success: function(respon){

					$('.loader_wrapper').html(``)

					swal('Berhasil','Sinkronisasi Berhasil', 'success');
				},
				error: function(request, status, err){
					$('.loader_wrapper').html(``)
					swal('Gagal',(status == "timeout") ? "Timeout" : "error: " + err, 'error');
				}
			});
 		}
 	});
 });

$('.download_data_master').click(function(e){
 	e.preventDefault();
 	swal({
 		title: "Apakah anda yakin?",
 		text: "Data yang disinkronkan tidak dapat dikembalikan! \n pastikan koneksi internet anda stabil",
 		icon: "warning",
 		buttons: true,
 		dangerMode: true,
 	}).then((willDelete) => {
 		if (willDelete) {
 			$.ajax({
		 		type : 'POST',
		 		url : url_server + '/api/get_data_master',
				timeout: 0, // Infinite
				beforeSend: function(){
					$('.loader_wrapper').html(`<div class="loader"></div>`)
				},
				success: function(respon){

					$.ajax({
						url : base_url + 'api/download_data_master',
						method : 'POST',
						data : {
							kategori : JSON.stringify(respon.kategori),
							supplier : JSON.stringify(respon.supplier),
							petugas : JSON.stringify(respon.petugas),
							karyawan : JSON.stringify(respon.karyawan),
							barang : JSON.stringify(respon.barang)
						},
						success : function () {
							$('.loader_wrapper').html(``)

							swal('Berhasil','Sinkronisasi Berhasil', 'success');
						}
					})
				},
				error: function(request, status, err){
					$('.loader_wrapper').html(``)
					swal('Gagal',(status == "timeout") ? "Timeout" : "error: " + err, 'error');
				}
			});	
 		}
 	});
})
 
$('.download_stok').click(function(e){
 	e.preventDefault();
 	 swal({
	 	title: "Apakah anda yakin?",
	 	text: "Data yang disinkronkan tidak dapat dikembalikan! \n pastikan koneksi internet anda stabil",
	 	icon: "warning",
	 	buttons: true,
	 	dangerMode: true,
	 }).then((willDelete) => {
	 	if (willDelete) {
	 		$.ajax({
		 		url : url_server + '/api/get_stok',
		 		method : 'POST',
		 		data : {
		 			id_outlet : id_outlet
		 		},
		 		timeout: 0,
		 		beforeSend: function(){
		 			$('.loader_wrapper').html(`<div class="loader"></div>`)
		 		},
		 		success: function(respon){
		 			$.ajax({
		 				url : base_url + 'api/update_stok',
		 				method : 'POST',
		 				data : { 
		 					id_outlet : id_outlet ,
		 					stok_outlet : JSON.stringify(respon)
		 				}
		 			})
		 			$('.loader_wrapper').html(``)
		 			swal('Berhasil','Update Stok Berhasil!', 'success');
		 		},
		 		error: function(request, status, err){
		 			$('.loader_wrapper').html(``)
		 			swal('Gagal',(status == "timeout") ? "Timeout" : "error: " + err, 'error');
		 		}
		 	});
	 	}
	 });
})