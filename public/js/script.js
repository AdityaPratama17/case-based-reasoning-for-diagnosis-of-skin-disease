// console.log('ok');
$(function() {
    $('[data-toggle="tooltip"]').tooltip();

    $('.tombolRevise').on('click', function() {
        const id = $(this).data('id');
        console.log(id);

        $.ajax({
            url: 'http://localhost/PHP/METODE%20PENALARAN/FINAL%20PROJECT%20-%20dermatology/public/revise/getKasus',
            data: {id : id},
            method: 'post',
            dataType: 'json',
            success: function(data){
                console.log(data);
                $('#id').val(data.id_cb);
                $('#f1').html(data.f1);
                $('#f2').html(data.f2);
                $('#f3').html(data.f3);
                $('#f4').html(data.f4);
                $('#f5').html(data.f5);
                $('#f6').html(data.f6);
                $('#f7').html(data.f7);
                $('#f8').html(data.f8);
                $('#f9').html(data.f9);
                $('#f10').html(data.f10);
                $('#f11').html(data.f11);
                $('#f12').html(data.f12);
                $('#f13').html(data.f13);
                $('#f14').html(data.f14);
                $('#f15').html(data.f15);
                $('#f16').html(data.f16);
                $('#f17').html(data.f17);
                $('#f18').html(data.f18);
                $('#f19').html(data.f19);
                $('#f20').html(data.f20);
                $('#f21').html(data.f21);
                $('#f22').html(data.f22);
                $('#f23').html(data.f23);
                $('#f24').html(data.f24);
                $('#f25').html(data.f25);
                $('#f26').html(data.f26);
                $('#f27').html(data.f27);
                $('#f28').html(data.f28);
                $('#f29').html(data.f29);
                $('#f30').html(data.f30);
                $('#f31').html(data.f31);
                $('#f32').html(data.f32);
                $('#f33').html(data.f33);
                $('#f34').html(data.f34);
                $('#f35').val(data.f35);
            }
        });
    });
});