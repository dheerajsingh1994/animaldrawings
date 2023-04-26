<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Upload file</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="col-md-9">
                <form action="d-upload.php" method="post" enctype="multipart/form-data">
                    <h2 style="text-align:center">Upload Files For Animal Drawing</h2>
                    <hr>
                    <label for="file_name">Upload Image and Video:</label>
                    <input type="file" name="uploadfile" id="uploadfile"></br>
                    <label for="rename_file">Asset Name:</label></br>
                    <input type="text" name="rename_file" placeholder="Enter Asset name"></br></br>
                    <label for="thumbnail">Video Thumnail (if uploading vieo):</label></br>
                    <input type="file" name="thumbnail" id="thumbnail"></br></br>
                    
                    <hr>
                    <label for="file_name">Upload Image For GIF:</label>
                    <input type="file" name="files[]" id="uploadfile2" multiple="multiple></br>
                    <label for="rename_file">Asset Name:</label></br>
                    <input type="text" name="renamefile" placeholder="Enter Asset name"></br></br>
                 
                    
                    <input class="btn btn-success" type="submit" name="submit" value="Upload">
                    <p><b>Note:</b><i class='text-danger'>Only .jpg, .jpeg, .gif, .png, .mp4 formats are allowed to a max size of 5 MB.</i> </p>
                </form>
            </div>
            <div class="col-md-3" style="text-align:center;margin-top: 20px">
                <h4>Delete All files over Server</h2>
                <button type="submit" class="btn btn-danger" id="btn-delete-all">Delete All Items</button>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script>

            $(document).ready(function(){
                // $(document).on('click', '#btn-delete-all', function(e) {
                //     e.preventDefault();
                //     deleteAllItems();
                // });
                $(document).on('submit', 'form', function(e) {
                    e.preventDefault();
                    alert('submit form');
                })
            });

            // const deleteAllItems = (reference) => {
            //     // if(confirm('Are you sure you want to delete all items?')) {
            //         // alert('deleted');
            //         $.ajax({
            //             URL: 'delete.php',
            //             type: 'POST',
            //             // cache: false,
            //             // processData: false,
            //             // contentType: "application/json",
            //             success: (res) => {
            //                 console.log(res);
            //                 let res1 = JSON.parse(res);
            //                 console.log(res1);
            //             },
            //             error: (err) => {
            //                 alert('error');
            //                 alert(err);
            //             }

            //         });
            //     // }
            // }
        </script>
    </body>
</html>