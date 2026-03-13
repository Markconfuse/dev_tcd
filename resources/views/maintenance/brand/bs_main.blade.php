@extends('layouts.page')

@section('title', 'Brand Settings')

@section('content_header', 'Brand Settings')

@section('css')

@stop

@section('content')

@include('maintenance.modals.mdl_addBrand')
@include('maintenance.modals.mdl_editBrand')

<div class="row">
  <div class="col-1">
  </div>

  <div class="col-10">
    <div class="card">
      <div class="card-header">
        {{-- <h3 class="card-title">Brand Settings</h3> --}}

        <div class="card-tools">
          <button title="Add New Brand" type="button" id="btnAddBrand" class="btn btn-primary btn-sm">
            <i class="fas fa-plus-square"></i>
          </button>

        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body" style="overflow-x:scroll">
        <table id="tblBrands" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Brand Name</th>
              <th>Brand Type</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($_brands as $brand)
            <tr style="text-align:center">
              <td>{{ $brand->brand }}</td>
              <td id="{{ $brand->transaction_type_id }}">
                @if($brand->transaction_type_id == 1)
                  <span class="bg-purple" style="padding:5px;border-radius:.25em">
                @else
                  <span class="bg-primary" style="padding:5px;border-radius:.25em">
                @endif
                  {{ $brand->transaction_description }}
                </span>
              </td>
              <td>
                <button id="{{ $brand->brand_id }}" title="Edit {{ $brand->brand }}" class="btn btn-sm btn-success btnEditBrand"><i class="fas fa-edit"></i></button>
                <button id="{{ $brand->brand_id }}" title="Delete {{ $brand->brand }}" class="btn btn-sm btn-danger btnDeleteBrand"><i class="fas fa-trash-alt"></i></button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <!-- /.card-body -->
    </div>
  </div>

  <div class="col-1">
  </div>
</div>

@stop

@section('js')

{{-- <script src="https://cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script> --}}
<script type="text/javascript">
  $(function () {
    var tblBrand = $('#tblBrands').DataTable({
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });

    $('#tblAddBrand').on('change', '.chkBrand', function(event) {
        if($(this).is(':checked')) {
          $(this).closest('tr').css('background-color','#eeeeee');
        } else {
          $(this).closest('tr').css('background-color','#ffffff');
        }
      })

    $('#btnAddBrand').on('click', function() {
      $('#mdl_addBrand').modal('show');
    })

    $('#btnSaveBrand').on('click', function() {

      $('#tblAddBrand tbody').find('input:text.bName').each(function() {
          if(!$(this).val().trim()) { 
            $(this).addClass('is-invalid');
          } else {
            $(this).removeClass('is-invalid');
          }
      });

      if(countClass('is-invalid') == 0) {
        $('#btnSaveBrand').hide();
        $('#frmAddBrand').submit();
      } else {
        Swal.fire({
          type: 'error',
          title: 'Please fill in all required fields!',
          showConfirmButton: false,
          width: '333px',
          timer: 1300
        })
      }

    })

    $('#btnUpdateBrand').on('click', function() {

      if(!$('#editBrandName').val().trim()) { 
        i = false;
        $('#editBrandName').addClass('is-invalid');
      } else {
        i = true;
        $('#editBrandName').removeClass('is-invalid');
      }

      if(i) {
        $('#btnUpdateBrand').hide();
        $('#frmEditBrand').submit();
      } else {
        Swal.fire({
          type: 'error',
          title: 'Brand Name is required!',
          showConfirmButton: false,
          width: '333px',
          timer: 1300
        })
      }

    })


    $('#btnAddRow').on('click', function() {
        var i = 0;

        var rw = "<tr>";
            rw += "<td><center><input class='chkBrand' type='checkbox'></center></td>";
            rw += "<td><input class='form-control bName' id='mat"+i+"' type='text' name='brandName[]'></td>";
            rw += "<td><select class='form-control' name='brandTypeID[]' id='brandTypeID' style='width: 100%;'>";
            $.each(JSON.parse('<?php echo json_encode($_brandType) ?>'), function(key, val) {
              rw += "<option value='"+val['transaction_type_id']+"'>"+val['transaction_description']+"</option>"
            })
            rw += "</td>";
            rw += "</tr>";

        i += 1;

        $('#tblAddBrand >tbody > tr:nth-child('+ ($('#tblAddBrand >tbody > tr ').length ) +')').after(rw);
    });

    $("#btnRemRow").on('click', function(){
      console.log(countCheckedTr());
        if (countCheckedTr() === 0) {
          Swal.fire({
            type: 'error',
            title: "Please check at least one row!",
            showConfirmButton: false,
            width: '333px',
            timer: 1300
          })
        } else if(countCheckedTr() < countClass('chkBrand')) {
          $('#tblAddBrand tbody').find('input:checkbox.chkBrand').each(function() {
              if($(this).is(":checked")) {
                $(this).parents("tr").remove();
              }
          });
        } else {
           Swal.fire({
            type: 'error',
            title: "You can't delete all the rows!",
            showConfirmButton: false,
            width: '333px',
            timer: 1300
          })
        }

    });

    $('#tblBrands').on('click', '.btnEditBrand', function() {3

      $('#editBrandID').val($(this).attr('id'));
      $('#editBrandName').val($(this).parents('tr').find("td:eq(0)").text());
      $('#editBrandTypeID').val($(this).parents('tr').find("td:eq(1)").attr('id'));

      $('#mdl_editBrand').modal('show');
    })

    $('#tblBrands').on('click','.btnDeleteBrand',function(){
      var bid = $(this).attr('id');

      Swal.fire({
          title: $(this).attr('title')+'?',
          text: "You won't be able to restore this brand!",
          type: 'question',
          width: '350px',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
        if (result.value) {
          window.open('{{ url('delete-brand') }}?bid='+bid, '_self');
        }
      })

  });

    function countCheckedTr()
    {
      var i = 0;
      $('#tblAddBrand tbody').find('input:checkbox.chkBrand').each(function() {
            if($(this).is(":checked")) {
              i += 1;
            }
      });

      return i;
    }

    function countClass(rClass) {
      var i = 0;

      $('.'+rClass).map(function() {
        i += 1;
      }).get();

      return i;
    }



  });

</script>

@stop

