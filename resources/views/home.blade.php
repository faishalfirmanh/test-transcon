@extends('layout')
@section('title')
    home
@endsection
@section('table_content')


<div style="border: 2px solid black; padding: 20px;margin: 10px;">


    <form style="margin-left: 40px" action="" id="form-all">
        <div>
            <label>No Transaksi</label>
            <input id="no_trans" required  min="1" type="number" style="margin-top:5px;" name="no_trans">
        </div>
        <div>
            <label>Transaction Date</label>
            <input id="date_trans" required type="date" style="margin-top:5px;" name="date_trans">
        </div>
        <div>
            <hr>
        </div>
        <div>
            <button id="add_form" onclick="displayAddForm()">Tambah</button>
        </div>
        <br><br><br>
        <div id="form_empty"></div>
        <template id="form_contains">
            <div class="form_template">
               <div style="width:100px; height:30px;background-color: rgb(214, 145, 145);margin-top:10px;margin-bottom:10px;" onclick="deleteForm(this)">
                    <p style="color: black;font-size:15;text-align:center;">Deleted</p>
               </div>
                <div style="margin-top: 30px;">
                    <label>Item Name</label>
                    <input   required type="text" style="margin-top:5px; margin-left :18px" name="item_name[]">
                </div>
                <div>
                    <label>Quantity</label>
                    <input   required type="number" style="margin-top:5px; margin-left :30px" name="qty[]">
                </div>
                <div>
                    <hr>
                </div>
            </div>
           
        </template>
        <div class="btn-group-modal2">
            <button type="submit">Submit</button>
        </div>
    </form>
</div> 

<div class="tables" style="margin-top:20px;">
    
    <section class="table__body">
        <table id="trans-ajax-list">
            <thead>
                <tr>
                    <th>id trans</th>
                    <th>No Transaksi</th>
                    <th>Total item</th>
                    <th>Total Qty</th>
                    <th>action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </section>
</div>
@endsection


@push('scripts')
    <script>

    $(document).ready(function(){
         $('#trans-ajax-list').DataTable({
            "ajax": {
                "url": "{{ route('all-trans') }}",
                "dataSrc": "data",
                /*response data*/
            },
            "columns": [
                {
                    "data": "id"
                },
                {
                    "data" : "transaction_number"
                },
                {
                    "data": "total_item"
                },
                {
                    "data": "total_qty"
                },
                {
                    render: function(data, type, row) {
                            const cek =`<button onclick="deleteTrans(${row.id})"  style="background:red" title="">delete</button>`;
                            return `<a onclick="" style="margin-right:5px;">edit</a>` +
                                cek;
                        }
                }
            ]
        });
      })

      function deleteTrans(id){
        Swal.fire({
                title: "Yakin ingin menghapus transaksi ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('deleted_trans') }}`,
                        type: 'post',
                        data: {
                            'no_trans': id
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                'Your transacktion has been deleted.',
                                'success'
                            )
                            location.reload();
                        },
                        error: function(err) {
                            console.log('error', err);
                        }
                    })
                }
            })
      } 

        const layout = document.querySelector('#form_empty');
        const template = document.querySelector('#form_contains');

        function deleteForm(el){
            el.closest('.form_template').remove();
            console.log('====================================');
            console.log("hapus");
            console.log('====================================');
        }

        function displayAddForm(){
            const clone = template.content.cloneNode(true);
            layout.appendChild(clone);
        }

        $("#form-all").on("submit", function(e) {
            e.preventDefault();

            const tanggal_trans = $('#date_trans').val();
            const no_trans = $('#no_trans').val();
            
            var item_names = $("input[name='item_name[]']").map(function() {
                return $(this).val();
            }).get();

            var quantitys = $("input[name='qty[]']").map(function() {
                return $(this).val();
            }).get();

           
            var data = {
                transaction_date: tanggal_trans,
                transaction_number: no_trans,
                "item_name[]": item_names,
                "quantity[]": quantitys
            };

            $.ajax({
                url: "{{ route('addtrans') }}",
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.status == 'ok') {
                        Swal.fire(
                                'Add',
                                'Save data transaksi berhasil',
                                'success'
                            ).then((res)=>{
                                // if (res.isConfirmed) {
                                    $('#trans-ajax-list').DataTable().ajax.reload(null, true);
                                //}
                            })
                    }else{
                        if(response.transaction_number){
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal simpan',
                                text: `${response.transaction_number}`,
                            })
                        }
                       
                    }
                },
                error: function(xhr, status, error) {
                    alert("Gagal save");
                }
            });
            
        })
    </script>
@endpush