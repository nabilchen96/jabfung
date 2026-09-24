document.addEventListener('DOMContentLoaded', function () {

    getData();

});


var table = null;


function getData() {

    table = $("#myTable").DataTable({

        ordering: true,

        processing: true,

        searching: true,

        lengthChange: false,

        ajax: {
            url: '/data-angka-kredit',
        },

        columns: [

            {
                render: function (data, type, row, meta) {

                    return meta.row +
                        meta.settings._iDisplayStart + 1;

                }
            },


            {
                data: "name"
            },

            {
                render: function (data, type, row, meta) {

                    return `
                        <b>${row.no_sk}</b> <br>
                        ${row.tgl_sk}

                    `;

                }
            },


            {
                render: function (data, type, row, meta) {

                    return `
                        ${row.tgl_mulai}

                    `;

                }
            },

            {
                render: function (data, type, row, meta) {

                    return `
                       
                        ${row.tgl_selesai}
                    `;

                }
            },


            {
                data: "kredit_utama"
            },


            {
                data: "kredit_penunjang"
            },


            {
                data: "total_kredit"
            },


            {

                render: function (data, type, row, meta) {

                    return `

                        <div class="dropdown">

                            <a class="text-success"
                                href="#"
                                data-toggle="dropdown">

                                <i class="bi bi-three-dots"
                                    style="font-size:1.5rem"></i>

                            </a>


                            <div class="dropdown-menu">

                                <a class="dropdown-item text-success"
                                    data-toggle="modal"
                                    data-target="#modal"
                                    href="javascript:void(0)"
                                    data-bs-id="${row.id}">

                                    <i class="bi bi-grid"></i>
                                    &nbsp; Edit

                                </a>


                                <a class="dropdown-item text-danger"
                                    onclick="hapusData(${row.id})">

                                    <i class="bi bi-trash"></i>
                                    &nbsp; Hapus

                                </a>

                            </div>

                        </div>

                    `;

                }

            }

        ],

    });

}

$('#modal').on('show.bs.modal', function (event) {

    var button = $(event.relatedTarget);

    var recipient = button.data('bs-id');

    var cok = $("#myTable")
        .DataTable()
        .rows()
        .data()
        .toArray();


    let cokData = cok.filter(function (dt) {

        return dt.id == recipient;

    });


    document.getElementById("form").reset();

    document.getElementById('id').value = '';


    if (recipient) {

        // EDIT MODE

        var modal = $(this);

        modal.find('#id')
            .val(cokData[0].id);

        modal.find('#user_id')
            .val(cokData[0].user_id);

        modal.find('#no_sk')
            .val(cokData[0].no_sk);

        modal.find('#tgl_sk')
            .val(cokData[0].tgl_sk);

        modal.find('#tgl_mulai')
            .val(cokData[0].tgl_mulai);

        modal.find('#tgl_selesai')
            .val(cokData[0].tgl_selesai);

        // modal.find('#bulan_mulai')
        //     .val(cokData[0].bulan_mulai);

        // modal.find('#tahun_mulai')
        //     .val(cokData[0].tahun_mulai);

        // modal.find('#bulan_selesai')
        //     .val(cokData[0].bulan_selesai);

        // modal.find('#tahun_selesai')
        //     .val(cokData[0].tahun_selesai);

        modal.find('#kredit_utama')
            .val(cokData[0].kredit_utama);

        modal.find('#kredit_penunjang')
            .val(cokData[0].kredit_penunjang);

        modal.find('#total_kredit')
            .val(cokData[0].total_kredit);

    }

});

form.onsubmit = (e) => {

    let formData = new FormData(form);

    document.getElementById('respon_error').innerHTML = '';

    e.preventDefault();

    document.getElementById("tombol_kirim").disabled = true;


    axios({

        method: 'post',

        url: formData.get('id') == ''
            ? '/store-angka-kredit'
            : '/update-angka-kredit',

        data: formData

    })

        .then(function (res) {

            if (res.data.responCode == 1) {

                Swal.fire({

                    icon: 'success',

                    title: 'Sukses',

                    text: res.data.respon,

                    timer: 3000,

                    showConfirmButton: false

                });


                $("#modal").modal("hide");


                $('#myTable')
                    .DataTable()
                    .clear()
                    .destroy();


                getData();


            } else {

                let respon_error = '';

                Object.entries(res.data.respon)
                    .forEach(([field, messages]) => {

                        messages.forEach(message => {

                            respon_error +=
                                `<li>${message}</li>`;

                        });

                    });


                document.getElementById(
                    'respon_error'
                ).innerHTML = respon_error;

            }


            document.getElementById(
                "tombol_kirim"
            ).disabled = false;

        })

        .catch(function (res) {

            document.getElementById(
                "tombol_kirim"
            ).disabled = false;

            console.log(res);

        });

};

hapusData = (id) => {

    Swal.fire({

        title: "Yakin hapus data?",

        icon: 'warning',

        showCancelButton: true,

        confirmButtonColor: '#d33',

        confirmButtonText: 'Ya',

        cancelButtonColor: '#3085d6',

        cancelButtonText: "Batal"

    }).then((result) => {

        if (result.value) {

            axios.post('/delete-angka-kredit', {

                id: id

            })

                .then((response) => {

                    if (response.data.responCode == 1) {

                        Swal.fire({

                            icon: 'success',

                            title: 'Berhasil',

                            text: response.data.respon,

                            timer: 2000,

                            showConfirmButton: false

                        });


                        $('#myTable')
                            .DataTable()
                            .clear()
                            .destroy();


                        getData();


                    } else {

                        Swal.fire({

                            icon: 'warning',

                            title: 'Gagal...',

                            text: response.data.respon,

                        });

                    }

                }, (error) => {

                    console.log(error);

                });

        }

    });

};