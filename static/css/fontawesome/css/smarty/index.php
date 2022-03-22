<form>
    <p><input type="file" name="file" class="file" required></p>
    <input type="submit" name="submit" class="submit" value="Submit">
</form>
<script src="https://code.jquery.com/jquery-3.5.0.js"></script>
<script type="text/javascript">
    $(function() {
        $('.submit').on('click', function() {
            var file_data = $('.file').prop('files')[0];
            if(file_data != undefined) {
                var form_data = new FormData();                  
                form_data.append('file', file_data);
                $.ajax({
                    type: 'POST',
                    url: 'ajax.php',
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success:function(response) {
                        if(response == 'success') {
                            alert('File uploaded successfully.');
                        } else {
                            alert('Something went wrong. Please try again.');
                        }
  
                        $('.file').val('');
                    }
                });
            }
            return false;
        });
    });
</script>