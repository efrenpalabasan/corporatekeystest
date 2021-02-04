<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corporate Keys Test</title>
</head>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

<style type="text/css">
    .container{
            color: #000000;
            font-size: 18px;
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
            width: 100%;
    }

    .div-list {
        display: table;      
        margin: auto;  
        width: auto;         
        background-color: #eee;         
        border: 1px solid #666666;         
        border-spacing: 5px;
    }
    .div-row {
        display: table-row;
        width: auto;
        clear: both;
    }
    .div-col {
        float: left;
        display: table-column;         
        width: 200px;         
    }

    .div-row:nth-child(odd) {
        background: #FFFFFF;
    }

    .div-row:nth-child(even) {
        background: #CCCCCC;
    }

    #add_new_item{
        float:right;
        margin:10px;
        color:#000000 !important;
    }

    a {
        text-decoration: none;
    }

    .edit_item{
        color:blue;
    }

    .delete_item{
       color:red;
    }

</style>



<body>
    <div class="container">
        <h2>Items List</h2>

        <div class="div-list">

             <div class="div-row-header">
                <div class="div-col" align="center">Title</div>
                <div  class="div-col">Filename</div>
                <div  class="div-col">Date Added</div>
                <div  class="div-col">Action</div>
             </div>
             <div class="content">
             </div> 
             <div class="add-item">
                    <input id="add_new_item" class="edit_item" type="button" data-id="0" value="Add New Item"/>
             </div>     
            
      </div>
    </div>
</body>

<p><a id="add_item_trigger" href="#add_item_modal" rel="modal:open" style="display:none;">Open Modal</a></p>
<div id="add_item_modal" class="modal">

    <form id="add_item_form" method="POST" action="" enctype="multipart/form-data"> 
        <h2 id="add_item_title"></h2>
        <input type="hidden" id="id"/>
		<p>
            Title :
			<input type="text" id="title" name="title" />
        </p>

        <p id="filename"></p>

        <p id="thumbnail_container">
            Thumbnail :
			<img id="thumbnail">
        </p>

        <p>
            Upload File
			<input type="file" id="fileToUpload" name="fileToUpload" />
		</p>
		<p>
			<input id="save" value="Save" type="button"/>
    	</p>

    </form>
    <button><a id="close_modal" href="#" rel="modal:close">Close window</a></button>    
</div>

<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous"></script>
<!-- jQuery Modal -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

 <script>

    function fetch_items() {  
        var the_url = "/programs/programs.php?action=fetchItems"
        $('.content').html('');

        $.ajax({
            url: the_url,
            dataType: 'json',
            type: 'get',
            success: function(data) {
                for(i = 0; i < data.data.length; i++){
                    var rec = data.data[i];
                    if (rec.id) {
                        $('.content').append("<div class='div-row'> <div class='div-col'>"+rec.title+"</div><div class='div-col'>"+rec.filename+"</div><div class='div-col'>"+rec.date_added+"</div><div class='div-col'><a href='javascript:;' class='edit_item' data-id='"+rec.id+"'>edit</a> | <a href='javascript:;' class='delete_item' data-id='"+rec.id+"' >delete</a></div></div>" );
                    }
                }
            }
        });
    }

    function get_item(id) {  
        var the_url = "/programs/programs.php?action=getItem"

        $.ajax({
            url: the_url,
            dataType: 'json',
            data: { 'id' : id},
            type: 'post',
            success: function(data) {
                var rec = data.data[0];
                $('#id').val(rec.id);
                $('#title').val(rec.title);
                $('#filename').html('File Name : '+rec.filename);
                $('#thumbnail_container').css('display','block');
                $('#filename').css('display','block');
                $('#thumbnail').attr('src','uploads/'+rec.thumbnail);
            }
        });
    }



    $( document ).ready(function() {

        fetch_items(); // fetch records


        $('#save').on('click',function(){ // save item
               var id = $('#id').val(); 
               var title = $('#title').val(); 
               //var file = $('#fileToUpload')[0].files[0]
                
               var fd = new FormData();
               var files = $('#fileToUpload')[0].files;
                
                // Check file selected or not
                if(files.length > 0 || id != ''){
                fd.append('file',files[0]);
                fd.append('id',id);
                fd.append('title',title);

                $.ajax({
                    url: '/programs/programs.php?action=saveItem',
                    dataType: 'json',
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(data){

                        if(data.message == 'ok'){
                            $('#close_modal').trigger('click');
                            fetch_items(); 
                        }else{
                            alert('file not uploaded');
                        }
                    },
                });
                }else{
                     alert("Please select a file.");
                }
        });

    });

    $(document).on('click', '.delete_item', function(e) {
        e.preventDefault(); 
        var id = $(this).data('id');
        if(confirm('Are you sure you want  to delete this item?')){

            var the_url = "/programs/programs.php?action=deleteItem"

             $.ajax({
                   url: the_url,
                   dataType: 'json',
                   data: { 'id' : id},
                   type: 'post',
                    success: function(data) {                 
                        if(data.message == 'ok'){
                            fetch_items();
                        }
                    }
             });
        }


            

    });


    $(document).on('click', '.edit_item', function(e) {
        e.preventDefault(); 

        var id = $(this).data('id');
        
        if(id == '0'){
            $('#id').val('');
            $('#add_item_title').html('Add New Item');
            $('#title').val('');
            $('#filename').css('display','none');
            $('#thumbnail_container').css('display','none');
            
        }else{
            $('#add_item_title').html('Update Item');     
            get_item(id);
        }


       
        $('#add_item_trigger').trigger('click');
                return false;

    });

 </script>
</html>